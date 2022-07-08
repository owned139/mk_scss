<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "mk_scss"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'SCSS Compiler',
    'description' => 'Compiles SCSS files on the fly to CSS via PHP',
    'category' => 'fe',
    'author' => 'Michell Kalb',
    'author_email' => 'michell-kalb@t-online.de',
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => false,
    'version' => '2.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.99.99',
        ],
    ],
];
