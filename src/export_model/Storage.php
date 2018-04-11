<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 20.03.18
 * Time: 17:28
 */

namespace Drupal\phylogram_datatransfer\export_model;


class Storage {

	protected $data_path = '.';

	protected $path_array = [];

	protected $file_name = '';

	protected $folder_array = [];

	protected $file_extension = '';

	protected $folder_path = '';

	protected $full_file_name = '';

	protected $file;

    /**
     * Storage constructor.
     * @param string $data_path This is where data is stored
     * @param array $data_folders This is how data should be stored, eg [data/]year/month/
     * @param string $file_extension
     */
	public function __construct( string $data_path, array $data_folders, string $file_extension = 'csv' ) {

		$this->data_path      = substr( $data_path, - 1 ) === DIRECTORY_SEPARATOR ? $data_path : $data_path . DIRECTORY_SEPARATOR;
		$this->path_array     = $data_folders;
		$this->file_name      = array_pop( $data_folders );
		$this->folder_array   = $data_folders;
		$this->file_extension = $file_extension;
		$this->folder_path    = $this->data_path . implode( DIRECTORY_SEPARATOR, $this->folder_array ) . DIRECTORY_SEPARATOR;

		# due to bug: drupal_mkdir does not set permissions to directories it created recursively https://www.drupal.org/project/drupal/issues/1068266
		$path = $data_path;

		foreach ( $this->folder_array as $folder ) {
			$path .= DIRECTORY_SEPARATOR;
			$path .= $folder;
			if ( ! is_dir( $path ) ) {
				drupal_mkdir( $path );
			}
		}

		$this->full_file_name = $this->folder_path . $this->file_name . '.' . $this->file_extension;

	}

	public function fileExists() {
		return file_exists( $this->full_file_name );
	}

	public function closeFile() {
		fclose( $this->file );
	}

	public function openFile( string $mode ) {

		$this->file = fopen( $this->full_file_name, $mode );

	}

	/**
	 * @return bool|resource
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * fputcsv()
	 *
	 * @param array $row
	 * @param string $delimiter
	 * @param string $enclosure
	 *
	 * @return bool|int
	 */
	public function writeFile( array $row, string $delimiter, string $enclosure, string $escape_char ) {

		return fputcsv( $this->file, $row, $delimiter, $enclosure, $escape_char );

	}


}