<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 09:26
 */

namespace Drupal\phylogram_datatransfer\import_model;

class Blacklist {

	/**
	 * @param string $email_address
	 * @return  bool
	 */
	public static function contains( string $email_address ) {

	    $email_address = password_hash($email_address);
		# if not in database return null and ask topic
		$stm = <<<STM
          SELECT phylogram_datatransfer_blacklist.email_address
            FROM phylogram_datatransfer_blacklist
           WHERE phylogram_datatransfer_blacklist.email_address = :email_address;
STM;

		$query  = db_query( $stm, [ ':email_address' => $email_address ] );
		$result = $query->fetchField();

		return $result == TRUE;
	}

	public static function insert($email_address) {
        $email_address = password_hash($email_address);
	    $insert = db_insert('phylogram_datatransfer_blacklist');
	    $insert->fields([
	        'email_address' => $email_address,
        ]);
	    $insert->execute();
    }

    public static function remove($email_address) {
        $email_address = password_hash($email_address);
        $delete = db_delete('phylogram_datatransfer_blacklist');
        $delete->condition('email_address', $email_address, '=');
        $delete->execute();
    }
}