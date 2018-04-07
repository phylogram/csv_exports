<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 04.04.18
 * Time: 10:13
 */

namespace import_model\imports;


class Activity extends \Drupal\phylogram_datatransfer\import_model\AbstractImportPDO {

	public static $oldest_entry_stm = <<<STM2
  SELECT created 
    FROM campaignion_activity
ORDER BY submitted ASC
   LIMIT 1;
STM2;

	protected function _create_stm_0( string $fields ) {
		$this->main_stm = <<<MAIN_STM
SELECT $fields
  FROM campaignion_activity
  JOIN redhen_contact
    ON (redhen_contact.contact_id = campaignion_activity.activity_id);
MAIN_STM;
	}
}