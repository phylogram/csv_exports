<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 20.03.18
 * Time: 19:31
 */

namespace src\export_model;


class FolderNaming
{
    static public function translateTime(array $levels): array
    {
        $translated_levels = array();

        foreach ($levels as $level => $name) {
            if (is_numeric($level)) {
                $translated_levels[$level] = date($name);
            } else {
                $translated_levels[$level] = $name;
            }

        }
    }
}
