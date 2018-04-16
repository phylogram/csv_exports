<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 03.04.18
 * Time: 15:28
 */

namespace Drupal\phylogram_datatransfer\import_model\imports;


class RedhenContact extends \Drupal\phylogram_datatransfer\import_model\AbstractImportEntity {

  public static $oldest_entry_stm = <<<STM_OLDEST
  SELECT created
    FROM redhen_contact
ORDER BY created ASC
   LIMIT 1;
STM_OLDEST;

  public $entity_type = 'redhen_contact';


}