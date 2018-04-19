<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 16.04.18
 * Time: 12:50
 */

namespace Drupal\phylogram_datatransfer\import_model\imports;


class OptOutContact extends \Drupal\phylogram_datatransfer\import_model\AbstractImportEntityOptValue {

  public $optin = 0;

  public $expire_modification = '+2 months';

  protected function _addData() {
    parent::_addData();
    $created = $this->row['created'];
    $created = new \DateObject($created);
    $created->modify($this->expire_modification);
    $expire = $created->getTimestamp();
    $this->row['expires'] = $expire;
  }
}