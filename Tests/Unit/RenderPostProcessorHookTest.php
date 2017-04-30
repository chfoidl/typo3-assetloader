<?php

namespace Sethorax\Assetsloader\Tests\Unit;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Sethorax\Assetsloader\Hook\RenderPostProcessorHook;
use Sethorax\Assetsloader\Utility\TypoScriptUtility;

class RenderPostProcessorHookTest extends UnitTestCase
{
    protected $params;

    protected $defaultPluginConfig;

    protected $typoScriptUtilityMock;

    public function setUp()
    {
        $this->params = [
            "headerData" => [],
            "footerData" => []
        ];

        $this->defaultPluginConfig = [
            'settings.' => [
                'deferredCssLoadingScript' => 'function ###SCRIPTNAME###() { var e = document.createElement("link"); e.rel = "stylesheet"; e.type = "text/css"; e.href = "###CSSFILE###"; document.body.appendChild(e);} if (window.addEventListener) window.addEventListener("load", ###SCRIPTNAME### , false); else if (window.attachEvent) window.attachEvent("onload", ###SCRIPTNAME###); else window.onload = ###SCRIPTNAME###;',
                'WFLLoadingScript' => '(function(d) { var wf = d.createElement(\'script\'), s = d.scripts[0]; wf.src = \'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js\'; wf.async = true; s.parentNode.insertBefore(wf, s); })(document);'
            ]
        ];

        $this->typoScriptUtilityMock = $this->getMockBuilder(TypoScriptUtility::class)
            ->disableOriginalConstructor()
            ->getMock();

        shell_exec('mkdir -p ./.Build/Web/fileadmin');
        shell_exec('cp -R ./Tests/Fixtures/Files/. ./.Build/Web/fileadmin');
    }

    public function tearDown()
    {
        shell_exec('rm -rf ./.Build/Web/fileadmin');
    }

    /**
     * @test
     */
    public function doesNotModifyParamsIfNotConfigured()
    {
        $this->typoScriptUtilityMock->method('getPluginSetup')
            ->willReturn([]);

        $hook = new RenderPostProcessorHook($this->typoScriptUtilityMock);
        $postHookParams = $hook->process($this->params);

        $this->assertEquals(
            $postHookParams,
            $this->params
        );
    }

