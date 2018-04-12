<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 14:47
 */

namespace Drupal\phylogram_datatransfer\ctrl;

class Time {

    /**
     * Iterates through time in calender style: Year and month will not be in 365 or 30 days, it will be the first day
     * of the next year/month. All other units will behave as absolute values.
     *
     * @param  \DateObject $start
     * @param  \DateObject $stop
     * @param string $frequency Valid units can be found @link http://php.net/manual/de/datetime.formats.relative.php here. @endlink
     * @return \Generator
     */
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

    /**
     * Create the modification string
     *
     * @param $frequency
     * @return string
     */
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