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
    'version' => '3.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.99.99',
            // ckeditor depends on scssphp and we need that too
            'rte_ckeditor' => '12.4.0-12.99.99',
        ],
    ],
];
