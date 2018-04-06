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
    abstract protected  function _getRow();

    /**
     * Executes $this->query.
     */
    abstract public function execute();


	public function __construct( string $start, string $stop, array $fields ) {
		$dt1 = new \DateTime($start);
		$this->start = $dt1->getTimestamp();
		$dt2 = new \DateTime($stop);
		$this->stop = $dt2->getTimestamp();

		$this->_createFields($fields);
		$this->_prepare();
		$this->_query();
	}

	public function fetchRow() {
		while ($this->row = $this->_getRow()) {
			$this->_modifyRow();
			yield $this->row;
		}
	}

	/**
	 * @return string oldest entry time as Y-m-d
	 */
	public static function getOldestEntryTime() {
		$query = db_query(self::$oldest_entry_stm);
		$unix_tmstp = $query->fetchField();
		$dt = new \Datetime();
		$dt->setTimestamp($unix_tmstp);
		$string = $dt->format('Y-m-d');
		return $string;
	}

    public function getExportNames() {
        return array_column($this->fields, 'export_name');
    }

    public function getImportNames() {
        return array_column($this->fields, 'import_name');
    }


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
        return True;
    }
}