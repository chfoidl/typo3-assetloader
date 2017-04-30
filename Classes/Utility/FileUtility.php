<?php

namespace Sethorax\Assetsloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileUtility
{
    /**
     * Reads the contents of a file.
     *
     * @param $path
     * @return NULL|string
     */
    public static function readAbsFileContents($path)
    {
        $absPath = GeneralUtility::getFileAbsFileName($path);

        if (file_exists($absPath)) {
            return file_get_contents($absPath);
        } else {
            return null;
        }
    }
}
