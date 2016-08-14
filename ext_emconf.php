<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Directmail recipients - extends EXT:direct_mail',
    'description' => 'Extended configurations for directmail extension. See README.',
    'category' => '',
    'author' => 'Joerg kummer',
    'author_email' => 'typo3 et enobe dot de',
    'author_company' => 'enobe.de',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'version' => '1.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-7.99.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    'autoload' => array(
        'psr-4' => array(
            'Jok\\XtDirectmail\\' => 'Classes',
        ),
    ),
);
