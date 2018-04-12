<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 19:59
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Class AccessTime
 *
 * When is teh last time, we exported data for a topic? And store the time, when we were successful.
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
class AccessTime {

	/**
     * Retrieves last access time
     *
	 * @param string $topic
	 *
	 * @return  $string || NULL
	 */
	public static function getLast( string $topic ) {
		# if not in database return null and ask topic
		$stm = <<<STM
          SELECT phylogram_datatransfer_export_time.access
            FROM phylogram_datatransfer_export_time
           WHERE phylogram_datatransfer_export_time.topic = :topic
        ORDER BY phylogram_datatransfer_export_time.access DESC
           LIMIT 1;
STM;

		$query  = db_query( $stm, [ ':topic' => $topic ] );
		$result = $query->fetchField();
        $result = new \DateObject($result);
		return $result;
	}

    /**
     * Stores access time
     *
     * @param string $topic
     * @param $DateTime
     */
	public static function setLast( string $topic, $DateTime ) {
	    $timestamp = $DateTime->getTimestamp();
		$table = db_insert( 'phylogram_datatransfer_export_time' );
		$table->fields( [
			'topic'  => $topic,
			'access' => $timestamp,
		] );
		$table->execute();
	}
}