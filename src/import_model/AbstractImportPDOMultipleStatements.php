<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 06.04.18
 * Time: 23:37
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Class AbstractImportPDOMultipleStatements
 *
 * When one query is simple not enough ...
 *
 * Calls as much queries as needed for each row of the main query.
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
abstract class AbstractImportPDOMultipleStatements extends AbstractImportPDO {

  use   \Drupal\phylogram_datatransfer\ctrl\SortFieldsTrait;

    /**
   * @var $input_fields Remembers the $field argument in _construct()
   */
  public $input_fields; // 1, 2, 3

  /**
   * $query_and_query_and_fetch_additional_data_ 0, 1, ... must be implemented
   * in concrete child classes
   *
   * @var string $query_and_fetch_additional_data_stub will be beginning of
   *   each method that queries and fetches a statement, numbered
   */
  protected $query_and_fetch_additional_data_stub = 'query_and_fetch_'; // 0, 1, 2, ...

  /**
   * $statement_tables_array_ 0, 1, ... must be implemented in concrete child
   * classes
   *
   * @var string $statement_tables_array_stub will be beginning of each
   *   property that defines all tables in a query, numbered
   */
  protected $statement_tables_array_stub = 'statement_tables_array_'; // 0, 1, 2, ...

/**
   * $statement_fields_array_ 0, 1, ... will be generated automatically!
   *
   * @var string $statement_fields_array_stub will be beginning of each
   *   property that defines the fields of a statement, numbered
   */
  protected $statement_fields_array_stub = 'statement_fields_array_';

  /**
   * Overrides parent method
   *
   * Creates the field list for the select statement and passes it to the
   * right method(s).
   *
   * SELECT $fields FROM ...
   */
  protected function _createStatements() {

    $methods = get_class_methods($this);

    foreach ($methods as $method) {
      if (substr($method, 0, strlen($this->create_statement_method_stub)) === $this->create_statement_method_stub) {
        $n = substr($method, strlen($this->create_statement_method_stub));
        $fields_property = $this->statement_fields_array_stub . $n;
        $fields = $this->$fields_property;
        $fields = implode(', ', $fields);
        $this->$method($fields);
        $statement_property = $this->statement_property_stub . $n;
        $statement = $this->$statement_property;
      }
    }
  }

  /**
   * Calls the additional queries and sorts the fields.
   *
   * @return bool|void
   */
  protected function _modifyRow() {
    $this->_getAdditionalData();
    $this->row = $this->sortFields($this->row, $this->sort_fields);
  }


  /**
   * Calls all methods starting with query_and_fetch_
   *
   * These will have to be implemented foreach class
   */
  protected function _getAdditionalData() {
    $methods = get_class_methods($this);
    foreach ($methods as $method) {
      if (substr($method, 0, strlen($this->query_and_fetch_additional_data_stub)) === $this->query_and_fetch_additional_data_stub) {
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
   */
  protected function _createFields(array $fields) {
    parent::_createFields($fields);
    $this->_findTablesAndFields();
    $this->_addSortedFieldsProperty();
    $this->_sortTablesToQueries();
  }

  /**
   * Creates a fields list, where tables, fields, full names, sql expressions
   * ans export names are stated.
   *
   * They keys are:
   * 'export_name' => $export_name,
   * 'import_name' => $import_name, // The sql statement
   * 'table_name' => $table,
   * 'field_name' => $field,
   * 'full_field_name' => $table . '.' . $field,
   */
  protected function _findTablesAndFields() {
    $export_names = $this->getExportNames();
    $import_names = $this->getImportNames();
    $extracted_keys = [];
    $regex = <<<'REGEX'
	/(?<table>[0-9, a-z, A-Z$_]+)\`{0,1}\.\`{0,1}(?<![\'\"])(?<field>[0-9a-zA-Z$_]+)/
REGEX;
    foreach ($export_names as $export_name) {
      $import_name = current($import_names);
      next($import_names);
      $result = [];
      preg_match($regex, $import_name, $result);
      if (!$result) {
        continue; // and cry
      }
      $table = $result['table'];
      $field = $result['field'];

      $extracted_keys[] = [
        'export_name' => $export_name,
        'import_name' => $import_name,
        'table_name' => $table,
        'field_name' => $field,
        'full_field_name' => $table . '.' . $field,
      ];
    }
    $this->input_fields = $this->fields; // For later use?
    $this->fields = $extracted_keys;
  }

  /**
   * Adds a sorted field property like this array('export_name' =>
   * array(aliases), ...)
   */
  protected function _addSortedFieldsProperty() {
    foreach ($this->fields as $field) {
      $export_name = $field['export_name'];
      unset($field['export_name']);
      $this->sort_fields[$export_name] = $field;
    }
  }

  /**
   * Goes through each field, to which table the field belongs, to which query
   * the table belongs and stores that information.
   */
  protected function _sortTablesToQueries() {
    $table_arrays = $this->_getQueryTables();
    foreach ($this->fields as $field) {
      $table_name = $field['table_name'];
      $import_name = $field['import_name'];
      foreach ($table_arrays as $property => $table_array) {
        if (array_search($table_name, $table_array) !== FALSE) {
          $n = substr($property, strlen($this->statement_tables_array_stub));
          $fields_array_property = $this->statement_fields_array_stub . $n;
          $this->$fields_array_property[] = $import_name;
          break;
        }
      }
    }
  }


  /**
   * Lists all properties, that contain an array of fields belonging to one
   * query
   *
   * @return array
   */
  protected function _getQueryTables() {
    $properties = get_object_vars($this);
    $table_properties = [];
    foreach ($properties as $property => $value) {
      if (substr($property, strlen('_stub') * -1) === '_stub') {
        continue;
      }
      if (substr($property, 0, strlen($this->statement_tables_array_stub)) === $this->statement_tables_array_stub) {
        $table_properties[$property] = $value;
      }
    }
    return $table_properties;
  }

  /**
   * Adds data (from additional queries) to current row and checks if keys
   * already exists. If, adds table to keyname.
   *
   * @param array $additional_data from fetchAssoc or similar.
   * @param array $fields The fields contained in the specific query.
   */
  protected function _addDataToRow(array $additional_data, array $fields) {

    foreach ($fields as $field_aliases) {
      $value = NULL;
      $field_name = $field_aliases['field_name'];
      $full_field_name = $field_aliases['full_field_name'];
      $value = array_key_exists($field_name, $additional_data) ? $additional_data[$field_name] : NULL;
      $value = array_key_exists($full_field_name, $additional_data) ? $additional_data[$full_field_name] : $value;
      $new_key = array_key_exists($this->row, $field_name) ? $field_name : $full_field_name;
      $this->row[$new_key] = $value;
    }
  }
}