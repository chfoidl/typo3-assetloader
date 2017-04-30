<?php

defined('TYPO3_MODE') or die('Access denied.');

/**
 * Add Extension TypoScript
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY , 'Configuration/TypoScript' , 'Assets Loader' );