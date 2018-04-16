<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 11:48
 */

namespace Drupal\phylogram_datatransfer\import_model\imports;

class Action extends \Drupal\phylogram_datatransfer\import_model\AbstractImportPDO {

  public static $oldest_entry_stm = <<<STM2
  SELECT submitted 
    FROM webform_submissions
ORDER BY submitted ASC
   LIMIT 1;
STM2;

  /**
   * Prepares the main statement
   *
   */
  protected function _create_stm_0(string $fields) {

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
		 WHERE node.type = 'donation'
		       OR node.type = 'webform'
		       OR node.type = 'email_protest'
		       OR node.type = 'petition'
		       );
MAIN_STM;
  }

}