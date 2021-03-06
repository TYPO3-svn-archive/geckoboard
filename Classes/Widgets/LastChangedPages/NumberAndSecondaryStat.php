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

class Tx_Geckoboard_Widgets_LastChangedPages_NumberAndSecondaryStat
	extends Tx_Geckoboard_Widgets_LastChangedPages {

	/**
	 * load the needed data
	 *
	 * @return void
	 */
	protected function getData() {
		/** @var Tx_Geckoboard_Domain_Repository_PagesRepository $pageRepository */
		$pageRepository = $this->objectManager->get('Tx_Geckoboard_Domain_Repository_PagesRepository');
		$this->data['pageCount'] = $pageRepository->getLastChangedPageCount();
	}

	/**
	 * load the applicable widget
	 *
	 * @return CarlosIO\Geckoboard\Widgets\NumberAndSecondaryStat
	 */
	protected function loadGeckoboardWidget() {
		if (!is_numeric($this->data['pageCount'])) {
			throw new \Exception(__CLASS__ . ': pageCount was not successfully loaded from the db');
		}
		/** @var CarlosIO\Geckoboard\Widgets\NumberAndSecondaryStat $widget */
		$widget = new CarlosIO\Geckoboard\Widgets\NumberAndSecondaryStat();

		$widget->setId($this->config['id']);
		$widget->setMainValue($this->data['pageCount']);

		return $widget;
	}
}

?>