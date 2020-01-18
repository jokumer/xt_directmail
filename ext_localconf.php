<?php

defined('TYPO3_MODE') || die('Access denied.');

/**
 * HOOKS for EXT:direct_mail, cmd_compileMailGroup_postProcess.
 *
 * Use custom query for recipient_list via hook 'cmd_compileMailGroup'
 * Get selection of email and name as recipients from raw sql query
 */
//$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['getSingleMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod2']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
$TYPO3_CONF_VARS['EXTCONF']['direct_mail']['mod3']['cmd_compileMailGroup'][] = \Jokumer\XtDirectmail\Hooks\MailGroupHook::class;
