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


	/**
	 * Looks the smallest time frame in config array
	 *
	 * @param array $levels from config
	 *
	 * @return array ['level => time-frame-string]
	 */
	public static function smallestTimeFrame( array $levels ) {

		# Filter non time data
		$time_values = [];
		foreach ( $levels as $level => $entry ) {
			if ( key( $entry ) === 'time' ) {
				$time_values[ $level ] = current( $entry );

			}
		}

		# Find smallest frame
		$smallest_frame_key   = NULL;
		$smallest_frame_level = NULL;
		$smallest_frame       = '';

		foreach ( $time_values as $level => $time_value ) {
			$time_value              = strtolower( $time_value );
			$this_smallest_frame_key = NULL;
			$this_smallest_frame     = '';
			# Find in each string
			foreach ( self::$time_string_conversions as $search => $replace ) {

				if ( strpos( $time_value, $search ) !== FALSE ) {

					$this_value = self::$time_sizes[ $replace ];
					if ( ! $this_smallest_frame_key || $this_value < $this_smallest_frame_key ) {
						$this_smallest_frame_key = $this_value;
						$this_smallest_frame     = $replace;
					}
				}
			}
			if ( ! $smallest_frame_key || $this_smallest_frame_key < $smallest_frame_key ) {
				$smallest_frame_key   = $this_smallest_frame_key;
				$smallest_frame_level = $level;
				$smallest_frame       = $this_smallest_frame;
			}
		}

		if ( $smallest_frame_level ) {
			return [ $smallest_frame_level => $smallest_frame ];
		} else {
			return NULL;  # To Do: Throw Error?
		}
	}

	public static function iterateCalenderTimeFrames( string $start, string $stop, string $smallest_time_frame ) {

		$start_frame = new \DateTime( $start );
		$end_frame   = clone( $start_frame );
		$end_frame->modify( "first day of next $smallest_time_frame" ); # To Do: If less then day -> timezone not found in database error

		$stop_date = new \DateTime( $stop );


		while ( $end_frame->getTimestamp() < $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $end_frame ];
			$start_frame->modify( "first minute of next $smallest_time_frame" );
			$end_frame->modify( "first minute of next $smallest_time_frame" );
		}

		# if there is a fraction of a month left
		if ( $end_frame->getTimestamp() > $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $stop_date ];
		}
	}
}