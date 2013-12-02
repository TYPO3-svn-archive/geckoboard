<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (t3lib_div::int_from_ver( TYPO3_version ) < 6000000 ) {
	Tx_Extbase_Utility_Extension::configurePlugin(
		$_EXTKEY,
		'Task',
		array(
			'Connector' => 'push',
		)
	);
} else {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
		$_EXTKEY,
		'Task',
		array(
			'Connector' => 'push',
		)
	);
}

	// register information for the image import tasks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_Geckoboard_Task_GeckoPusher'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xml:geckoPusher.name',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xml:geckoPusher.description',
	'additionalFields' => 'Tx_Geckoboard_Task_GeckoPusherAdditionalFields'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['geckoboard']['widgets'][] = 'Tx_Geckoboard_Widgets_PageCount';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['geckoboard']['widgets'][] = 'Tx_Geckoboard_Widgets_LastChangedPages';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['geckoboard']['widgets'][] = 'Tx_Geckoboard_Widgets_LatestNewPages';

?>