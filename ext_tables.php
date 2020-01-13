<?php

if (!defined ('TYPO3_MODE')) die ('Access denied.');

/**
 * Extend TCA for direct_mail recipients by adding a custom item to attach own query using hook 'cmd_compileMailGroup'
 * HOOK must be registered for direct_mail modules 2 & 3 (2 = dmail, 3 = recipient_list)
 */
$TCA['sys_dmail_group']['columns']['query'] = [
    'label' => 'LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.query',
    'config' => [
        'type' => 'text',
    ]
];
// Add type item
$TCA['sys_dmail_group']['columns']['type']['config']['items'][] = ['LLL:EXT:xt_directmail/Resources/Private/Language/locallang.xml:sys_dmail_group.type.I.5', '5'];
// Add type/showitem
$TCA['sys_dmail_group']['types']['5']['showitem'] = 'type,title,description,query';