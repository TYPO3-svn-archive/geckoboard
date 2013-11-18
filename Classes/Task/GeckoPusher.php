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

class Tx_Geckoboard_Task_GeckoPusher extends tx_scheduler_Task {
	/**
	 * @var array
	 */
	protected $configuredWidgets = array();

	/**
	 * @var array
	 */
	protected $bootstrapConfiguration = array(
		'extensionName' => 'Geckoboard',
		'pluginName' => 'Task',
		'controller' => 'Connector',
		'action' => 'push',
		'switchableControllerActions' => array(
			'Connector' => array(
				'1' => 'push',
			)
		),
		'settings' => '=< plugin.tx_geckoboard_task.settings',
		'persistence' => '=< plugin.tx_geckoboard_task.persistence',
	);

	/**
	 * @param array $configuredWidgets
	 * @return void
	 */
	public function setConfiguredWidgets($configuredWidgets) {
		$this->configuredWidgets = $configuredWidgets;
	}

	/**
	 * @return array
	 */
	public function getConfiguredWidgets() {
		return $this->configuredWidgets;
	}

	/**
	 * Function executed from the Scheduler.
	 *
	 * @return	boolean
	 */
	public function execute() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['Geckoboard']['modules'] =
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['Geckoboard']['plugins'];

		$this->bootstrapConfiguration['settings.']['taskId'] = $this->getTaskUid();
		$this->bootstrapConfiguration['settings.']['configuredWidgets'] = $this->getConfiguredWidgets();

		/** @var $bootstrap Tx_Extbase_Core_Bootstrap */
		$bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
		$bootstrap->run('', $this->bootstrapConfiguration);

		return TRUE;
	}
}

?>