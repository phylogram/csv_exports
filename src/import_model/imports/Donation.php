<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 11:48
 */

namespace Drupal\phylogram_datatransfer\import_model\imports;

class Donation extends \Drupal\phylogram_datatransfer\import_model\AbstractImportPDOMultipleStatements {

	public $statement_tables_array_0 = [ 'webform_submissions', 'webform_tracking' ];
    protected function _create_stm_0(string $fields)
    {
        // Making sure webform_submissions.sid is in the game, for stm_1
        $fields .= strpos($fields, 'sid') === FALSE ? ', webform_submissions.sid': '';
        $this->stm_0 = <<<MAIN_STM
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
    }

    public $stm_1;
	public $statement_tables_array_1 = [
		'payment_status_item',
		'campaignion_activity_payment',
		'campaignion_activity',
		'campaignion_activity_webform',
	];

    protected function _create_stm_1($fields) {
        $this->stm_1 = <<<STM2
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
    }

    public function query_and_fetch_additional_data_1() {
        $sid = $this->row['sid'];
        $payment_query = db_query( $this->stm_1, [ 'sid' => $sid ] );
        $row_1 = $payment_query->fetchAssoc();
        $this->_addDataToRow($row_1, $this->statement_fields_array_1);
    }


	public static $oldest_entry_stm = <<<STM3
  SELECT submitted 
    FROM webform_submissions
ORDER BY submitted ASC
   LIMIT 1;
STM3;




}