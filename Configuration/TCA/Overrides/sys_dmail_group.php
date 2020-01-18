<?php

defined('TYPO3_MODE') || die('Access denied.');

/**
 * Extend TCA for direct_mail recipients by adding a custom item to attach own query using hook 'cmd_compileMailGroup'
 * HOOK must be registered for direct_mail modules 2 & 3 (2 = dmail, 3 = recipient_list).
 */
// Add type item
$GLOBALS['TCA']['sys_dmail_group']['columns']['type']['config']['items'][] = [
    'LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.type.I.5', '5',
];
// Add type/showitem
$GLOBALS['TCA']['sys_dmail_group']['types']['5']['showitem'] =
    'type,sys_language_uid,title,description,--div--;LLL:EXT:direct_mail/Resources/Private/Language/locallang_tca.xlf:sys_dmail_group.advanced,query';
// Label query field
$GLOBALS['TCA']['sys_dmail_group']['columns']['query']['label'] =
    'LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.query';
// Hide query field for non admins
$GLOBALS['TCA']['sys_dmail_group']['columns']['query']['displayCond'] =
    'HIDE_FOR_NON_ADMINS';
