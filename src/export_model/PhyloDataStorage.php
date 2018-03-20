<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 20.03.18
 * Time: 17:28
 */

namespace src\export_model;


class PhyloDataStorage
{
    /*
     * Check method naming and flow
     */
    public $path;
    public $file_name;
    public $file_extension;
    public $file;


    /**
     * PhyloDataStorageInterface constructor will create and store folder-path
     *
     * Will ignore named keys!
     *
     * @param array $levels array("level-nr-n as int": name, )
     * @param string $data_path absolute data path
     * @throws \Error on $data_path not existing
     */
    public function __construct(array $levels, string $data_path)
    {
        $this->path = $this->createPath($levels, $data_path);
        $this->createFolder();
    }

    /**
     * drupal_mk_dir()
     * createFolder
     * @return bool
     */
    public function createFolder(): bool
    {
        return drupal_mkdir($this->path);
    }

    /**
     * creates path for folder
     * @param $levels array
     * @param $data_path string absolute data path
     * @return string
     * @throws \Error
     */
    public function createPath(array $levels, string $data_path)
    {
        $data_path = $data_path ? $data_path[-1] === DIRECTORY_SEPARATOR : $data_path . DIRECTORY_SEPARATOR;

        if (!is_dir($data_path)) {
            throw new \Error('$data_path does not exist'); # To Do make better
        }

        $directory_levels = array();

        foreach ($levels as $level => $name) {
            if (is_numeric($level)) {
                $directory_levels[$level] = $name;
            }
        };

        $directory_levels = implode(DIRECTORY_SEPARATOR, $directory_levels);
        $path = $data_path . $directory_levels;
        return $path;
    }

    /**
     * makes File in append mode and assigns it to $this->file
     */
    public function makeFile($file_name='', $file_extension='')
    {
        if (isset($this->file)) {
            fclose($this->file);
        }
        $this->file_name = $file_name;
        $this->file_extension = $file_extension;
        $this->file = fopen($this->getFileName(), 'a');
    }

    /**
     * @param $file_name string
     * @param $file_extension
     * @return resource
     */
    public function getFile($file_name='', $file_extension='')
    {
         if (!isset($this->file)) {
             $this->makeFile($file_name, $file_extension);
         }
         return $this->file;
    }

    /**
     * @param string $folder
     * @return mixed
     */
    public function setDataExportFolder(string $folder): bool
    {
        // TODO: Implement setDataExportFolder() method.
    }

    /**
     * @param string $extension
     * @return bool
     */
    public function setDataExportFileExtension(string $extension): bool
    {
        // TODO: Implement setDataExportFileExtension() method.
    }

    public function getFileName()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->file_name . '.' . $this->file_extension;
    }

    /**
     * Checks if file exists
     *
     * @param string $file_name
     * @return bool
     */
    public function fileExists(string $file_name): bool
    {
        // TODO: Implement fileExists() method.
    }
}