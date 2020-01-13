<?php
$EM_CONF['xt_directmail'] = [
    'title' => 'Directmail recipients - extends EXT:direct_mail',
    'description' => 'Extended configurations for directmail extension. See README.',
    'category' => '',
    'author' => 'J. Kummer',
    'state' => 'stable',
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
            'directmail' => '5.2.0-6.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Jokumer\\XtDirectmail\\' => 'Classes',
        ],
    ],
];
