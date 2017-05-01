<?php

namespace Sethorax\Assetloader\Tests\Unit;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Sethorax\Assetloader\Hook\ClearCachePostProcHook;
use Sethorax\Assetloader\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearCachePostProcHookTest extends UnitTestCase
{
    protected $tempDir;

    protected $typoScriptUtilityMock;

    public function setUp()
    {
        $this->typoScriptUtilityMock = $this->getMockBuilder(TypoScriptUtility::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tempDir = GeneralUtility::getFileAbsFileName('typo3temp/assetloader');

        shell_exec('mkdir -p ./.Build/Web/fileadmin');
        shell_exec('mkdir -p ' . $this->tempDir);
        shell_exec('cp -R ./Tests/Fixtures/Files/. ./.Build/Web/fileadmin');
    }

    public function tearDown()
    {
        shell_exec('rm -rf ./.Build/Web/fileadmin');
        shell_exec('rm -rf ' . $this->tempDir);
    }

    /**
     * @test
     */
    public function doesRunClearCacheCommandWithoutConfiguration()
    {
        $this->typoScriptUtilityMock->method('getPluginSetup')
            ->willReturn([]);

        $processor = new ClearCachePostProcHook($this->typoScriptUtilityMock);
        $processor->clearCacheCommand();
    }

    /**
     * @test
     */
    public function doesFullyClearTempDirectory()
    {
        file_put_contents($this->tempDir . '/test1.txt', 'content');
        file_put_contents($this->tempDir . '/test2.css', 'content');
        file_put_contents($this->tempDir . '/test3.js', 'content');

        $this->assertEquals(3, count(GeneralUtility::getFilesInDir($this->tempDir)));

        $this->typoScriptUtilityMock->method('getPluginSetup')
            ->willReturn([]);

        $processor = new ClearCachePostProcHook($this->typoScriptUtilityMock);
        $processor->clearCacheCommand();

        $this->assertEquals(0, count(GeneralUtility::getFilesInDir($this->tempDir)));
    }

    /**
     * @test
     */
    public function doesClearTempDirectoryAndRecreateAssets()
    {
        file_put_contents($this->tempDir . '/test1.txt', 'content');
        file_put_contents($this->tempDir . '/test2.css', 'content');
        file_put_contents($this->tempDir . '/test3.js', 'content');

        $this->assertEquals(3, count(GeneralUtility::getFilesInDir($this->tempDir)));

        $this->typoScriptUtilityMock->method('getPluginSetup')
            ->willReturn([
                'minifyCSS' => '1',
                'minifyJS' => '1',
                'concatenateCSS' => '1',
                'concatenateJS' => '1',
                'includeCSSDeferred.' => [
                    'style1' => 'fileadmin/style1.css',
                    'style2' => 'fileadmin/style2.css'
                ],
                'includeJSDeferred.' => [
                    'script' => 'fileadmin/script1.js'
                ],
            ]);

        $processor = new ClearCachePostProcHook($this->typoScriptUtilityMock);
        $processor->clearCacheCommand();

        $this->assertEquals(2, count(GeneralUtility::getFilesInDir($this->tempDir)));
    }
}
