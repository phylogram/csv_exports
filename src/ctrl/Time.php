<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 14:47
 */

namespace Drupal\phylogram_datatransfer\ctrl;

class Time {

	public static $time_string_conversions = [
		# Days
		'd' => 'day',
		'j' => 'day',
		'l' => 'day',
		'z' => 'day',
		# Months
		'm' => 'month',
		'n' => 'month',
		'f' => 'month', # F will be downsized to f
		# Year
		'y' => 'year',
		# Time,
		'g' => 'hour', # Hours
		'h' => 'hour',
		# Less will be ignored (for now)
	];

	public static $time_sizes = [
		'hour'  => 1,
		'day'   => 2,
		'month' => 3,
		'year'  => 4,
	];

	public static function iterateCalenderTimeFrames( $start, $stop, string $frequency ) {

		$start_frame = new \DateObject( $start );
		$end_frame   = clone( $start_frame );
		$end_frame->modify( "first day of next $frequency" ); # To Do: If less then day -> timezone not found in database error

		$stop_date = $stop;


		while ( $end_frame->getTimestamp() < $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $end_frame ];
			$start_frame->modify( "first minute of next $frequency" );
			$end_frame->modify( "first minute of next $frequency" );
		}

		# if there is a fraction of a month left
		if ( $end_frame->getTimestamp() > $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $stop_date ];
		}
	}
}