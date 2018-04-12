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

    /**
     * @return string
     */
    public function getDataPath(): string
    {
        return $this->data_path;
    }

    /**
     * @param string $data_path
     */
    public function setDataPath(string $data_path)
    {
        $this->data_path = $data_path;
    }

    /**
     * @return mixed|string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @param mixed|string $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->file_extension;
    }

    /**
     * @param string $file_extension
     */
    public function setFileExtension(string $file_extension)
    {
        $this->file_extension = $file_extension;
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return $this->folder_path;
    }

    /**
     * @param string $folder_path
     */
    public function setFolderPath(string $folder_path)
    {
        $this->folder_path = $folder_path;
    }

    /**
     * @return string
     */
    public function getFullFileName(): string
    {
        return $this->full_file_name;
    }

    /**
     * @param string $full_file_name
     */
    public function setFullFileName(string $full_file_name)
    {
        $this->full_file_name = $full_file_name;
    }

	


}