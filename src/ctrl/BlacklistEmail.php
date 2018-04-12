<?php
/**
 * @class BlacklistEmail The blacklist command
 *
 * Created by PhpStorm.
 * User: phylogram
 * Date: 10.04.18
 * Time: 14:34
 */

namespace Drupal\phylogram_datatransfer\ctrl;


class BlacklistEmail
{
    /**
     * Add an email-address to the blacklist. These Email-adresses will not be exported.
     * @param string $email
     */
    static function add(string $email) {
        \Drupal\phylogram_datatransfer\import_model\Blacklist::insert($email);
    }

    /**
     * Remove an email-address from the blacklist. This addresses will be exported. Old data will have to be exported
     * again.
     * @param string $email
     */
    static function remove(string $email) {
        Drupal\phylogram_datatransfer\import_model\Blacklist::remove($email);
    }
}