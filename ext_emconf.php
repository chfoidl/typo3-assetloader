<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Assets Loader',
    'description' => 'Loads CSS and JS asynchronously to increase page performance.',
    'category' => 'frontend',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => 'typo3temp/assetsloader',
    'modify_tables' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sethorax',
    'author_email' => 'info@sethorax.com',
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.10-8.7.99',
        ]
    ]
];
