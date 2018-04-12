<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 20:16
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Interface ImportInterface
 *
 * What all classes in import_model\imports\ should be able to do!
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
interface ImportInterface {

	/**
	 * ImportInterface constructor.
	 *
	 * @param \DateTime $start from
	 * @param \DateTime $stop to
     * @param array $fields array(array('export_name' => 'Is Draft', 'import_name' => 'some_table.is_draft'), array(...), ...)
	 *
	 */
	public function __construct($start, $stop, array $fields );

	/**
	 * Actually starts the main query.
	 *
	 * @return mixed
	 */
	public function execute();

	/**
	 * Like db-fetch.
	 *
	 * @return array
	 */
	public function fetchRow();

	/**
	 * Oldest entry in main table concerning topic.
	 *
	 * @return string
	 */
	public static function getOldestEntryTime();

    /**
     * Get the headers for csv
     * 
     * @return mixed
     */
	public function getExportNames();
}