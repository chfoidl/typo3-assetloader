<?php

namespace Sethorax\Assetloader\Utility;

use MatthiasMullie\Minify;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class MinificationUtility
{
    const MODE_JS = 'js';
    const MODE_CSS = 'css';

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var mixed
     */
    protected $minifier;

    /**
     * Loads the all dependencies if class has not been loaded yet.
     */
    public function __construct()
    {
        if (!class_exists('\MatthiasMullie\Minify\Minify')) {
            $this->loadDependencies();
        }
    }

    /**
     * Sets the mode to js or css and initializes the correct minifier.
     *
     * @param string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        switch (strtolower($mode)) {
            case 'js':
                $this->mode = self::MODE_JS;
                $this->minifier = new Minify\JS();
                break;

            case 'css':
                $this->mode = self::MODE_CSS;
                $this->minifier = new Minify\CSS();
                break;
        }

        return $this;
    }

    /**
     * Adds content to the minifier directly.
     *
     * @param string $content
     * @return $this
     */
    public function addContent($content)
    {
        $this->content .= $content;
        $this->minifier->add($content);

        return $this;
    }

    /**
     * Adds a file to the minifier.
     * Also adds the filename and the last modification time for hash generation.
     *
     * @param string $file
     * @return $this
     */
    public function addFile($file)
    {
        $this->content .= $file . filemtime(GeneralUtility::getFileAbsFileName($file));
        $this->minifier->add(GeneralUtility::getFileAbsFileName($file));

        return $this;
    }

    /**
     * Outputs the minified code as a string.
     *
     * @return string
     */
    public function minifyToString()
    {
        return $this->minifier->minify();
    }

    /**
     * Writes the minfied code to a file and returns the path.
     * The filename contains a md5 hash of the file content.
     *
     * @return NULL|string
     */
    public function minifyToFile()
    {
        $tempDir = GeneralUtility::getFileAbsFileName('typo3temp/assetloader');

        if (!file_exists($tempDir)) {
            shell_exec('mkdir -p ' . $tempDir);
        }

        $contentHash = md5($this->content);
        $savedFile = $tempDir . '/minified-' . $contentHash . '.' . $this->mode;

        if (!file_exists($savedFile)) {
            $this->minifier->minify($savedFile);
        }

        $relPath = PathUtility::getRelativePathTo($savedFile);
        $relPath = substr($relPath, 0, strlen($relPath) - 1);

        return $relPath;
    }

    /**
     * Loads all dependencies of the minifier.
     *
     * @return void
     */
    protected function loadDependencies()
    {
        $depPath = __DIR__ . '/../Vendor';

        require_once $depPath . '/minify/src/Minify.php';
        require_once $depPath . '/minify/src/CSS.php';
        require_once $depPath . '/minify/src/JS.php';
        require_once $depPath . '/minify/src/Exception.php';
        require_once $depPath . '/minify/src/Exceptions/BasicException.php';
        require_once $depPath . '/minify/src/Exceptions/FileImportException.php';
        require_once $depPath . '/minify/src/Exceptions/IOException.php';
        require_once $depPath . '/path-converter/src/ConverterInterface.php';
        require_once $depPath . '/path-converter/src/Converter.php';
    }
}
