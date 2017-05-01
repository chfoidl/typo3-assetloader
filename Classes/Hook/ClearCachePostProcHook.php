<?php

namespace Sethorax\Assetloader\Hook;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Sethorax\Assetloader\Hook\RenderPostProcessorHook;

class ClearCachePostProcHook
{
    /**
     * @return void
     */
    public function clearCacheCommand()
    {
        $this->clearTempDirectory();
        $this->createNewAssets();
    }

    /**
     * @return void
     */
    protected function clearTempDirectory()
    {
        $tempDir = GeneralUtility::getFileAbsFileName('typo3temp/assetloader');

        if (file_exists($tempDir)) {
            array_map('unlink', glob($tempDir . '/*'));
        }
    }

    protected function createNewAssets()
    {
        $renderPostProcessor = new RenderPostProcessorHook();
        $renderPostProcessor->createOptimizedAssets();
    }
}