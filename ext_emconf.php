<?php

########################################################################
# Extension Manager/Repository config file for ext: "geckoboard"
#
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Geckoboard Connect',
	'description' => 'Creates Geckoboard widgets with data from TYPO3',
	'category' => 'misc',
	'author' => 'Cynthia Mattingly',
	'author_email' => 'typo3@marketing-factory.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'Marketing Factory Consulting GmbH',
	'version' => '1.0.0',
	'_md5_values_when_last_written' => 'a:16:{s:9:"ChangeLog";s:4:"20f2";s:10:"README.txt";s:4:"5c92";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"a9f4";s:14:"ext_tables.php";s:4:"e23f";s:14:"ext_tables.sql";s:4:"c852";s:34:"icon_tx_mfcdownloadimages_main.gif";s:4:"475a";s:16:"locallang_db.php";s:4:"1f58";s:7:"tca.php";s:4:"3edd";s:25:"user_mfc_katadyn_hint.php";s:4:"295e";s:19:"doc/wizard_form.dat";s:4:"586f";s:20:"doc/wizard_form.html";s:4:"3699";s:38:"pi1/class.tx_mfcdownloadimages_pi1.php";s:4:"26cf";s:17:"pi1/locallang.php";s:4:"e2f9";s:24:"pi1/static/editorcfg.txt";s:4:"beb4";s:16:"res/template.htm";s:4:"374e";}',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-4.5.99',
			'php' => '5.3.0-0.0.0',
			'scheduler' => ''
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(),
);

?>