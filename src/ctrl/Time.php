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
		$start_frame = clone($start);
		$end_frame   = clone( $start_frame );

        $modification = static::_getModificationString($frequency);
		$end_frame->modify($modification); # To Do: If less then day -> timezone not found in database error

		$stop_date = $stop;


		while ( $end_frame->getTimestamp() < $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $end_frame ];
			$start_frame->modify( $modification );
			$end_frame->modify( $modification );
		}

		# if there is a fraction of a month left
		if ( $end_frame->getTimestamp() > $stop_date->getTimestamp() ) {
			yield [ 'start' => $start_frame, 'stop' => $stop_date ];
		}
	}

	protected static function _getModificationString($frequency) {
        // Taking Care of calender style iterating
        if (
            $frequency == 'month'
            || $frequency == 'year'
        ) {
            $modification = 'first day of next';
        } else {
            $modification = '';
        }
        $modification .= "next $frequency";
        return $modification;
    }
}