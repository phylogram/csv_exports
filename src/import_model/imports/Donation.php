<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 11:48
 */
namespace Drupal\phylogram_datatransfer\import_model\imports;

class Donation implements \Drupal\phylogram_datatransfer\import_model\ImportInterface {

  public $start;
  public $stop;

  public $fields = [];

  public $main_stm = '';
  public $main_fields = array();
  public $main_tables = ['webform_submissions', 'webform_tracking'];

  public $payment_stm = '';
  public $payment_fields = array();
  public $payment_tables = [
    'payment_status_item', 'campaignion_activity_payment', 'campaignion_activity',
    'campaignion_activity_webform',
    ];

  public $query;


  public static $oldest_entry_stm = <<<STM3
  SELECT submitted 
    FROM webform_submissions
ORDER BY submitted ASC
   LIMIT 1;
STM3;


  /**
   * ImportInterface constructor.
   *
   * @param string $start TimeString
   * @param string $stop TimeString
   */
  public function __construct(string $start, string $stop, array $fields) {
    $dt1 = new \DateTime($start);
    $this->start = $dt1->getTimestamp();
    $dt2 = new \DateTime($stop);
    $this->stop = $dt2->getTimestamp();

    // Order list of fields from config
    // Sort them to queries
    // We change the columns into key / value pairs so it is easier to iterate
    $this->fields = array_combine(array_column($fields, 'export_name'), array_column($fields, 'import_name'));



    foreach ($this->fields as $field) {
        foreach ($this->main_tables as $main_table) {
          if (strpos( $field, $main_table) !== FALSE) {
            $this->main_fields[] = $field;
          }
        }
      foreach ($this->payment_tables as $payment_table) {
        if (strpos( $field, $payment_table) !== FALSE) {
          $this->payment_fields[] = $field;
        }
      }


    }

    // Fill in the fields to the query
    // 1 – Main Statement
    $fields = implode(', ', $this->main_fields);
    $this->main_stm = <<<MAIN_STM
SELECT $fields
  FROM webform_submissions
  JOIN (webform_tracking)
    ON (webform_tracking.sid = webform_submissions.sid)
 WHERE  webform_submissions.submitted > :start
        AND
        webform_submissions.submitted < :stop
        AND
	   webform_submissions.nid IN
       (SELECT node.nid
          FROM node
		 WHERE node.type = 'donation');
MAIN_STM;

    // 2 – Payment Statement
    $fields = implode(', ', $this->payment_fields);
    $this->payment_stm = <<<STM2
SELECT $fields
  FROM payment_status_item
  JOIN (campaignion_activity_payment, campaignion_activity, campaignion_activity_webform)
    ON (    campaignion_activity_payment.pid = payment_status_item.pid
       AND campaignion_activity.activity_id = campaignion_activity_payment.activity_id
       AND campaignion_activity_webform.activity_id = campaignion_activity.activity_id
       )
 WHERE campaignion_activity_webform.sid = :sid
ORDER BY payment_status_item.created DESC
   LIMIT 1
STM2;

    // Finally state and execute the main query
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
    #$this->query->execute();
    return TRUE;
  }

  /**
   * Like db-fetch
   *
   * @return array
   */
  public function fetchRow() {
    while ($row = $this->query->fetchAssoc()) {
      // Get additional data
      $sid = $row['sid'];
      $payment_query = db_query($this->payment_stm, ['sid' => $sid]);
      $row['Payment Status'] = $payment_query->fetchField();
      
      // Order it
      $ordered_row = [];
      foreach ($this->fields as $field) {
        $ordered_row[$field] = $row['field'];
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