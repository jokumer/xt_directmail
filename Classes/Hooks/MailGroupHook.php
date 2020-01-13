<?php
namespace Jokumer\XtDirectmail\Hooks;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * HOOK for EXT:direct_mail
 
 1. cmd_compileMailGroup_postProcess
 
 * Use custom query for recipient_list via hook 'cmd_compileMailGroup'
 * Get selection of email and name as recipients from raw sql query
 * 
 * needs also:
 * # Extend TCA for direct_mail recipients by adding a custom item to attach own query using hook 'cmd_compileMailGroup'
 * $TCA['sys_dmail_group']['columns']['type']['config']['items'][] = array('LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.type.I.5', '5');
 * # HOOK must be registered for direct_mail modules 2 & 3 (2 = dmail, 3 = recipient_list)
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['getSingleMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
 *.
 * @author J. Kummer
 *
 * @package TYPO3
 * @subpackage xt_directmail
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class MailGroupHook {

    /**
     * Hook for cmd_compileMailGroup
     *
            $id_lists = array(
                'PLAINLIST' => array(
                    0 => array(
                        'email' => 'mail@web.de',
                        'name' => 'Testname Name'
                    )
                )
            );
     *
     */
    function cmd_compileMailGroup_postProcess($id_lists, &$parentObject, $mailGroup) {
        /**
         * Do only for custom sys_dmail_group type == 5 (additional type)
         * Here a special solution, because of bug: http://forge.typo3.org/issues/36467
         */
        // mod3: Reciepientlist
        if ($parentObject->MCONF['name'] == 'DirectMailNavFrame_RecipientList') {  // mod3: Reciepientlist - define reciepient mail groups
            if ($mailGroup['type'] == 5 && !empty($mailGroup['query'])) {
                $mails = $this->getUniqueMails($mailGroup['query']);
            }
        }
        // mod2: Direct Mail
        // Since there is called a further method getSingleMailGroup($group_uid), we need this workaround
        if ($parentObject->MCONF['name'] == 'DirectMailNavFrame_DirectMail') {  // mod2: Direct Mail - prepair sending newsletter
            if ($mailGroup[0])
                $mailGroup = BackendUtility::getRecord('sys_dmail_group', $mailGroup[0]);
            // for additional types
            if ($mailGroup['type'] == 5 && !empty($mailGroup['query'])) {
                $mails = $this->getUniqueMails($mailGroup['query']);   
            }           
        }
        // merge mails with existing list
        if (empty($id_lists['PLAINLIST']))
            $id_lists['PLAINLIST'] = [];
        if (!empty($mails))
            $id_lists['PLAINLIST'] = array_merge($id_lists['PLAINLIST'], $mails);
        return $id_lists;
    }
    
    /**
     * Get unique emails from query
     *
     * @param array $query
     * @return array $mails
     */
    private function getUniqueMails($query) {
        $mails = array();
        $mail_uniqes = array();
        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_dmail_group');
        try {
            $rows = $connection->executeQuery($query)->fetchAll();
        } catch (DBALException $e) {
            
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!$row['email']) continue;
                if (in_array(strtolower($row['email']), $mail_uniqes)) continue;
                $mails[] = array(
                    'email' => $row['email'],
                    'name' => $row['name'],
                );
                $mail_uniqes[] = strtolower($row['email']);
            }
        }
        return $mails;
    }
}
