<?php

namespace Sethorax\Assetloader\Utility;

class StringUtility
{
    /**
     * Replaces all markers in $replacements with the respective replacement.
     * Returns the replaced string.
     *
     * @param string $string
     * @param array $replacements
     * @return string
     */
    public static function replaceMarker($string, $replacements)
    {
        $replacedString = $string;

        foreach ($replacements as $marker => $replacement) {
            $replacedString = str_replace($marker, $replacement, $replacedString);
        }

        return $replacedString;
    }

    /**
     * Creates a crc32 hash of the given string.
     * All numbers will be replaced with letters.
     *
     * @param $string
     * @return string
     */
    public static function createCharacterOnlyHash($string)
    {
        $hash = hash('crc32', $string, false);

        $hash = str_replace('0', 'a', $hash);
        $hash = str_replace('1', 'b', $hash);
        $hash = str_replace('2', 'c', $hash);
        $hash = str_replace('3', 'd', $hash);
        $hash = str_replace('4', 'e', $hash);
        $hash = str_replace('5', 'f', $hash);
        $hash = str_replace('6', 'g', $hash);
        $hash = str_replace('7', 'h', $hash);
        $hash = str_replace('8', 'i', $hash);
        $hash = str_replace('9', 'j', $hash);

        return $hash;
    }
}
