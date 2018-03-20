<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 14.03.18
 * Time: 15:11
 */

namespace src;

/**
 * Interface PhyloDataStorageInterface
 * handles files and folder structure
 * @package src
 */
interface PhyloDataStorageInterface {

	/**
	 * PhyloDataStorageInterface constructor will store folder-path
	 *
	 * Will ignore named keys!
	 *
	 * @param array $levels array("level-nr-n as int": name, )
	 */
	public function __construct(array $levels);

	/**
	 * Sets the file path up
	 * if !folderExists() createFolder
	 * @return bool
	 */
	public function initFolder(): bool;

	/**
	 * if !fileExists createFile
	 *
	 * @return resource|bool
	 */
	public function initFile();


	/**
	 * Check if folder exists in config: PHYLO_DATATRANSFER_STORAGE_PATH
	 *
	 * @return bool
	 */
	public function folderExists(): bool;

	/**
	 * creates Folder
	 *
	 * @return bool
	 */
	public function createFolder(): bool;

	/**
	 * deletes folder of object and object ...
	 *
	 * @return bool
	 */
	public function deleteFolder(): bool;


	/**
	 * Checks if file exists
	 *
	 *
	 * @return bool
	 */
	public function fileExists(string $file_name): bool;

	/**
	 * Creates file
	 *
	 * @param string $fil_name
	 *
	 * @return bool
	 */
	public function createFile(string $fil_name): bool;

	/**
	 * Deletes current file
	 *
	 * @return bool
	 */
	public function deleteFile(): bool;



}