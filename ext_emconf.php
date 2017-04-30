<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Assetloader',
    'description' => 'Pagespeed friendly asset loader.',
    'category' => 'frontend',
    'version' => '0.9.0',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => 'typo3temp/assetloader',
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
