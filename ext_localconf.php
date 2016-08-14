<?php

if (!defined ('TYPO3_MODE')) die ('Access denied.');

/**
 * XClass Dmail class to insert hook. See <http://forge.typo3.org/issues/36467>
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['DirectMailTeam\\DirectMail\\Module\\Dmail'] = array(
    'className' => 'Jok\\XtDirectmail\\Xclass\\Dmail',
);

/**
 * HOOKS for EXT:direct_mail, cmd_compileMailGroup_postProcess
 *
 * Use custom query for recipient_list via hook 'cmd_compileMailGroup'
 * Get selection of email and name as recipients from raw sql query
 */
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['getSingleMailGroup'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/MailGroupHook.php:&Jok\XtDirectmail\Hooks\MailGroupHook';
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = 'EXT:' . $_EXTKEY . '/Classes/Hooks/MailGroupHook.php:&Jok\XtDirectmail\Hooks\MailGroupHook';
