<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 06.04.18
 * Time: 16:55
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImport implements ImportInterface {

	public $start;
	public $stop;

	public $fields;

	public function __construct( string $start, string $stop, array $fields ) {
		$dt1 = new \DateTime($start);
		$this->start = $dt1->getTimestamp();
		$dt2 = new \DateTime($stop);
		$this->stop = $dt2->getTimestamp();

		$this->fields = $this->_createFields($fields);

		$this->query = $this->_query();
	}

	abstract public function execute();

	abstract public function fetchRow();

	abstract public static function getOldestEntryTime();

	/**
	 * Returns an array of fields, suitable for ordering and querying
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	protected function _createFields($fields) {
		return array_combine(array_column($fields, 'export_name'), array_column($fields, 'import_name'));
	}

	/**
	 * @return mixed Entity Field Query
	 */
	protected abstract function _query();

}