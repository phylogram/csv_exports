<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 28.03.18
 * Time: 09:26
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Class Blacklist
 *
 * Stores a hashed email black list
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
class Blacklist {

    const HASH_ALGO = PASSWORD_BCRYPT;

    /**
     * Checks if a email address is in blacklist
     *
	 * @param string $email_address
	 * @return  bool
	 */
	public static function contains( string $email_address ) {

	    $email_address = password_hash($email_address, static::HASH_ALGO );
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

    /**
     * Add new email address to blacklist.
     *
     * @param array $email_addresses
     */
	public static function insert(array $email_addresses) {
	    $insert = db_insert('phylogram_datatransfer_blacklist');
        $insert->fields(['email_address', 'created']);
	    foreach ($email_addresses as $email_address) {
	        $now = new \DateObject();
	        $now = $now->getTimestamp();
            $email_address = password_hash($email_address, static::HASH_ALGO);
            $insert->values([$email_address, $now]);
        }
	    $insert->execute();
    }

    /**
     * Remove email address from black list.
     *
     * @param $email_address
     */
    public static function remove($email_address) {
        $email_address = password_hash($email_address);
        $delete = db_delete('phylogram_datatransfer_blacklist');
        $delete->condition('email_address', $email_address, '=');
        $delete->execute();
    }
}