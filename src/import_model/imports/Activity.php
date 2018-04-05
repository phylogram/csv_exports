<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 04.04.18
 * Time: 10:13
 */

namespace import_model\imports;


class Activity implements \Drupal\phylogram_datatransfer\import_model\ImportInterface {

  public $start;
  public $stop;

  public $fields = [];
  public $main_stm = '';

  public $query;


  public static $oldest_entry_stm = <<<STM2
  SELECT created 
    FROM campaignion_activity
ORDER BY submitted ASC
   LIMIT 1;
STM2;

  /**
   * ImportInterface constructor.
   *
   * @param string $start TimeString
   * @param string $stop TimeString
   *
   */
  public function __construct(string $start, string $stop, array $fields) {
    $dt1 = new \DateTime($start);
    $this->start = $dt1->getTimestamp();
    $dt2 = new \DateTime($stop);
    $this->stop = $dt2->getTimestamp();

    $this->fields = array_combine(array_column($fields, 'export_name'), array_column($fields, 'import_name'));
    $fields = implode(', ', $fields);

    // Fill the fields into the query
    $this->main_stm = <<<MAIN_STM
SELECT $fields
  FROM campaignion_activity
  JOIN redhen_contact
    ON (redhen_contact.contact_id = campaignion_activity.activity_id);
MAIN_STM;

    // State the query and execute it.
    $this->query = db_query($this->main_stm, [
      'start' => $this->start,
      'stop' => $this->stop,
    ]);
  }

  /**
   * In this case, we use db_select, so execution has already happened.
   *
   * @return mixed
   */
  public function execute() {
    return TRUE;
  }

  /**
   * Like db-fetch
   *
   * @return array
   */
  public function fetchRow() {
    while ($row = $this->query->fetchAssoc()) {
      // Sort the row
      $ordered_row = [];
      foreach ($this->fields as $field) {
        $ordered_row[$field] = $row[$field];
      }
      yield $ordered_row;
    }
  }

  /**
   * Oldest entry in main table concerning topic
   *
   * @return string
   */
  public static function getOldestEntryTime() {
    $query = db_query(self::$oldest_entry_stm);
    $unix_tmstp = $query->fetchField();
    $dt = new \Datetime();
    $dt->setTimestamp($unix_tmstp);
    $string = $dt->format('Y-m-d');
    return $string;
  }

}