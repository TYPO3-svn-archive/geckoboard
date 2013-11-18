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

class Tx_Geckoboard_Controller_ConnectorController extends Tx_Extbase_MVC_Controller_ActionController {
	/**
	 * automatically called from the scheduler task
	 *
	 * @return void
	 */
	public function pushAction() {
		$configuredWidgetList = $this->getConfiguredWidgetList();

		foreach ($configuredWidgetList as $widgetName) {
			$widget = $this->objectManager->get($widgetName);

			/** @var $widget Tx_Geckoboard_Widgets_Widget */
			$widget->setConfig($this->settings['configuredWidgets'][$widgetName]);

			if ($widget->configIsValid($widgetName)) {
				$widget->push();
			} else {
				foreach ($widget->error as $errorKey) {
					/** @var t3lib_userAuthGroup $beUser */
					$beUser = $GLOBALS['BE_USER'];
					$beUser->simplelog($widgetName . ' - ' .$errorKey, 'geckoboard', 1);
				}
			}
		}
	}

	/**
	 * return the intersect of configured and available widgets,
	 * in case the available widget list has been reduced
	 *
	 * @return array
	 */
	protected function getConfiguredWidgetList() {
		$availableWidgetList = (array) $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['geckoboard']['widgets'];
		$configuredWidgetList = array_intersect($availableWidgetList, array_keys($this->settings['configuredWidgets']));

		return $configuredWidgetList;
	}
}

?>