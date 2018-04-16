<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 06.04.18
 * Time: 17:35
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Class AbstractImportPDO
 *
 * All PDO, sql, queries go here
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
abstract class AbstractImportPDO extends AbstractImport {

    /**
   * @var $stm_0 (like $statement_property_stub) is the first and main
   *   statement.
   */
  public $stm_0; // . 0, 1, 2

  /**
   * @var string $statement_property_stub will be beginning of each property
   *   that defines a statement, numbered
   *
   * This is actually needed for child @class
   *   AbstractImportPDOMultipleStatements, but "begins" here
   */
  protected $statement_property_stub = 'stm_'; // . 0, 1, 2

/**
   * @var string $create_statement_method_stub will be beginning of each method
   *   that creates a statement, numbered
   *
   * This is actually needed for child @class
   *   AbstractImportPDOMultipleStatements, but "begins" here
   */
  protected $create_statement_method_stub = '_create_stm_';

  /**
   * We use db_select, so execution has already happened.
   *
   * @return mixed
   */
  public function execute() {
    return TRUE;
  }

  /**
   * Query the main statement stm_0
   */
  protected function _query() {
    $this->query = db_query($this->stm_0, [
      ':start' => $this->start,
      ':stop' => $this->stop,
    ], [
        'fetch' => \PDO::FETCH_NAMED,
      ]
    );
  }

  /**
   * Passes to fetchAssoc()
   *
   * @return mixed
   */
  protected function _getRow() {
    return $this->query->fetchAssoc();
  }

  /**
   * Creates the field list for the select statement and passes it to the
   * right method(s).
   *
   * SELECT $fields FROM ...
   */
  protected function _prepare() {
    $this->_createStatements();
  }

  /**
   * Creates stm_0
   */
  protected function _createStatements() {
    $fields = $this->getImportNames();
    $fields = implode(', ', $fields);
    $this->_create_stm_0($fields);
  }

  /**
   * Creates the main/first sql statement. (See $create_statement_method_stub)
   *
   * @param string $fields SELECT $fields FROM ...
   */
  abstract protected function _create_stm_0(string $fields);
}


