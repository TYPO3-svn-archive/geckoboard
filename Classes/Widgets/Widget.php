<?php
/***************************************************************
 *  Copyright notice
 *  (c) 2013 Cynthia Mattingly <typo3@marketing-factory.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * include the autoloader for the external geckoboard classes from CarlosIO
 */
require_once(t3lib_extMgm::extPath('geckoboard') . 'Resources\Private\PHP\autoload.php');

class Tx_Geckoboard_Widgets_Widget {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var Tx_Geckoboard_Widgets_Widget
	 */
	protected $widgetClass;

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @var string
	 */
	protected $apiKey;

	/**
	 * @var array
	 */
	public $error;

	/**
	 * @var array
	 */
	public $validTypes = array(
		'NumberAndSecondaryStat' => array('url' => 'http://www.geckoboard.com/developers/custom-widgets/widget-types/number-and-optional-secondary-stat/'),
		'Text' => array('url' => 'http://www.geckoboard.com/developers/custom-widgets/widget-types/text/'),
	);

	public function __construct() {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geckoboard']);
		$this->apiKey = preg_replace('/[^a-z0-9]/i', '', $confArr['apiKey']);
	}

	/**
	 * @param array $config
	 * @return void
	 */
	public function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return array
	 */
	public function getValidTypes() {
		return $this->validTypes;
	}

	/**
	 * run the push action for the widget
	 *
	 * @return bool
	 */
	public function push() {
		$this->loadWidgetClass();
		$this->getData();

		try {
			$this->pushToGeckoboard($this->loadGeckoboardWidget());
		} catch (\Exception $e) {
			/** @var t3lib_userAuthGroup $beUser */
			$beUser = $GLOBALS['BE_USER'];
			$beUser->simplelog($e->getMessage(), 'geckoboard', 1);
		}
	}

	/**
	 * @return bool
	 */
	public function configIsValid() {
		$isValid = TRUE;
		if (count($this->config) == 0) {
			$isValid = FALSE;
			$this->error[] = 'noWidgetConfig';
		}

		$id = trim($this->config['id']);
		if ($id == '') {
			$isValid = FALSE;
			$this->error[] = 'noWidgetId';
		}

		if (!isset($this->config['type'])) {
			$isValid = FALSE;
			$this->error[] = 'noWidgetType';
		} elseif (!class_exists('CarlosIO\Geckoboard\Widgets\\' . $this->config['type'])) {
			$isValid = FALSE;
			$this->error[] = 'noWidgetClass';
		}

		return $isValid;
	}

	/**
	 * initialize correct widget type
	 *
	 * @return void
	 */
	protected function loadWidgetClass() {
		$parentClass = trim(strrchr(get_class($this), '_'), '_');
		/** @var Tx_Geckoboard_Widgets_Widget widgetClass */
		$this->widgetClass = $this->objectManager->get(
			'Tx_Geckoboard_Widgets_' . $parentClass . '_' . $this->config['type']
		);

		$this->widgetClass->setConfig($this->config);
	}

	/**
	 * the actual sending of the structured widget data to the geckoboard url
	 *
	 * @param CarlosIO\Geckoboard\Widgets\Widget $widget
	 * @return void
	 * @throws Exception
	 */
	protected function pushToGeckoboard($widget) {
		if (get_parent_class($widget) != 'CarlosIO\Geckoboard\Widgets\Widget') {
			throw new \Exception('widget was not successfully loaded');
		}
		$geckoboardClient = new CarlosIO\Geckoboard\Client();
		$geckoboardClient->setApiKey($this->apiKey);
		$geckoboardClient->push($widget);
	}

	/**
	 * load the needed data from the db
	 *
	 * @return void
	 */
	protected function getData() {
		$this->widgetClass->getData();
	}

	/**
	 * load the structured widget data
	 *
	 * @return \CarlosIO\Geckoboard\Widgets\Widget
	 * @throws Exception
	 */
	protected function loadGeckoboardWidget() {
		return $this->widgetClass->loadGeckoboardWidget();
	}
}

?>