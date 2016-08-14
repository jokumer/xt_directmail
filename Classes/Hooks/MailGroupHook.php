<?php
namespace Jok\XtDirectmail\Hooks;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['getSingleMailGroup'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/MailGroupHooks.php:&Jok\XtDirectmail\Hooks\MailGroupHook';
 * $TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/MailGroupHooks.php:&Jok\XtDirectmail\Hooks\MailGroupHook';
 *.
 * @author JKummer <typo3 et enobe de>
 *
 * @package TYPO3
 * @subpackage xt_directmail
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
        global $TYPO3_DB;
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
                $mailGroup = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('sys_dmail_group', $mailGroup[0]);
            // for additional types
            if ($mailGroup['type'] == 5 && !empty($mailGroup['query'])) {
                $mails = $this->getUniqueMails($mailGroup['query']);   
            }           
        }
        // merge mails with existing list
        if (empty($id_lists['PLAINLIST']))
            $id_lists['PLAINLIST'] = array();
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
        global $TYPO3_DB;
        $mails = array();
        $mail_uniqes = array();
        $res = $TYPO3_DB->sql_query($query);
        // each address
        while ($row = $TYPO3_DB->sql_fetch_assoc($res)) {
            if (!$row['email']) continue;
            if (in_array(strtolower($row['email']), $mail_uniqes)) continue;
            $mails[] = array(
                'email' => $row['email'],
                'name' => $row['name'],
            );
            $mail_uniqes[] = strtolower($row['email']);
        }
        return $mails;
    }
}