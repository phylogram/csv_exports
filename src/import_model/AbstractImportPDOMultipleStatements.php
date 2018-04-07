<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 06.04.18
 * Time: 23:37
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportPDOMultipleStatements extends AbstractImportPDO {

	protected $query_and_fetch_additional_data_stub = 'query_and_fetch_'; // 1, 2, 3

	/**
	 * Overrides parent method
	 *
	 * Creates the field list for the select statement and passes it to the
	 * right method(s).
	 *
	 * SELECT $fields FROM ...
	 */
	protected function _createStatements() {

		$methods = get_class_methods( $this );

		foreach ( $methods as $method ) {
			if ( substr( $method, 0, strlen( $this->create_statement_method_stub ) ) === $this->create_statement_method_stub ) {
				$this->$method();
			}
		}
	}

	protected function _modifyRow() {
		$this->_getAdditionalData();
		// sort with trait
	}


	/**
	 * Calls all methods starting with query_and_fetch_
	 *
	 * These will have to be imlpemented foreach class
	 */
	protected function _getAdditionalData() {
		$methods = get_class_methods( $this );
		foreach ( $methods as $method ) {
			if ( substr( $method, 0, strlen( $this->query_and_fetch_additional_data_stub ) ) === $this->query_and_fetch_additional_data_stub ) {
				$this->$method();
			}
		}
	}

	/**
	 * Overrides parent-method
	 *
	 * Extracts an array of fields from sql, suitable for ordering and querying,
	 * with keys:
	 *
	 * export_name (eg Draft), import_name (eg if(payment.is_draft, 'y', 'n')),
	 * table_name (eg payment), field_name (eg is_draft), full_field_name (eg
	 * payment.is_draft)
	 *
	 * Will not handle not ascii chars in identifiers, missing table names in
	 * import_name or sql errors
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	protected function _createFields( $fields ) {
		$extracted_keys = [];
		$regex = <<<'REGEX'
	/(?<table>[0-9, a-z, A-Z$_]+)\`{0,1}\.\`{0,1}(?<![\'\"])(?<field>[0-9a-zA-Z$_]+)/
REGEX;

		foreach ($fields as $export_name => $import_name) {
			$result = [];
			// If extraction of table && import name is not successful everything
			// else will not make sense.
			if (!preg_match($regex, $import_name, $result)) {
				continue;
			}
			extract($result);
			$extracted_keys[] = [
				'export_name' => $export_name,
				'import_name' => $import_name,
				'table_name' => $table,
				'field_name' => $field,
				'full_field_name' => $table . '.' . $field,
			];
		}
		return $extracted_keys;
	}


}