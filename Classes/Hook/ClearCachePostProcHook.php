<?php

namespace Sethorax\Assetloader\Hook;

use Sethorax\Assetloader\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearCachePostProcHook
{
    /**
     * @var TypoScriptUtility|NULL
     */
    protected $typoscriptUtility;

    /**
     * @param TypoScriptUtility|NULL $typoscriptUtility
     */
    public function __construct($typoscriptUtility = null)
    {
        $this->typoscriptUtility = $typoscriptUtility;
    }

    /**
     * Runs all methods for that hook.
     *
     * @return void
     */
    public function clearCacheCommand()
    {
        $this->clearTempDirectory();
        $this->createNewAssets();
    }

    /**
     * Deletes all files in the temp directory.
     *
     * @return void
     */
    protected function clearTempDirectory()
    {
        $tempDir = GeneralUtility::getFileAbsFileName('typo3temp/assetloader');

        if (file_exists($tempDir)) {
            array_map('unlink', glob($tempDir . '/*'));
        }
    }

    /**
     * Recreates all assets.
     *
     * @return void
     */
    protected function createNewAssets()
    {
        if (isset($this->typoscriptUtility)) {
            $renderPostProcessor = new RenderPostProcessorHook($this->typoscriptUtility);
        } else {
            $renderPostProcessor = new RenderPostProcessorHook();
        }

        $renderPostProcessor->createOptimizedAssets();
    }
}
