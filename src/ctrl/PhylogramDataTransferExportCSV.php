<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 14:35
 */

module_load_include('config', 'phylogram_datatransfer', 'phylogram_datatransfer');

/**
 * Class PhylogramDataTransferExportCSV
 */
class PhylogramDataTransferExportCSV {

  public $start;

  public $stop;

  public $mode;

  public $exclude;

  public $smallestTimeFrame;


  /**
   * PhylogramDataTransferExportCSV constructor.
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
  public function __construct(string $start, string $stop, string $mode, $exclude) {
    $this->start = $start; # To Do: If last: get from database
    $this->stop = $stop;
    $this->mode = $mode;
    $this->smallestTimeFrame = PhylogramDataTransferTime::smallestTimeFrame(phylogram_datatransfer_folder_tree);
    $this->exclude = $exclude ? $exclude : [];
  }

  /**
   * Excecute command
   *
   * @return bool on success
   */
  public function excecute(): bool {
    # get files from directory
    $filenames = scandir(PHYLOGRAM_DATATRANSFER_IMPORT_TOPIC_FOLDER);

    foreach ($filenames as $full_filename) {
      if ($full_filename[0] === '.') {
        continue;
      }
      $filename = explode('.', $full_filename);
      $filename = $filename[0];

      if (in_array($filename, $this->exclude)) {
        continue;
      }

      require_once(PHYLOGRAM_DATATRANSFER_IMPORT_TOPIC_FOLDER . '/' . $full_filename);


      # if start = last, database query, if query 0 ask $filename

      if ($this->start === 'last') {
        $last_access = PhylogramDataTransferAccessTime::getLast($filename);
        $last_access = !$last_access ? $filename::getOldestEntryTime() : $last_access;
        $start = $last_access;
      }
      else {
        $start = $this->start;
      }


      $database_topic_query = new $filename($start, $this->stop);

      $time_frames = PhylogramDataTransferTime::iterateCalenderTimeFrames($start, $this->stop, current($this->smallestTimeFrame));
      foreach ($time_frames as $time_frame) {

        # create file & write headers if new or mode = 'w'
        $folder_names = PhylogramDataTransferFolderNaming::translateTime(phylogram_datatransfer_folder_tree, $filename, $time_frame['start']);
        $folders = new PhylogramDataTransferStorage(PHYLOGRAM_DATATRANSFER_EXPORT_DATA_FOLDER, $folder_names, PHYLOGRAM_DATATRANSFER__DEFAULT_EXPORT_FILE_EXTENSION);
        $write_headers = !$folders->fileExists() || $this->mode === 'w';
        $folders->openFile($this->mode);
        if ($write_headers) {
          $header = $database_topic_query->getHeaders();
          # Do Stuff with it
          $folders->writeFile($header, PHYLOGRAM_DATATRANSFER_CSV_DELIMITER, PHYLOGRAM_DATATRANSFER_CSV_ENCLOSURE, PHYLOGRAM_DATATRANSFER_CSV_ESCAPE_CHAR);
        }

        $exclude_columns = $database_topic_query->getNameColumns();

        $database_topic_query->execute();

        foreach ($database_topic_query->fetchRow() as $row) {

          # validate
          foreach ($exclude_columns as $exclude_column) {
            $exclude = PhylogramDataTransferBlacklist::contains($row[$exclude_column]);
            if ($exclude) {
              continue;
            }
          }
          # write

          $folders->writeFile($row, PHYLOGRAM_DATATRANSFER_CSV_DELIMITER, PHYLOGRAM_DATATRANSFER_CSV_ENCLOSURE, PHYLOGRAM_DATATRANSFER_CSV_ESCAPE_CHAR);
          # Clean
          ob_flush();
          flush(); # To Do: Do more?

        }
        $folders->closeFile();
        # store if success
      }

    }
  return TRUE;
  }

}