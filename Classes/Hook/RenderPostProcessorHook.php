<?php

namespace Sethorax\Assetloader\Hook;

use Sethorax\Assetloader\Page\WebfontLoader;
use Sethorax\Assetloader\Renderer\TagRenderer;
use Sethorax\Assetloader\Utility\FileUtility;
use Sethorax\Assetloader\Utility\MinificationUtility;
use Sethorax\Assetloader\Utility\StringUtility;
use Sethorax\Assetloader\Utility\TypoScriptUtility;

class RenderPostProcessorHook
{
    const POSITION_HEAD = 'headerData';
    const POSITION_FOOTER = 'footerData';

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var \Sethorax\assetloader\Page\WebfontLoader
     */
    protected $webfontLoader;

    /**
     * @param TypoScriptUtility|NULL $typoscriptUtility
     */
    public function __construct(TypoScriptUtility $typoscriptUtility = null)
    {
        if (!isset($typoscriptUtility)) {
            $typoscriptUtility = new TypoScriptUtility();
        }

        $this->settings = $typoscriptUtility->getPluginSetup('tx_assetloader');
    }

    /**
     * @param array $params
     * @return array
     */
    public function process($params)
    {
        $this->params = $params;

        $this->includeCssInlineHead();
        $this->includeCssInlineFooter();
        $this->includeJsInlineHead();
        $this->includeJsInlineFooter();
        $this->includeCssDeferred();
        $this->includeJsDeferred();

        $this->includeGoogleFonts();
        $this->includeCustomFonts();
        $this->addWebfontLoader();

        return $this->params;
    }

    /**
     * Creates minified or concatenated css and js files in temp directory
     *
     * @return void
     */
    public function createOptimizedAssets()
    {
        $this->processFiles('css', $this->settings['includeCSSDeferred.'], $this->settings['concatenateCSS'], $this->settings['minifyCSS']);
        $this->processFiles('js', $this->settings['includeJSDeferred.'], $this->settings['concatenateJS'], $this->settings['minifyJS']);
    }

    /**
     * Includes inline css in the head.
     * @return void
     */
    protected function includeCssInlineHead()
    {
        $this->includeCssInline($this->settings['includeCSSInline.'], self::POSITION_HEAD);
    }

    /**
     * Includes inline css in the footer.
     * @return void
     */
    protected function includeCssInlineFooter()
    {
        $this->includeCssInline($this->settings['includeCSSInlineFooter.'], self::POSITION_FOOTER);
    }

    /**
     * Includes inline js in the head
     * @return void
     */
    protected function includeJsInlineHead()
    {
        $this->includeJsInline($this->settings['includeJSInline.'], self::POSITION_HEAD);
    }

    /**
     * Includes inline js in the footer.
     * @return void
     */
    protected function includeJsInlineFooter()
    {
        $this->includeJsInline($this->settings['includeJSInlineFooter.'], self::POSITION_FOOTER);
    }

    /**
     * Adds inline css to either the head or the footer. If minification or concatenation is enabled
     * all contents will be minified and or concatenated as well.
     * The output will be rendered as a string and added to the specified position.
     *
     * @param array $settings
     * @param string $pos
     * @return void
     */
    protected function includeCssInline($settings, $pos)
    {
        if (isset($settings)) {
            foreach ($settings as $key => $value) {
                $rawCss = $this->getSettingContent($settings, $key, $value);

                if ($this->settings['minifyCSS'] === '1') {
                    $minifier = new MinificationUtility();
                    $css = $minifier->setMode('css')
                        ->addContent($rawCss)
                        ->minifyToString();
                } else {
                    $css = $rawCss;
                }

                if (!empty($css)) {
                    $tag = new TagRenderer();
                    $this->params[$pos][] =
                        trim($tag->create('style')
                            ->setContent($css)
                            ->addAttribute('assetloader-' . $key)
                            ->renderToString());
                }
            }
        }
    }

    /**
     * Adds inline js to either the head or the footer. If minification or concatenation is enabled
     * all contents will be minified and or concatenated as well.
     * The output will be rendered as a string and added to the specified position.
     *
     * @param array $settings
     * @param string $pos
     */
    protected function includeJsInline($settings, $pos)
    {
        if (isset($settings)) {
            foreach ($settings as $key => $value) {
                if (TypoScriptUtility::isDotlessKey($key)) {
                    $rawJs = $this->getSettingContent($settings, $key, $value);

                    if ($this->settings['minifyJS'] === '1') {
                        $minifier = new MinificationUtility();
                        $js = $minifier->setMode('js')
                            ->addContent($rawJs)
                            ->minifyToString();
                    } else {
                        $js = $rawJs;
                    }

                    if (!empty($js)) {
                        $tag = new TagRenderer();
                        $this->params[$pos][] =
                            trim($tag->create('script')
                                ->setContent($js)
                                ->addAttribute('assetloader-' . $key)
                                ->addAttribute('type', 'text/javascript')
                                ->renderToString());
                    }
                }
            }
        }
    }

