<?php
/**
 * @class BlacklistEmail The blacklist command
 *
 * Created by PhpStorm.
 * User: phylogram
 * Date: 10.04.18
 * Time: 14:34
 */

namespace Drupal\phylogram_datatransfer\ctrl;

module_load_include('config', 'phylogram_datatransfer', 'phylogram_datatransfer');

class BlacklistEmail {

  /**
   * Add an email-address to the blacklist. These Email-adresses will not be
   * exported.
   *
   * @param array $emails
   */
  static function addArray(array $emails) {
    \Drupal\phylogram_datatransfer\import_model\Blacklist::insert($emails);

    $removeRows = new \Drupal\phylogram_datatransfer\export_model\RemoveRows(
      PHYLOGRAM_DATATRANSFER_EXPORT_DATA_FOLDER,
      $emails,
      PHYLOGRAM_DATATRANSFER_CSV_DELIMITER,
      PHYLOGRAM_DATATRANSFER_CSV_ENCLOSURE,
      PHYLOGRAM_DATATRANSFER_CSV_ESCAPE_CHAR,
      PHYLOGRAM_DATATRANSFER__DEFAULT_EXPORT_FILE_EXTENSION
    );
    $removeRows->execute();
  }

}