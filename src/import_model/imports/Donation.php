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

  public $query;

  public $main_stm = <<<MAIN_STM
SELECT webform_submissions.nid as nid, webform_submissions.sid AS sid, if(is_draft, 'Yes', 'No') as Draft, from_unixtime(submitted) AS Submitted,
       from_unixtime(completed) AS Completed, from_unixtime(modified) AS Modified, remote_addr AS 'Remote Adress',
       if(completed, 'Yes', 'No') AS Completed, referer AS Referer, external_referer AS 'External Referer',
       source AS Source, medium AS Medium, version as Version, other as Other,
       country as Country, term as Term, campaign AS Campaign
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

  public $payment_status_stm = <<<STM2
SELECT replace(payment_status_item.status, 'payment_status_', '') AS 'Payment Status'
  FROM payment_status_item
  JOIN (campaignion_activity_payment, campaignion_activity, campaignion_activity_webform)
    ON (    campaignion_activity_payment.pid = payment_status_item.pid
       AND campaignion_activity.activity_id = campaignion_activity_payment.activity_id
       AND campaignion_activity_webform.activity_id = campaignion_activity.activity_id
       )
 WHERE campaignion_activity_webform.sid = .sid
ORDER BY payment_status_item.created DESC
   LIMIT 1
STM2;

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
  public function __construct(string $start, string $stop) {
    $file = fopen('/home/phylogram/log', 'a');
    fwrite($file, $start);
    fclose($file);
    $dt1 = new \DateTime($start);
    $this->start = $dt1->getTimestamp();
    $dt2 = new \DateTime($stop);
    $this->stop = $dt2->getTimestamp();

    $this->query = db_query($this->main_stm, [
      'start' => $this->start,
      'stop' => $this->stop,
    ]);
  }

  public function getHeader(): array {
    $main_header = [
      'nid',
      'sid',
      'Draft',
      'Submitted',
      'Completed',
      'Modified',
      'Remote Adress',
      'Source',
      'Medium',
      'Version',
      'Other',
      'Country',
      'Term',
      'Campaign',
      'Payment Status',
    ];
  }

  /**
   * Columns with e-mail-adress in it, to exclude
   *
   * @return array
   */
  public function getNameColumns(): array {
    return [];
  }

  /**
   * actually starts the main query
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
      $nid = $row['nid'];
      $sid = $row['sid'];

      $payment_query = db_query($this->payment_status_stm, ['sid' => $sid]);
      $row['Payment Status'] = $payment_query->fetchField();
      yield $row;
    }
  }

  /**
   * Oldest entry in main table concerning topic
   *
   * @return string
   */
  public static function getOldestEntryTime(): string {
    $query = db_query(self::$oldest_entry_stm);
    $unix_tmstp = $query->fetchField();
    $dt = new Datetime();
    $dt->setTimestamp($unix_tmstp);
    $string = $dt->format('Y-m-d');
    return $string;
  }

}