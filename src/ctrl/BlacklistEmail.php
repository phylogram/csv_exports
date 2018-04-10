<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 10.04.18
 * Time: 14:34
 */

namespace Drupal\phylogram_datatransfer\ctrl;


class BlacklistEmail
{
    static function add(string $email) {
        \Drupal\phylogram_datatransfer\import_model\Blacklist::insert($email);
    }

    static function remove(string $email) {
        Drupal\phylogram_datatransfer\import_model\Blacklist::remove($email);
    }
}