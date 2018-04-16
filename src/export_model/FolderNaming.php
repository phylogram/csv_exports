<?php

/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 20.03.18
 * Time: 19:31
 */

namespace Drupal\phylogram_datatransfer\export_model;

/**
 * Class FolderNaming
 *
 * Translates paths with date() to array of parsed folder_names
 *
 * @package Drupal\phylogram_datatransfer\export_model
 */
class FolderNaming {

  /**
   * replaces current time in array('time' => stuff)
   *
   * @param string $folder_structure like 'AllMyFolders/Y/m/'
   * @param string $file_name
   * @param bool $file_name_to_date if TRUE will parse file name with date()
   * @param $timestamp string|int unix timestamp – if not given time()
   *
   * @return array
   */
  static public function translateTime(string $folder_structure, string $file_name, $file_name_to_date = FALSE, $timestamp = NULL) {
    $timestamp = !$timestamp ? time() : $timestamp;
    $translation = date($folder_structure, $timestamp);
    $translation = substr($translation, -1) === DIRECTORY_SEPARATOR ? $translation : $translation . DIRECTORY_SEPARATOR;
    $translation = $translation[0] === DIRECTORY_SEPARATOR ? substr($translation, 1) : $translation;
    $file_name = $file_name_to_date ? date($file_name, $timestamp) : $file_name;
    $translation .= $file_name;
    $translation = explode(DIRECTORY_SEPARATOR, $translation);

    return $translation;
  }
}
