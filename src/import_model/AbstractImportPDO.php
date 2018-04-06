<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 06.04.18
 * Time: 17:35
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportPDO extends AbstractImport {

	public $stm_0;

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

	protected function _prepare() {
		$fields = $this->getImportNames();
		$fields = implode(', ', $fields);
		$this->_create_stm_0($fields);
	}

	abstract protected function _create_stm_0(string $fields);
}

