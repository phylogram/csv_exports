<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 09:26
 */

class PhylogramDataTransferBlacklist {

  /**
   *
   * @return  bool
   */
  public static function contains(string $email_adress) {
    # if not in database return null and ask topic
    $stm = <<<STM
          SELECT phylogram_datatransfer_blacklist.email_adress
            FROM phylogram_datatransfer_blacklist
           WHERE phylogram_datatransfer_blacklist.email_adress = :email_adress;
STM;

    $query = db_query($stm, [':email_adress' => $email_adress]);
    $result = $query->fetchField();
    return $result == TRUE;
  }

}