<?php

defined('TYPO3_MODE') or die('Access denied.');

/* Register hooks */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
    \Sethorax\Assetloader\Hook\ClearCachePostProcHook::class . '->clearCacheCommand';

if (TYPO3_MODE === 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess'][$_EXTKEY] =
        \Sethorax\Assetloader\Hook\RenderPostProcessorHook::class . '->process';
}
