<?php

/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 14:35
 */

namespace Drupal\phylogram_datatransfer\ctrl;

module_load_include( 'config', 'phylogram_datatransfer', 'phylogram_datatransfer' );

/**
 * Class ExportCSV
 */
class ExportCSV {

	public $start;

	public $stop;

	public $mode;

	public $exclude;

	public $frequency;
	public $settings;

	/**
	 * ExportCSV constructor.
	 *
	 * @param string $start 'Date/Time to start. Use php understandable time
	 *   strings (See http://php.net/manual/de/datetime.construct.php)'
	 * @param string $stop 'Date/Time to stop. Use php understandable time
	 *   strings (See http://php.net/manual/de/datetime.construct.php)'
	 * @param string $mode How to open and write to new lines. Default: See
	 *   config. Use a to append to and w to overwrite files (See
	 *   http://php.net/manual/de/function.fopen.php)
	 * @param array||NULL $exclude List of topics to exclude. Separate with
	 *   whitespace and surround with quoation marks.
	 */
	public function __construct( $start, $stop, string $mode, $exclude ) {
		$this->start = $start ? new \DateObject($start): $start;
		$this->stop  = new \DateObject($stop);
		$this->mode  = $mode;
		$this->settings = new TransferSettings();
		$this->exclude = $exclude ? $exclude : [];
	}

	/**
	 * Excecute command
	 *
	 * @return bool on success
	 */
	public function execute() {
		// Get settings
		$settings = $this->settings->iterateSettings();

		foreach ( $settings as $topic => $setting ) {

			$class = $setting['class'];
			if (array_search($class, $this->exclude) !== FALSE
                || array_search($topic, $this->exclude) !== FALSE) {
			    continue;
            }

			if ( !$this->start) {
				$last_access = \Drupal\phylogram_datatransfer\import_model\AccessTime::getLast( $class );
				$last_access = ! $last_access->getTimeStamp() ? $class::getOldestEntryTime() : $last_access;
                $start = $last_access;
			} else {
				$start = $this->start;

			}
			$time_frames = \Drupal\phylogram_datatransfer\ctrl\Time::iterateCalenderTimeFrames( $start, $this->stop, $setting['frequency'] );

			foreach ( $time_frames as $time_frame ) {
				# create file & write headers if new or mode = 'w'
				$this_time    = $time_frame['start'];
				$folder_names = \Drupal\phylogram_datatransfer\export_model\FolderNaming::translateTime( $setting['folder_structure'], $setting['file_name'], FALSE, $this_time->getTimestamp() );
				$folders      = new \Drupal\phylogram_datatransfer\export_model\Storage( PHYLOGRAM_DATATRANSFER_EXPORT_DATA_FOLDER, $folder_names, $setting['file_extension'] );

				$write_headers = ! $folders->fileExists() || $this->mode === 'w'; # To Do: if file exists with no headers (eg due to an error), no headers, will be written
				$folders->openFile( $this->mode );
                $database_topic_query = new $class( $time_frame['start'], $time_frame['stop'], $setting['fields'] );

                if ( $write_headers ) {
					$header = $database_topic_query->getExportNames();;
					# Do Stuff with it
					$folders->writeFile( $header, PHYLOGRAM_DATATRANSFER_CSV_DELIMITER, PHYLOGRAM_DATATRANSFER_CSV_ENCLOSURE, PHYLOGRAM_DATATRANSFER_CSV_ESCAPE_CHAR );
				}

				$database_topic_query->execute();
				foreach ( $database_topic_query->fetchRow() as $row ) {
					# validate
					foreach ( $row as $column ) {
						$exclude = \Drupal\phylogram_datatransfer\import_model\Blacklist::contains( $row[ $column ] );
						if ( $exclude ) {
							continue;
						}
					}
					# write

					$folders->writeFile( $row, PHYLOGRAM_DATATRANSFER_CSV_DELIMITER, PHYLOGRAM_DATATRANSFER_CSV_ENCLOSURE, PHYLOGRAM_DATATRANSFER_CSV_ESCAPE_CHAR );


				}
				$folders->closeFile();
				# store if success
				\Drupal\phylogram_datatransfer\import_model\AccessTime::setLast( $class, $time_frame['stop'] );
			}

		}

		return TRUE;
	}

}