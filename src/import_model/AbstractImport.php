<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 06.04.18
 * Time: 16:55
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImport implements ImportInterface {

	public static $oldest_entry_stm;

	public $start;
	public $stop;

	public $fields;
	public $query;

	public $row;

	/**
	 * Sets $this->query.
	 */
	abstract protected function _query();

	/**
	 * Sets $this->row.
	 */
	abstract protected function _getRow();

	/**
	 * Executes $this->query.
	 */
	abstract public function execute();

    /**
     * AbstractImport constructor.
     * @param string $start
     * @param string $stop
     * @param array $fields Is supposed to be an array of arrays with keys: import_name and _export_name, like in ctrl\TransferSettings
     */
	public function __construct( $start, $stop, array $fields ) {
		$this->start = $start->getTimestamp();
		$this->stop  = $stop->getTimestamp();

		$this->_createFields( $fields );
		$this->_prepare();
		$this->_query();
	}


	public function fetchRow() {
		while ( $this->row = $this->_getRow() ) {
			$this->_modifyRow();
			yield $this->row;
		}
	}

	/**
	 * @return \DateObject oldest entry time
	 */
	public static function getOldestEntryTime() {
		$query = db_query( static::$oldest_entry_stm );
		$unix_tmstp = $query->fetchField();
		// If is no oldestEntryTime, got back to
		$unix_tmstp = $unix_tmstp ?: 'now';
		$date_object = new \DateObject($unix_tmstp);
		return $date_object;
	}

	public function getExportNames() {
		return array_column( $this->fields, 'export_name' );
	}

	public function getImportNames() {
		return array_column( $this->fields, 'import_name' );
	}


	/**
	 * Returns an array of fields, suitable for ordering and querying
	 *
	 * @param array $fields [ ['export_name'=> 'Draft', 'import_name' => 'is_draft'], ...]
	 *
	 */
	protected function _createFields( array $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Stub, to be overridden by child classes. Is called in __construct() after
	 * _createFields and _query()
	 *
	 * Suitable for defining fields in sql statements for example.
	 *
	 * @return bool
	 *
	 */
	protected function _prepare() {
		return TRUE;
	}

	protected function _modifyRow() {
		return TRUE;
	}
}