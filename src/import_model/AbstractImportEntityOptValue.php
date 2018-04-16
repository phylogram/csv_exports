<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 16.04.18
 * Time: 12:46
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportEntityOptValue extends \Drupal\phylogram_datatransfer\import_model\imports\RedhenContact {

  public $optin;

  public $optin_stm;

  public function _prepare() {
    parent::_prepare();
    $this->optin_stm = <<<SQL

SQL;
  }

  protected function _query() {
    parent::_query();
    //$this->query->fieldCondition('field_direct_mail_newsletter', '', $this->optin, '=');
  }

  protected function _modifyRow() {
    $this->_addData();
    parent::_modifyRow();
  }

  protected function _addData() {
    if (array_key_exists('contact_id', $this->row)) {
      $contact_id = $this->row['contact_id'];

      $contact_id = is_array($contact_id) ? current($contact_id) : $contact_id;
      $query = db_query(
        "SELECT optin_statement FROM campaignion_activity JOIN (campaignion_activity_newsletter_subscription) ON (campaignion_activity.activity_id = campaignion_activity_newsletter_subscription.activity_id) WHERE campaignion_activity.contact_id < :contact LIMIT 1",
        [
          'contact' => $contact_id,
        ]
      );
      $result = $query->execute();
      $field = $result ? $result->fetchField() : NULL;
      $this->row['optin_statement'] = $field;
    }
  }

  protected function _getRow() {
    $parent_rows = parent::_getRow();
    foreach ($parent_rows as $parent_row) {
      $parent_row = (array) $parent_row;
      $optin = current(current($parent_row['field_direct_mail_newsletter']))['value'];
      if ($optin != $this->optin) {
        continue;
      }
      yield $parent_row;
    }

  }

}