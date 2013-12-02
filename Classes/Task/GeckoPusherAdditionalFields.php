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
 * Additional fields provider class for usage with the Scheduler's sleep task
 *
 * @author Cynthia Mattingly <cm@marketing-factory.de>
 * @package TYPO3
 * @subpackage geckoboard
 */
class Tx_Geckoboard_Task_GeckoPusherAdditionalFields implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * @var Tx_Geckoboard_Task_GeckoPusher
	 */
	protected $task;

	/**
	 * @var int
	 */
	protected $uid = 0;

	/**
	 * @var array
	 */
	protected $currentWidgetList = array();

	/**
	 * @var array
	 */
	protected $currentWidgetConfig = array();

	/**
	 * @var array
	 */
	protected $availableWidgetList = array();

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * This method is used to define new fields for adding or editing a task
	 *
	 * @param array $taskInfo reference to the array containing the info
	 * @param Tx_Geckoboard_Task_GeckoPusher $task reference to the current task
	 * @param tx_scheduler_Module $parentObject reference to the calling object
	 * @return array containing all the information
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
		$this->task = & $task;
		$this->init($parentObject, $taskInfo);

		return array_merge($this->getGeneralAdditionalFields(), $this->getConfiguredWidgetAdditionalFields());
	}

	/**
	 * This method checks any additional data that is relevant to the specific task
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param array $submittedData reference to the array containing the data
	 * @param tx_scheduler_Module $parentObject reference to the calling object
	 * @return boolean True if validation was ok
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		/** @var $language language */
		$this->language = & $GLOBALS['LANG'];

		$dataToCheck = $submittedData[intval($submittedData['uid'])];

		if (count($dataToCheck['configuredWidgets']) == 0) {
			$this->displayFlashMessage(
				$this->language->sL(
					'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:error.chooseAWidget'
				), ''
			);

			return FALSE;
		}

		return $this->validateConfiguredWidgets($dataToCheck);
	}

	/**
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData array containing the data submitted by the user
	 * @param tx_scheduler_Task|Tx_Geckoboard_Task_GeckoPusher $task current task
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$data = $submittedData[intval($submittedData['uid'])];
		$task->setConfiguredWidgets($this->formatDataForSaving($data));
	}

	/**
	 * Add additional fields that are not conditional on already chosen fields
	 *
	 * @return array
	 */
	protected function getGeneralAdditionalFields() {
		$additionalFields = array();

		$fieldId = 'configuredWidgets';
		$count = (count($this->availableWidgetList) <= 10 ?
			count($this->availableWidgetList) :
			10);
		$fieldCode = $this->getSelect($fieldId, '', $this->availableWidgetList, $this->currentWidgetList, TRUE, $count);

		$additionalFields[$fieldId] = array(
			'code' => $fieldCode,
			'label' => 'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.configuredWidgets',
			'cshKey' => '_MOD_tools_txgeckoboardM1',
			'cshLabel' => 'task_' . $fieldId
		);

		return $additionalFields;
	}

	/**
	 * Add additional fields that are conditional on already chosen fields
	 *
	 * @return array
	 */
	protected function getConfiguredWidgetAdditionalFields() {
		if (count($this->currentWidgetList) == 0) {
			return array();
		}

		$additionalFields = array();
		foreach ($this->availableWidgetList as $realName => $displayName) {
			if (isset($this->currentWidgetConfig[$realName])) {
				$additionalFields = array_merge(
					$additionalFields, $this->getWidgetAdditionalFields($realName, $displayName)
				);
			}
		}

		return $additionalFields;
	}

	/**
	 * Put the additional fields together
	 *
	 * @param string $realName
	 * @param string $displayName
	 * @return array
	 */
	protected function getWidgetAdditionalFields($realName, $displayName) {
		$widgetConfig = $this->currentWidgetConfig[$realName];

		$additionalFields = $this->buildNameDisplayField($realName, $displayName);
		$additionalFields = array_merge($additionalFields, $this->buildIdField($realName, $widgetConfig));
		$additionalFields = array_merge($additionalFields, $this->buildTypeField($realName, $widgetConfig));

		return $additionalFields;
	}

	/**
	 * Build an input field
	 *
	 * @param string $fieldName
	 * @param string $subField
	 * @param string $current
	 * @return string
	 */
	protected function getInputField($fieldName, $subField = '', $current = '') {
		$subField = ($subField == '' ?
			'' :
			'[' . $subField . ']');
		$result = '<input name="tx_scheduler[' . $this->uid . '][' . $fieldName . ']' . $subField . '" value="' . htmlspecialchars(
				$current
			) . '" size="40">';

		return $result;
	}

	/**
	 * Fetches the uid of the task or defaults to 0
	 *
	 * @return void
	 */
	protected function fetchTaskUid() {
		$this->uid = (is_object($this->task) ?
			$this->task->getTaskUid() :
			0);
	}

	/**
	 * Get the valid types for this widget
	 *
	 * @param Tx_Geckoboard_Widgets_Widget $widget
	 * @return array
	 */
	protected function getValidTypes(Tx_Geckoboard_Widgets_Widget $widget) {
		$typeList = $widget->getValidTypes();
		$validTypes = array();
		foreach ($typeList as $type => $typeInfo) {
			$label = $this->language->sL(
				'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.' . $type
			);

			if ($typeInfo['url'] != '') {
				$label .= ' <a target="_blank" href="' . $typeInfo['url'] . '">(?)</a>';
			}

			$validTypes[$type] = ($label != '' ?
				$label :
				$type);
		}

		return $validTypes;
	}

	/**
	 * Get which widgets are configured for the extension
	 *
	 * @return array list of widget names
	 */
	protected function getAvailableWidgetList() {
		$widgets = (array)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['geckoboard']['widgets'];

		$availableWidgets = array();
		foreach ($widgets as $widget) {
			//todo: check if there are extension dependencies for the widget
			$availableWidgets[$widget] = $this->language->sL(
				'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.' . $widget
			);

			if ($availableWidgets[$widget] == '') {
				$availableWidgets[$widget] = $widget;
			}
		}

		return $availableWidgets;
	}

	/**
	 * Build a select field
	 *
	 * @param string $fieldName
	 * @param string $subField
	 * @param array $values
	 * @param array $current
	 * @param boolean $multiple
	 * @param int $size
	 * @return string
	 */
	protected function getSelect($fieldName, $subField = '', array $values, array $current, $multiple = FALSE, $size = 1) {
		$subField = ($subField == '' ?
			'' :
			'[' . $subField . ']');

		$selectBox = '<select name="tx_scheduler[' . $this->uid . '][' . $fieldName . ']' . $subField . ($multiple ?
				'[]' :
				'') . '" id="task_' . $fieldName . '"' . ($multiple ?
				' multiple="multiple"' :
				'') . ' size="' . $size . '">';

		foreach ($values as $value => $label) {
			$selected = (in_array($value, $current) || count($values) == 1 ?
				' selected="selected"' :
				'');
			$selectBox .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
		}

		$selectBox .= '</select>';

		return $selectBox;
	}

	/**
	 * Build a set of radio buttons
	 *
	 * @param string $fieldName
	 * @param string $subField
	 * @param array $values
	 * @param array $current
	 * @return string
	 */
	protected function getRadioButtons($fieldName, $subField = '', array $values, array $current) {
		$subField = ($subField == '' ?
			'' :
			'[' . $subField . ']');

		$radioButtons = '';

		foreach ($values as $value => $label) {
			$selected = (in_array($value, $current) || count($values) == 1 ?
				' checked="checked"' :
				'');
			$radioButtons .= '<input ' . $selected . ' name="tx_scheduler[' . $this->uid . '][' . $fieldName . ']' . $subField . '" type="radio" value="' . $value . '">' . $label . '<br>';
		}

		return $radioButtons;
	}

	/**
	 * Put the data in a nice associative array
	 *
	 * @param array $data
	 * @return array
	 */
	protected function formatDataForSaving($data) {
		$widgets = array();
		foreach ($data['configuredWidgets'] as $widgetType) {
			$widgets[$widgetType]['id'] = $data[$widgetType]['id'];
			$widgets[$widgetType]['type'] = $data[$widgetType]['type'];
		}

		return $widgets;
	}

	/**
	 * stolen to enable autoloading here too
	 *
	 * @return void
	 */
	protected function initializeClassLoader() {
		if (t3lib_div::int_from_ver( TYPO3_version ) < 6000000 ) {
			if (!class_exists('Tx_Extbase_Utility_ClassLoader', FALSE)) {
				require(t3lib_extmgm::extPath('extbase') . 'Classes/Utility/ClassLoader.php');
			}

			$classLoader = new Tx_Extbase_Utility_ClassLoader();
			spl_autoload_register(
				array(
					$classLoader,
					'loadClass'
				)
			);
		}
	}

	/**
	 * load the current status of which widgets and widget config the user has entered
	 *
	 * @param $mode
	 * @param $taskInfo
	 * @return void
	 */
	protected function getCurrentWidgetConfig($mode, $taskInfo) {
		$configuredWidgets = $taskInfo[$this->uid]['configuredWidgets'];

		if ($mode == 'edit' && !isset($taskInfo[$this->uid])) {
			//load what has already been saved
			$this->currentWidgetConfig = $this->task->getConfiguredWidgets();
		} else {
			//load what has been entered but not saved
			foreach ($configuredWidgets as $widgetName) {
				$this->currentWidgetConfig[$widgetName] = (array) $taskInfo[$this->uid][$widgetName];
			}
		}
	}

	/**
	 * Setup everything we'll need to output the fields
	 *
	 * @param tx_scheduler_Module $parentObject
	 * @return void
	 */
	protected function init(tx_scheduler_Module $parentObject, array $taskInfo) {
		/** @var $language language */
		$this->language = & $GLOBALS['LANG'];

		$this->initializeClassLoader();
		$this->fetchTaskUid();
		$this->getCurrentWidgetConfig($parentObject->CMD, $taskInfo);

		$this->currentWidgetList = array_keys($this->currentWidgetConfig);
		$this->availableWidgetList = $this->getAvailableWidgetList();
	}

	/**
	 * to show a nice grouping of the backend config fields we show the widget name as h3
	 *
	 * @param $realName
	 * @param $displayName
	 * @return array
	 */
	protected function buildNameDisplayField($realName, $displayName) {
		$additionalFields = array();
		$fieldId = $realName . '_fake';
		$additionalFields[$fieldId] = array(
			'code' => '<input type="hidden">',
			'label' => '<h3>' . $displayName . '</h3>',
			'cshKey' => '_MOD_tools_txgeckoboardM1',
			'cshLabel' => 'task_' . $fieldId
		);

		return $additionalFields;
	}

	/**
	 * add the field for the id
	 *
	 * @param $realName
	 * @param $widgetConfig
	 * @return array
	 */
	protected function buildIdField($realName, $widgetConfig) {
		//every widget needs an id
		$additionalFields = array();

		$fieldId = $realName . '_id';
		$subField = 'id';
		$fieldCode = $this->getInputField($realName, $subField, $widgetConfig['id']);
		$additionalFields[$fieldId] = array(
			'code' => $fieldCode,
			'label' => 'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.widgetId',
			'cshKey' => '_MOD_tools_txgeckoboardM1',
			'cshLabel' => 'task_' . $fieldId
		);

		return $additionalFields;
	}

	/**
	 * add the field for the type plus links to the type definitions
	 *
	 * @param $realName
	 * @param $widgetConfig
	 * @return array
	 */
	protected function buildTypeField($realName, $widgetConfig) {
		$additionalFields = array();

		/** @var Tx_Geckoboard_Widgets_Widget $widget */
		$widget = t3lib_div::makeInstance($realName);
		$widgetTypes = $this->getValidTypes($widget);

		$fieldId = $realName . '_type';
		$currentWidgetTypes = array($widgetConfig['type']);
		$fieldCode = $this->getRadioButtons($realName, 'type', $widgetTypes, $currentWidgetTypes);

		$additionalFields[$fieldId] = array(
			'code' => $fieldCode,
			'label' => $this->language->sL(
					'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.widgetType'
				),
			'cshKey' => '_MOD_tools_txgeckoboardM1',
			'cshLabel' => 'task_' . $fieldId
		);

		return $additionalFields;
	}

	/**
	 * queue up a message to the backend user
	 *
	 * @param $message
	 * @param $header
	 * @param int $severity
	 * @return void
	 */
	protected function displayFlashMessage($message, $header, $severity = t3lib_FlashMessage::ERROR) {
		/** @var t3lib_FlashMessage $flashMessage */
		$flashMessage = t3lib_div::makeInstance(
			't3lib_FlashMessage', $message, $header, $severity
		);
		t3lib_FlashMessageQueue::addMessage($flashMessage);
	}

	/**
	 * check that widgets which have been selected are also configured correctly
	 *
	 * @param $dataToCheck
	 * @return bool
	 */
	protected function validateConfiguredWidgets($dataToCheck) {
		$everythingOK = TRUE;
		$this->initializeClassLoader();

		foreach ($dataToCheck['configuredWidgets'] as $widgetName) {
			$widget = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')
				->get($widgetName);

			//if the widget was JUST selected then show a warning instead of an error
			$severity = (isset($dataToCheck[$widgetName]) ?
				t3lib_FlashMessage::ERROR :
				t3lib_FlashMessage::WARNING);

			/** @var $widget Tx_Geckoboard_Widgets_Widget */
			$widget->setConfig($dataToCheck[$widgetName]);
			if (!$widget->configIsValid()) {
				$everythingOK = FALSE;
				$this->displayWidgetErrors($widgetName, $widget, $severity);
			}
		}

		return $everythingOK;
	}

	/**
	 * display properly translated error message(s) for this widget
	 *
	 * @param string $widgetName
	 * @param Tx_Geckoboard_Widgets_Widget $widget
	 * @param int $severity
	 * @return void
	 */
	protected function displayWidgetErrors($widgetName, $widget, $severity) {
		$message = array();
		$widgetDisplayName = $this->language->sL(
			'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:label.' . $widgetName
		);

		$header = sprintf(
			$this->language->sL(
				'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:error.widgetError'
			), $widgetDisplayName
		);
		foreach ($widget->error as $errorMsg) {
			$message[] = $this->language->sL(
				'LLL:EXT:geckoboard/Resources/Private/Language/locallang_be.xml:error.' . $errorMsg
			);
		}
		$this->displayFlashMessage(implode('<br>', $message), $header, $severity);
	}
}

?>