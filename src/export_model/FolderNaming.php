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

	/**
	 * replaces current time in array('time' => stuff)
	 * @param array $levels
	 * @param $timestamp  unix timestamp – if not given time()
	 * @return array
	 */
    static public function translateTime(array $levels, $timestamp=NULL): array
    {
        $translated_levels = array();
		$timestamp = time() ? !$timestamp : $timestamp;
        foreach ($levels as $level => $array) {
			if (array_key_exists('time', $array)) {
				$translated_levels[$level] = date($array['time'], $timestamp);
			} else {
				$translated_levels[$level] = current($array);
			}
        }
    }
}
