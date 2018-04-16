<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 20:16
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Interface ImportInterface
 *
 * What all classes in import_model\imports\ should be able to do!
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
interface ImportInterface {

  /**
   * ImportInterface constructor.
   *
   * @param \DateObject $start from
   * @param \DateObject $stop to
   * @param array $fields array(array('export_name' => 'Is Draft',
   *   'import_name' => 'some_table.is_draft'), array(...), ...)
   *
   */
  public function __construct(\DateObject $start, \DateObject $stop, array $fields);

  /**
   * Oldest entry in main table concerning topic.
   *
   * @return string
   */
  public static function getOldestEntryTime();

  /**
   * Actually starts the main query.
   *
   * @return mixed
   */
  public function execute();

  /**
   * Like db-fetch.
   *
   * @return array
   */
  public function fetchRow();

  /**
   * Get the headers for csv
   *
   * @return mixed
   */
  public function getExportNames();
}