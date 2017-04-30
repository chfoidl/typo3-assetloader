<?php

namespace Sethorax\Assetloader\Page;

class WebfontLoader
{
    /**
     * @var string
     */
    protected $webfontConfig;

    /**
     * Adds the beginning part of the webfontloader config object.
     */
    public function __construct()
    {
        $this->webfontConfig = 'WebFontConfig = {';
    }

    /**
     * Adds all google font families to the webfontloader config object.
     *
     * @param array $families
     * @return $this
     */
    public function addGoogleFonts($families)
    {
        $familiesArray = '';

        $this->webfontConfig .= 'google: { families: [';

        foreach ($families as $family) {
            $familiesArray .= '"' . $family . '",';
        }

        $this->webfontConfig .= substr($familiesArray, 0, strlen($familiesArray) - 1);
        $this->webfontConfig .= ']},';

        return $this;
    }

    /**
     * Adds all custom fonts to the webfontloader config object.
     *
     * @param array $families
     * @param array $urls
     * @return $this
     */
    public function addCustomFonts($families, $urls)
    {
        $familiesArray = '';
        $urlsArray = '';

        $this->webfontConfig .= 'custom: { families: [';

        foreach ($families as $family) {
            $familiesArray .= '"' . $family . '",';
        }

        $this->webfontConfig .= substr($familiesArray, 0, strlen($familiesArray) - 1);
        $this->webfontConfig .= '], urls: [';

        foreach ($urls as $url) {
            $urlsArray .= '"' . $url . '",';
        }

        $this->webfontConfig .= substr($urlsArray, 0, strlen($urlsArray) - 1);
        $this->webfontConfig .= ']},';

        return $this;
    }

    /**
     * Writes the closing bracket for the webfontloader config object.
     *
     * @return $this
     */
    public function finishConfig()
    {
        $this->webfontConfig .= '};';

        return $this;
    }

    /**
     * Adds the loading script to load the webfontloader.
     *
     * @param string $script
     * @return $this
     */
    public function setLoadingScript($script)
    {
        $this->webfontConfig .= $script;

        return $this;
    }

    /**
     * Returns the config.
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->webfontConfig;
    }
}
