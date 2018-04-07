<?php

/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 20.03.18
 * Time: 19:31
 */

namespace Drupal\phylogram_datatransfer\export_model;


class FolderNaming {

	/**
	 * replaces current time in array('time' => stuff)
	 *
	 * @param array $levels
	 * @param $timestamp  unix timestamp – if not given time()
	 *
	 * @return array
	 */
	static public function translateTime( string $folder_structure, string $topic, $timestamp = NULL ) {
		$timestamp   = ! $timestamp ? time() : $timestamp;
		$translation = date( $folder_structure, $timestamp );
		$translation = substr( $translation, - 1 ) === DIRECTORY_SEPARATOR ? $translation : $translation . DIRECTORY_SEPARATOR;
		$translation = $translation[0] === DIRECTORY_SEPARATOR ? substr( $translation, 1 ) : $translation;
		$translation .= $topic;
		$translation = explode( DIRECTORY_SEPARATOR, $translation );

		return $translation;
	}
}
