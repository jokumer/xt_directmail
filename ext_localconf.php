<?php

if (!defined ('TYPO3_MODE')) die ('Access denied.');

/**
 * XClass Dmail class to insert hook. See <http://forge.typo3.org/issues/36467>
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DirectMailTeam\DirectMail\Module\Dmail::class] = [
    'className' => \Jokumer\XtDirectmail\Xclass\Dmail::class,
];

/**
 * HOOKS for EXT:direct_mail, cmd_compileMailGroup_postProcess
 *
 * Use custom query for recipient_list via hook 'cmd_compileMailGroup'
 * Get selection of email and name as recipients from raw sql query
 */
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['getSingleMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
