<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 19:59
 */
namespace Drupal\phylogram_datatransfer\import_model;

class AccessTime {

  /**
   * @param string $topic
   *
   * @return  $string || NULL
   */
  public static function getLast(string $topic) {
    # if not in database return null and ask topic
    $stm = <<<STM
          SELECT phylogram_datatransfer_export_time.access
            FROM phylogram_datatransfer_export_time
           WHERE phylogram_datatransfer_export_time.topic = :topic
        ORDER BY phylogram_datatransfer_export_time.access DESC
           LIMIT 1;
STM;

    $query = db_query($stm, [':topic' => $topic]);
    $result = $query->fetchField();
    return $result;
  }


  public static function setLast(string $topic, $unix_timestamp) {
    $table = db_insert('phylogram_datatransfer_export_time');
    $table->fields(array(
      'topic' => $topic,
      'access' => $unix_timestamp));
    $table->execute();
  }
}