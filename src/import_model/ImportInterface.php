<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 20:16
 */

namespace Drupal\phylogram_datatransfer\import_model;

interface ImportInterface {

  /**
   * ImportInterface constructor.
   *
   * @param string $start TimeString
   * @param string $stop TimeString
   */
  public function __construct(string $start, string $stop);

  public function getHeader(): array;

  /**
   * Columns with e-mail-adress in it, to exclude
   *
   * @return array
   */
  public function getNameColumns(): array;

  /**
   * actually starts the main query
   *
   * @return mixed
   */
  public function execute();

  /**
   * Like db-fetch
   *
   * @return array
   */
  public function fetchRow();

  /**
   * Oldest entry in main table concerning topic
   *
   * @return string
   */
  public static function getOldestEntryTime(): string;
}