    /**
     * Includes the specified stylesheets to be loaded deferred.
     * The stylesheets will be minified and or concatenated if it is enabled in typoscript.
     * The stylesheets will be included as a inline script with loads those stylesheets after the page has been loaded.
     * The loading script can be set in typoscript. The markers for the css file path and the name of the loading
     * script will be replaced by the respective value.
     * The loading script will then be added to the footer data.
     *
     * @return void
     */
    protected function includeCssDeferred()
    {
        if (isset($this->settings['includeCSSDeferred.'])) {
            $files = $this->processFiles('css', $this->settings['includeCSSDeferred.'], $this->settings['concatenateCSS'], $this->settings['minifyCSS']);

            foreach ($files as $key => $value) {
                $scriptContent = StringUtility::replaceMarker($this->settings['settings.']['deferredCssLoadingScript'], [
                    '###CSSFILE###' => $value,
                    '###SCRIPTNAME###' => StringUtility::createCharacterOnlyHash($value)
                ]);

                $tag = new TagRenderer();
                $this->params[self::POSITION_FOOTER][] =
                    $tag->create('script')
                        ->setContent($scriptContent)
                        ->addAttribute('assetloader-' . $key)
                        ->addAttribute('type', 'text/javascript')
                        ->renderToString();
            }
        }
    }

    /**
     * Adds specified js files to the footer data. Files will be minified and or concatenated if enabled.
     * Scripts will be included with the async and defer attribute.
     *
     * @return void
     */
    protected function includeJsDeferred()
    {
        if (isset($this->settings['includeJSDeferred.'])) {
            $files = $this->processFiles('js', $this->settings['includeJSDeferred.'], $this->settings['concatenateJS'], $this->settings['minifyJS']);

            foreach ($files as $key => $value) {
                $tag = new TagRenderer();
                $this->params[self::POSITION_FOOTER][] =
                    $tag->create('script')
                        ->addAttribute('assetloader-' . $key)
                        ->addAttribute('src', $value)
                        ->addAttribute('type', 'text/javascript')
                        ->addAttribute('async')
                        ->addAttribute('defer')
                        ->renderToString();
            }
        }
    }

    /**
     * Includes all font families in the web font loader config object.
     * Initializes the WebfontLoader if it has not been initialized yet.
     *
     * @return void
     */
    protected function includeGoogleFonts()
    {
        $families = $this->settings['fontloader.']['googleFonts.'];

        if (isset($families) && $families) {
            if (!isset($this->webfontLoader)) {
                $this->webfontLoader = new WebfontLoader();
            }

            $this->webfontLoader->addGoogleFonts($families);
        }
    }

    /**
     * Includes all custom font families in the webfontloader config object.
     * Initializes the WebfontLoader if it has not been initialized yet.
     *
     * @return void
     */
    protected function includeCustomFonts()
    {
        $families = $this->settings['fontloader.']['customFonts.']['families.'];
        $urls = $this->settings['fontloader.']['customFonts.']['urls.'];

        if (isset($families) && isset($urls) && $families && $urls) {
            if (!isset($this->webfontLoader)) {
                $this->webfontLoader = new WebfontLoader();
            }

            $this->webfontLoader->addCustomFonts($families, $urls);
        }
    }

    /**
     * Adds the webfontloader config object and the wfl loading script if the WebfontLoader has been initialized.
     *
     * @return void
     */
    protected function addWebfontLoader()
    {
        if (isset($this->webfontLoader)) {
            $minifier = new MinificationUtility();
            $minifier->setMode('js')
                ->addContent(
                    $this->webfontLoader->finishConfig()
                        ->setLoadingScript($this->settings['settings.']['WFLLoadingScript'])
                        ->getConfig()
                );

            $tag = new TagRenderer();
            $this->params[self::POSITION_FOOTER][] =
                $tag->create('script')
                    ->setContent($minifier->minifyToString())
                    ->addAttribute('assetloader-webfontloader')
                    ->addAttribute('type', 'text/javascript')
                    ->renderToString();
        }
    }

    /**
     * Processes all files in $settings.
     * If enabled the files will be minified and or concatenated as well.
     * Files wich have excludeFromConcatenation set to 1 will be excluded from concatenation.
     *
     * @param string $type
     * @param array $settings
     * @param string $concatenationSetting
     * @param string $minificationSetting
     * @return array
     */
    protected function processFiles($type, $settings, $concatenationSetting, $minificationSetting)
    {
        $processQueue = [];
        $processedFiles = [];

        if (isset($settings)) {
            foreach ($settings as $key => $value) {
                if (TypoScriptUtility::isDotlessKey($key)) {
                    $additionalConfiguration = $settings[$key . '.'];

                    if ($concatenationSetting === '1' && $additionalConfiguration['excludeFromConcatenation'] !== '1') {
                        $processQueue['concatenated'][$key] = $value;
                    } else {
                        $processQueue[$key][$key] = $value;
                    }
                }
            }

            if ($minificationSetting === '1') {
                foreach ($processQueue as $key => $filesToProcess) {
                    $minifier = new MinificationUtility();
                    $minifier->setMode($type);

                    foreach ($filesToProcess as $file) {
                        $minifier->addFile($file);
                    }

                    $processedFiles[$key] = $minifier->minifyToFile();
                }
            } else {
                foreach ($processQueue as $files) {
                    foreach ($files as $key => $file) {
                        $processedFiles[$key] = $file;
                    }
                }
            }
        }

        return $processedFiles;
    }

    /**
     * Checks if there if the given $key also is available as a dot version.
     * If there is a dot version available and there is 'file' set to '1', this method returns the file contents.
     *
     * @param array $settings
     * @param string $key
     * @param string $value
     * @return string
     */
    protected function getSettingContent($settings, $key, $value)
    {
        $dotConfiguration = $settings[$key . '.'];

        if (isset($dotConfiguration) && $dotConfiguration['file'] === '1') {
            return FileUtility::readAbsFileContents($value);
        } else {
            return $value;
        }
    }
}
