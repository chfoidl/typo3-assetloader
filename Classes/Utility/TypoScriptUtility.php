<?php

namespace Sethorax\Assetsloader\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class TypoScriptUtility
{
    /**
     * @var array
     */
    protected $typoscriptSetup;

    /**
     * Gets the typoscript setup.
     */
    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
        $this->typoscriptSetup = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
    }

    /**
     * Returns the typoscript setup array for the given extension.
     *
     * @param string $extKey
     * @return mixed
     */
    public function getPluginSetup($extKey = '')
    {
        if (empty($extKey)) {
            return $this->typoscriptSetup['plugin.'];
        } else {
            return $this->typoscriptSetup['plugin.'][$extKey . '.'];
        }
    }

    /**
     * Returns the given setting value at the dotless key.
     *
     * @param array $settings
     * @param string $key
     * @return mixed
     */
    public static function getDotlessSetting($settings, $key)
    {
        return $settings[substr($key, 0, strlen($key) - 1)];
    }

    /**
     * Checks if the key contains a dot.
     *
     * @param string $key
     * @return bool
     */
    public static function isDotlessKey($key)
    {
        return !strpos($key, '.');
    }

    /**
     * Creates an array without the dots from typoscript settings.
     * If $value is an array it will clean the dots as well.
     *
     * @param $settings
     * @return array
     */
    public static function removeDotsFromKeys($settings)
    {
        $conf = [];

        if (is_array($settings)) {
            foreach ($settings as $key => $value) {
                $conf[self::removeDotFromString($key)] = is_array($value) ? self::removeDotsFromKeys($value) : $value;
            }
        }

        return $conf;
    }

    /**
     * Removes a dot at the end of the given $string.
     *
     * @param $string
     * @return mixed
     */
    private static function removeDotFromString($string)
    {
        return preg_replace('/\.$/', '', $string);
    }
}
