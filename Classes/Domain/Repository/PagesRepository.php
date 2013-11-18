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

class Tx_Geckoboard_Domain_Repository_PagesRepository extends Tx_Extbase_Persistence_Repository {

	const VALID_PAGE_WHERE = 'pages.deleted = 0 AND hidden = 0 AND doktype IN (%s)';

	/**
	 * @var t3lib_db
	 */
	protected $databaseBackend;

	/**
	 * @var string
	 */
	protected $dateFormat = '%d.%m.%Y %H:%i';

	/**
	 * @var string
	 */
	protected $doktypes = '1';

	/**
	 * @var string
	 */
	protected $validPageWhere;

	/**
	 * @var int
	 */
	protected $tstampXHoursAgo;

	/**
	 * @param t3lib_db $databaseBackend
	 * @return void
	 */
	public function injectDatabaseBackend(t3lib_db $databaseBackend) {
		$this->databaseBackend = $databaseBackend;
		$this->databaseBackend->connectDB();
	}

	/**
	 * @return void
	 */
	public function __construct() {
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geckoboard']);
		if (isset($confArr['dateFormat'])) {
			$this->dateFormat = $confArr['dateFormat'];
		}

		if (isset($confArr['doktypes'])) {
			$this->setDokTypes($confArr['doktypes']);
		}

		if (isset($confArr['hours']) && is_numeric($confArr['hours'])) {
			$hours = $confArr['hours'];
		} else {
			$hours = 24;
		}

		$this->tstampXHoursAgo = $GLOBALS['EXEC_TIME'] - (3600 * $hours);
		$this->validPageWhere = sprintf(self::VALID_PAGE_WHERE, $this->doktypes);
	}

	/**
	 * @param string $doktypes comma-separated list of numbers
	 * @return void
	 */
	public function setDokTypes($doktypes) {
		$configDoktypes = array_map('trim', explode(',', $doktypes));
		$validDoktypes = array_filter($configDoktypes, 'is_numeric');
		if (count($validDoktypes) > 0) {
			$this->doktypes = implode(',', $validDoktypes);
		}
	}

	/**
	 * how many normal active pages are there?
	 *
	 * @return resource
	 */
	public function getCountActiveStandardPages() {
		/** @var $statement t3lib_db_PreparedStatement */
		$statement = $this->databaseBackend->prepare_SELECTquery(
			'count(*) as count', 'pages', $this->validPageWhere
		);

		$statement->execute();
		$pageCount = $statement->fetch();
		$statement->free();

		return (isset($pageCount['count']) && is_numeric($pageCount['count']) ?
			$pageCount['count'] :
			FALSE);
	}

	/**
	 * how many normal active pages have been changed in the last X hours?
	 *
	 * @return resource
	 */
	public function getLastChangedPageCount() {
		/** @var $statement t3lib_db_PreparedStatement */
		$statement = $this->databaseBackend->prepare_SELECTquery(
			'count(*) as count', 'pages',
			$this->validPageWhere . ' AND tstamp > ' . $this->tstampXHoursAgo
		);

		$statement->execute();
		$pageCount = $statement->fetch();
		$statement->free();

		return (isset($pageCount['count']) && is_numeric($pageCount['count']) ?
			$pageCount['count'] :
			FALSE);
	}

	/**
	 * how many new pages have been added in the last X hours?
	 *
	 * @return resource
	 */
	public function getLatestNewPageCount() {
		/** @var $statement t3lib_db_PreparedStatement */
		$statement = $this->databaseBackend->prepare_SELECTquery(
			'count(*) as count', 'pages',
			$this->validPageWhere . ' AND crdate > ' . $this->tstampXHoursAgo
		);

		$statement->execute();
		$pageCount = $statement->fetch();
		$statement->free();

		return (isset($pageCount['count']) && is_numeric($pageCount['count']) ?
			$pageCount['count'] :
			FALSE);
	}

	/**
	 * which new pages have been added?
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getLatestNewPages($limit = 10) {
		/** @var $statement t3lib_db_PreparedStatement */
		$statement = $this->databaseBackend->prepare_SELECTquery(
			'concat(title, " - ", date_format(from_unixtime(tstamp), "' . $this->databaseBackend->quoteStr(
				$this->dateFormat, ''
			) . '")) as title', 'pages', $this->validPageWhere, '' /*groupby*/, 'crdate desc' /*orderby*/, $limit
		);

		$statement->execute();
		$pages = $statement->fetchAll();
		$statement->free();

		return (array) $pages;
	}

	/**
	 * which pages have been changed most recently?
	 *
	 * @param int $limit
	 * @return array
	 */
	public function getLastChangedPages($limit = 10) {
		/** @var $statement t3lib_db_PreparedStatement */
		$statement = $this->databaseBackend->prepare_SELECTquery(
			'concat(title, " - ", date_format(from_unixtime(tstamp), "' . $this->databaseBackend->quoteStr(
				$this->dateFormat, ''
			) . '")) as title', 'pages', $this->validPageWhere, '' /*groupby*/, 'tstamp desc'/*orderby*/, $limit
		);

		$statement->execute();
		$pages = $statement->fetchAll();
		$statement->free();

		return (array) $pages;
	}
}

?>