<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 12.04.18
 * Time: 20:21
 */

namespace Drupal\phylogram_datatransfer\export_model;

class RemoveRows
{
    /**
     * @var array of criterias to match against with ===
     */
    public $criteria = array();

    /**
     * @var resource temp file, wo write data in between
     */
    public $temp_file_handler;


    /**
     * @var resource
     */
    public $current_file_handler;

    /**
     * @var string The data path to search for
     */
    public $path;

    /**
     * @var string only change files that end with ...
     */
    public $file_ending;

    /**
     * @var \RecursiveIteratorIterator
     */
    public $folder_iterator;

    public $delimiter;
    public $enclosure;
    public $escape;


    public function __construct(string $path, array $criteria, string $delimiter, string $enclosure, string $escape, string $file_ending = 'csv')
    {
        $this->path = $path;
        $this->folder_iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $this->criteria = $criteria;
        $this->file_ending = $file_ending;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    public function execute() {
        foreach ($this->folder_iterator as $file) {
            if (substr($file, count($this->file_ending) * -1)===$this->file_ending) {
                $this->temp_file_handler = tmpfile();
                $this->current_file_handler = fopen($file, 'r');
                while ($row = fgetcsv($this->current_file_handler,
                        $delimiter = $this->delimiter,
                        $enclosure = $this->enclosure,
                        $escape = $this->escape
                    )) {
                    foreach ($row as $field) {
                        if (array_search($field, $this->criteria) !== FALSE) {
                            continue;
                        }
                    }

                    fputcsv($this->temp_file_handler, $row,
                        $delimiter = $this->delimiter,
                        $enclosure = $this->enclosure,
                        $escape = $this->escape
                    );
                }

                fclose($this->current_file_handler);
                $this->current_file_handler = fopen($file, 'w');

                while ($row = fgetcsv($this->temp_file_handler,
                    $delimiter = $this->delimiter,
                    $enclosure = $this->enclosure,
                    $escape = $this->escape
                )) {
                    fputcsv($this->current_file_handler, $row,
                        $delimiter = $this->delimiter,
                        $enclosure = $this->enclosure,
                        $escape = $this->escape
                    );
                }

                fclose($this->current_file_handler);
                fclose($this->temp_file_handler);
            }
        }
    }

}