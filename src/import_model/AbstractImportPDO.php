<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 06.04.18
 * Time: 17:35
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportPDO extends AbstractImport {

	protected $statement_property_stub = 'stm_';
    protected $create_statement_method_stub = '_create_stm_'; # 0, 1, 2

    public $stm_0;

    /**
     * Creates the main/first sql statement.
     * @param string $fields SELECT $fields FROM ...
     */
    abstract protected function _create_stm_0(string $fields);

	/**
	 * We use db_select, so execution has already happened.
	 *
	 * @return mixed
	 */
	public function execute() {
		return TRUE;
	}

	protected function _query() {
		$this->query = db_query($this->stm_0, [
			'start' => $this->start,
			'stop' => $this->stop,
		]);
	}

	protected function _getRow() {
		return $this->query->fetchAssoc();
	}

    /**
     * Creates the field list for the select statement and passes it to the right method(s).
     *
     * SELECT $fields FROM ...
     */
	protected function _prepare() {
		$fields = $this->getImportNames();
		$fields = implode(', ', $fields);
        $this->_createStatements();
	}

	protected function _createStatements() {
	    $this->_create_stm_0();
    }
}