    /**
     * @test
     */
    public function doesIncludeCssInlineHead()
    {
        $key = 'css';
        $value = 'body { background-color: white; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'includeCSSInline.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            '<style assetsloader-' . $key . '="">' . $value . '</style>',
            $postHookParams['headerData'][0]
        );
    }

    /**
     * @test
     */
    public function doesIncludeMultipleCssInlineHead()
    {
        $key1 = 'css1';
        $value1 = 'body { background-color: white; }';
        $key2 = 'css2';
        $value2 = 'body { font-size: 16px; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'includeCSSInline.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]);

        $this->assertEquals(
            [
                '<style assetsloader-' . $key1 . '="">' . $value1 . '</style>',
                '<style assetsloader-' . $key2 . '="">' . $value2 . '</style>'
            ],
            $postHookParams['headerData']
        );
    }

    /**
     * @test
     */
    public function doesIncludeCssInlineHeadMinified()
    {
        $key = 'css';
        $value = 'body { background-color: white; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'minifyCSS' => '1',
            'includeCSSInline.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            '<style assetsloader-' . $key . '="">body{background-color:white}</style>',
            trim($postHookParams['headerData'][0])
        );
    }

    /**
     * @test
     */
    public function doesIncludeJsInlineHead()
    {
        $key = 'js';
        $value = 'console.log( "Hello World!" );';

        $postHookParams = $this->runRenderPostProcessorHook([
           'includeJSInline.' => [
               $key => $value
           ]
        ]);

        $this->assertEquals(
            $postHookParams['headerData'][0],
            '<script assetsloader-' . $key . '="" type="text/javascript">' . $value . '</script>'
        );
    }

    /**
     * @test
     */
    public function doesIncludeJsInlineHeadMinified()
    {
        $key = 'js';
        $value = 'console.log( "Hello World!" );';

        $postHookParams = $this->runRenderPostProcessorHook([
            'minifyJS' => '1',
            'includeJSInline.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            $postHookParams['headerData'][0],
            '<script assetsloader-' . $key . '="" type="text/javascript">console.log("Hello World!")</script>'
        );
    }

    /**
     * @test
     */
    public function doesIncludeCssInlineFooter()
    {
        $key = 'css';
        $value = 'body { background-color: white; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'includeCSSInlineFooter.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            '<style assetsloader-' . $key . '="">' . $value . '</style>',
            $postHookParams['footerData'][0]
        );
    }

    /**
     * @test
     */
    public function doesIncludeMultipleCssInlineFooter()
    {
        $key1 = 'css1';
        $value1 = 'body { background-color: white; }';
        $key2 = 'css2';
        $value2 = 'body { font-size: 16px; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'includeCSSInlineFooter.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]);

        $this->assertEquals(
            [
                '<style assetsloader-' . $key1 . '="">' . $value1 . '</style>',
                '<style assetsloader-' . $key2 . '="">' . $value2 . '</style>'
            ],
            $postHookParams['footerData']
        );
    }

    /**
     * @test
     */
    public function doesIncludeCssInlineFooterMinified()
    {
        $key = 'css';
        $value = 'body { background-color: white; }';

        $postHookParams = $this->runRenderPostProcessorHook([
            'minifyCSS' => '1',
            'includeCSSInlineFooter.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            '<style assetsloader-' . $key . '="">body{background-color:white}</style>',
            trim($postHookParams['footerData'][0])
        );
    }

    /**
     * @test
     */
    public function doesIncludeJsInlineFooter()
    {
        $key = 'js';
        $value = 'console.log( "Hello World!" );';

        $postHookParams = $this->runRenderPostProcessorHook([
            'includeJSInlineFooter.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            $postHookParams['footerData'][0],
            '<script assetsloader-' . $key . '="" type="text/javascript">' . $value . '</script>'
        );
    }

    /**
     * @test
     */
    public function doesIncludeJsInlineFooterMinified()
    {
        $key = 'js';
        $value = 'console.log( "Hello World!" );';

        $postHookParams = $this->runRenderPostProcessorHook([
            'minifyJS' => '1',
            'includeJSInlineFooter.' => [
                $key => $value
            ]
        ]);

        $this->assertEquals(
            $postHookParams['footerData'][0],
            '<script assetsloader-' . $key . '="" type="text/javascript">console.log("Hello World!")</script>'
        );
    }

    /**
     * @test
     */
    public function doesIncludeCssDeferred()
    {
        $key = 'style1';
        $value = 'fileadmin/style1.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'includeCSSDeferred.' => [
                $key => $value
            ]
        ]));

        $this->assertContains($value, $postHookParams['footerData'][0]);
        $this->assertContains($key, $postHookParams['footerData'][0]);
        $this->assertContains('attachEvent', $postHookParams['footerData'][0]);
    }

    /**
     * @test
     */
    public function doesIncludeMultipleCssDeferred()
    {
        $key1 = 'style1';
        $value1 = 'fileadmin/style1.css';
        $key2 = 'style2';
        $value2 = 'fileadmin/style2.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'includeCSSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]));

        $combinedOutput = '';

        foreach ($postHookParams['footerData'] as $footerData) {
            $combinedOutput .= $footerData;
        }

        $this->assertContains($value1, $combinedOutput);
        $this->assertContains($key1, $combinedOutput);
        $this->assertContains($value2, $combinedOutput);
        $this->assertContains($key2, $combinedOutput);
        $this->assertContains('attachEvent', $combinedOutput);
    }

    /**
     * @test
     */
    public function doesIncludeCssDeferredMinifiedAndConcatenated()
    {
        $key1 = 'style1';
        $value1 = 'fileadmin/style1.css';
        $key2 = 'style2';
        $value2 = 'fileadmin/style2.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'minifyCSS' => '1',
            'concatenateCSS' => '1',
            'includeCSSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]));

        $loadingScript = $postHookParams['footerData'][0];
        $file = substr($loadingScript, strpos($loadingScript, 'e.href = "') + 10);
        $file = substr($file, 0, strpos($file, '"'));

        $filecontents = file_get_contents(__DIR__ . '/../../.Build/Web/typo3temp/' . $file);

        $this->assertContains('assetsloader-concatenated', $loadingScript);
        $this->assertContains('attachEvent', $loadingScript);
        $this->assertContains('body{display:block}body{display:none}', $filecontents);
    }

    /**
     * @test
     */
    public function doesIncludeCssDeferredMinifiedAndConcatenatedWithExclution()
    {
        $key1 = 'style1';
        $value1 = 'fileadmin/style1.css';
        $key2 = 'style2';
        $value2 = 'fileadmin/style2.css';
        $key3 = 'style3';
        $value3 = 'fileadmin/style3.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'minifyCSS' => '1',
            'concatenateCSS' => '1',
            'includeCSSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2,
                $key3 => $value3,
                $key3 . '.' => [
                    'excludeFromConcatenation' => '1'
                ]
            ]
        ]));

        $loadingScript = $postHookParams['footerData'][0];
        $file = substr($loadingScript, strpos($loadingScript, 'e.href = "') + 10);
        $file = substr($file, 0, strpos($file, '"'));

        $filecontents = file_get_contents(__DIR__ . '/../../.Build/Web/typo3temp/' . $file);

        $this->assertCount(2, $postHookParams['footerData']);
        $this->assertContains('assetsloader-concatenated', $loadingScript);
        $this->assertContains('attachEvent', $loadingScript);
        $this->assertContains('body{display:block}body{display:none}', $filecontents);
        $this->assertContains($key3, $postHookParams['footerData'][1]);
    }

    /**
     * @test
     */
    public function doesIncludeJsDeferred()
    {
        $key = 'script1';
        $value = 'fileadmin/script1.js';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'includeJSDeferred.' => [
                $key => $value
            ]
        ]));

        $this->assertContains($value, $postHookParams['footerData'][0]);
        $this->assertContains($key, $postHookParams['footerData'][0]);
    }

    /**
     * @test
     */
    public function doesIncludeMultipleJsDeferred()
    {
        $key1 = 'script1';
        $value1 = 'fileadmin/script1.js';
        $key2 = 'script2';
        $value2 = 'fileadmin/script2.js';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'includeJSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]));

        $combinedOutput = '';

        foreach ($postHookParams['footerData'] as $footerData) {
            $combinedOutput .= $footerData;
        }

        $this->assertContains($value1, $combinedOutput);
        $this->assertContains($key1, $combinedOutput);
        $this->assertContains($value2, $combinedOutput);
        $this->assertContains($key2, $combinedOutput);
    }

    /**
     * @test
     */
    public function doesIncludeJsDeferredMinifiedAndConcatenated()
    {
        $key1 = 'script1';
        $value1 = 'fileadmin/script1.js';
        $key2 = 'script2';
        $value2 = 'fileadmin/script2.js';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'minifyJS' => '1',
            'concatenateJS' => '1',
            'includeJSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2
            ]
        ]));

        $loadingScript = $postHookParams['footerData'][0];
        $file = substr($loadingScript, strpos($loadingScript, 'src="') + 5);
        $file = substr($file, 0, strpos($file, '"'));

        $filecontents = file_get_contents(__DIR__ . '/../../.Build/Web/typo3temp/' . $file);

        $this->assertContains('assetsloader-concatenated', $loadingScript);
        $this->assertContains('(function(){console.log(\'Hello World!\')})();var test=1;console.log(test)', $filecontents);
    }

    /**
     * @test
     */
    public function doesIncludeJsDeferredMinifiedAndConcatenatedWithExclution()
    {
        $key1 = 'script1';
        $value1 = 'fileadmin/script1.js';
        $key2 = 'script2';
        $value2 = 'fileadmin/script2.js';
        $key3 = 'script3';
        $value3 = 'fileadmin/script3.js';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'minifyJS' => '1',
            'concatenateJS' => '1',
            'includeJSDeferred.' => [
                $key1 => $value1,
                $key2 => $value2,
                $key3 => $value3,
                $key3 . '.' => [
                    'excludeFromConcatenation' => '1'
                ]
            ]
        ]));

        $loadingScript = $postHookParams['footerData'][0];
        $file = substr($loadingScript, strpos($loadingScript, 'src="') + 5);
        $file = substr($file, 0, strpos($file, '"'));

        $filecontents = file_get_contents(__DIR__ . '/../../.Build/Web/typo3temp/' . $file);

        $this->assertCount(2, $postHookParams['footerData']);
        $this->assertContains('assetsloader-concatenated', $loadingScript);
        $this->assertContains('(function(){console.log(\'Hello World!\')})();var test=1;console.log(test)', $filecontents);
        $this->assertContains($key3, $postHookParams['footerData'][1]);
    }

    /**
     * @test
     */
    public function doesIncludeGoogleFonts()
    {
        $key = 'roboto';
        $value = 'Roboto:400,500i';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'fontloader.' => [
                'googleFonts.' => [
                    $key => $value
                ]
            ]
        ]));

        $this->assertContains($value, $postHookParams['footerData'][0]);
        $this->assertContains('assetsloader-webfontloader', $postHookParams['footerData'][0]);
        $this->assertNotContains('custom', $postHookParams['footerData'][0]);
        $this->assertNotContains('urls', $postHookParams['footerData'][0]);
    }

    /**
     * @test
     */
    public function doesIncludeCustomFonts()
    {
        $key = 'fa';
        $family = 'FontAwesome';
        $url = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'fontloader.' => [
                'customFonts.' => [
                    'families.' => [
                        $key => $family
                    ],
                    'urls.' => [
                        $key => $url
                    ]
                ]
            ]
        ]));

        $this->assertContains($family, $postHookParams['footerData'][0]);
        $this->assertContains($url, $postHookParams['footerData'][0]);
        $this->assertContains('assetsloader-webfontloader', $postHookParams['footerData'][0]);
        $this->assertNotContains('google ', $postHookParams['footerData'][0]);
    }

    /**
     * @test
     */
    public function doesIncludeGoogleAndCustomFonts()
    {
        $keyCustom = 'fa';
        $keyGoogle = 'roboto';
        $valueGoogle = 'Roboto:300,400i';
        $familyCustom = 'FontAwesome';
        $urlCustom = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';

        $postHookParams = $this->runRenderPostProcessorHook(array_merge($this->defaultPluginConfig, [
            'fontloader.' => [
                'googleFonts.' => [
                    $keyGoogle => $valueGoogle
                ],
                'customFonts.' => [
                    'families.' => [
                        $keyCustom => $familyCustom
                    ],
                    'urls.' => [
                        $keyCustom => $urlCustom
                    ]
                ]
            ]
        ]));

        $this->assertContains($familyCustom, $postHookParams['footerData'][0]);
        $this->assertContains($urlCustom, $postHookParams['footerData'][0]);
        $this->assertContains($valueGoogle, $postHookParams['footerData'][0]);
        $this->assertContains('assetsloader-webfontloader', $postHookParams['footerData'][0]);
        $this->assertContains('google', $postHookParams['footerData'][0]);
        $this->assertContains('custom', $postHookParams['footerData'][0]);
    }


    protected function runRenderPostProcessorHook($pluginSetup)
    {
        $this->typoScriptUtilityMock->method('getPluginSetup')
            ->willReturn($pluginSetup);

        $hook = new RenderPostProcessorHook($this->typoScriptUtilityMock);

        return $hook->process($this->params);
    }
}
