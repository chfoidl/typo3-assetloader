<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Assets Loader',
    'description' => 'Loads CSS and JS asynchronously to increase page performance.',
    'category' => 'frontend',
    'version' => '0.9.0',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => 'typo3temp/assetsloader',
    'modify_tables' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Sethorax',
    'author_email' => 'info@sethorax.com',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ]
    ]
];
