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

    protected $emails = array();

    public function _construct() {
        $stm = <<<STM
          SELECT phylogram_datatransfer_blacklist.email_address
            FROM phylogram_datatransfer_blacklist
STM;

        $query  = db_query( $stm);
        $this->emails = $query->fetchCol();
    }

    /**
     * Checks if a email address is in blacklist
     *
	 * @param string $email_address
	 * @return  bool
	 */
	public function contains( string $email_address ) {

		foreach ($this->emails as $hash) {
		    $is_in = password_verify($email_address, $hash);
		    if ($is_in) {
		        return TRUE;
            }
        }
		return FALSE;
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

}