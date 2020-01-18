<?php

namespace Jokumer\XtDirectmail\Hooks;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HOOK for EXT:direct_mail.
 *
 * 1. cmd_compileMailGroup_postProcess
 * Use custom query for recipient_list via hook 'cmd_compileMailGroup'
 * Get selection of email and name as recipients from raw sql query
 *
 * needs also:
 * # Extend TCA for direct_mail recipients by adding a custom item to attach own query using hook 'cmd_compileMailGroup'
 * $TCA['sys_dmail_group']['columns']['type']['config']['items'][] = array('LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.type.I.5', '5');
 * # HOOK must be registered for direct_mail modules 2 & 3 (2 = dmail, 3 = recipient_list)
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
 *.
 *
 * @author J. Kummer
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MailGroupHook
{
    /**
     * Hook for cmd_compileMailGroup
     * Only for custom sys_dmail_group type == 5 (additional type).
     *
     * $id_lists = array(
     *     'PLAINLIST' => array(
     *         0 => array(
     *             'email' => 'mail@web.de',
     *             'name' => 'Testname Name'
     *         )
     *     )
     * );
     *
     * Param mailGroup is array, but:
     * - mod2 (Direct Mail) array of group-uid's
     * - mod3 (Recipientlist) array of rows from one single group
     *
     * @param $id_lists
     * @param $parentObject
     * @param $mailGroup
     *
     * @return mixed
     */
    public function cmd_compileMailGroup_postProcess($id_lists, &$parentObject, $mailGroup)
    {
        // Get mail addresses for mod2: Direct Mail - prepair sending newsletter
        if ($parentObject->MCONF['name'] == 'DirectMailNavFrame_DirectMail') {
            /**
             * Step 'send_mass' fetches single groups only
             * Step 'send_mail_final' fetches all groups in one request as array with uid's of each group.
             */
            if ($parentObject->CMD === 'send_mass' || $parentObject->CMD === 'send_mail_final') {
                if (is_array($mailGroup) && count($mailGroup) >= 1) {
                    foreach ($mailGroup as $groupId) {
                        $group = BackendUtility::getRecord('sys_dmail_group', $groupId);
                        // for additional types
                        if ($group['type'] == 5 && !empty($group['query'])) {
                            $mailAddresses = $this->getUniqueMailsBySqlQuery($group['query']);
                        }
                    }
                }
            }
        }
        // Get mail addresses for mod3: Reciepientlist - define reciepient mail groups
        if ($parentObject->MCONF['name'] == 'DirectMailNavFrame_RecipientList') {
            if ($mailGroup['type'] == 5 && !empty($mailGroup['query'])) {
                $mailAddresses = $this->getUniqueMailsBySqlQuery($mailGroup['query']);
            }
        }
        // Merge all mail addresses with existing list
        if (empty($id_lists['PLAINLIST'])) {
            $id_lists['PLAINLIST'] = [];
        }
        if (!empty($mailAddresses)) {
            $id_lists['PLAINLIST'] = array_merge($id_lists['PLAINLIST'], $mailAddresses);
        }
        $id_lists['PLAINLIST'] = $this->cleanPlainList($id_lists['PLAINLIST']);

        return $id_lists;
    }

    /**
     * Get unique emails by raw SQL query.
     *
     * @param array $query
     *
     * @return array $mails
     */
    private function getUniqueMailsBySqlQuery($query)
    {
        $mails = [];
        $mail_uniqes = [];
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_dmail_group');

        try {
            $rows = $connection->executeQuery($query)->fetchAll();
        } catch (DBALException $e) {
            //throw new \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\SqlErrorException($e->getPrevious()->getMessage(), 1579204549, $e);
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!$row['email']) {
                    continue;
                }
                if (in_array(strtolower($row['email']), $mail_uniqes)) {
                    continue;
                }
                $mails[] = [
                    'email' => $row['email'],
                    'name'  => $row['name'],
                ];
                $mail_uniqes[] = strtolower($row['email']);
            }
        }

        return $mails;
    }

    /**
     * Remove double record in an array
     * This is a copy of DirectMailUtility::cleanPlainList
     * Which is not not exactly enough, when email is doublicate but name differs!
     *
     * @param array $plainList Email of the recipient
     *
     * @return array Cleaned array
     */
    private function cleanPlainList(array $plainList)
    {
        /**
         * $plainlist is a multidimensional array.
         * this method only remove if a value has the same array
         * $plainlist = array(
         * 		0 => array(
         * 				name => '',
         * 				email => '',
         * 			),
         * 		1 => array(
         * 				name => '',
         * 				email => '',
         * 			),.
         *
         * );
         */
        $taken = [];
        foreach ($plainList as $key => $item) {
            if (!in_array($item['email'], $taken)) {
                $taken[] = $item['email'];
            } else {
                unset($plainList[$key]);
            }
        }

        return $plainList;
    }
}
