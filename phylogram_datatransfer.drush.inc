<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 27.03.18
 * Time: 12:56
 */

module_load_include('config', 'phylogram_datatransfer', 'phylogram_datatransfer');

/**
 * Implements hook_drush_command().
 */
function phylogram_datatransfer_drush_command() {

  $available_topics = [];

  foreach (scandir(PHYLOGRAM_DATATRANSFER_IMPORT_TOPIC_FOLDER) as $file) {
    if ($file[0] === '.') {
      continue;
    }
    $topic = explode('.', $file);
    $available_topics[] = current($topic);
  }

  $available_topics = implode(', ', $available_topics);


  $items['export-csv'] = [
    'description' => 'Exports various data from campaignion to csv and stores them in custom folder structure',
    'options' => [
      'start' => 'Date/Time to start. Default: See config. Use php understandable time strings (See http://php.net/manual/de/datetime.construct.php)',
      'stop' => 'Date/Time to stop. Default: See config. Use php understandable time strings (See http://php.net/manual/de/datetime.construct.php)',
      'mode' => 'How to open and write to new lines. Default: See config. Use a to append to and w to overwrite files (See http://php.net/manual/de/function.fopen.php)',
      'exclude' => "List of topics to exclude. Separate with whitespace and surround with quoation marks. Availble topics are:\n$available_topics",
    ],
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_DATABASE,     # To Do: Check
  ];

  return $items;
}


/**
 * Implements drush_hook_COMMAND_validate().
 *
 * @hook validate
 */
function drush_phylogram_datatransfer_export_csv_validate() {
  $start = drush_get_option('start', $default = PHYLOGHRAM_DATATRANSFER_DEFAULT_START_EXPORT);
  if ($start !== 'last') {
    try {
      strtotime($start);
    } catch (Exception $error) {
      return drush_set_error($error, "Please use 'last' or php time string");   # To Do. Test
    }
  }

  $stop = drush_get_option('stop', $default = PHYLOGRAM_DATATRANSFER__DEFAULT_END_EXPORT);
  try {
    strtotime($stop);
  } catch (Exception $error) {
    return drush_set_error($error, "Please use php time string");
  }

  $mode = drush_get_option('mode', $default = PHYLOGRAM_DATATRANSFER__DEFAULT_FILE_MODE);
  if ($mode !== 'a' && $mode !== 'w') {
    return drush_set_error('WRONG FILE MODE', 'Please use file mode "a" for appending to files, or "w" for overwriting files');
  }
}

/**
 * Implements drush_COMMANDFILE_COMMANDNAME().
 */
function drush_phylogram_datatransfer_export_csv() {

  $start = drush_get_option('start', $default = PHYLOGHRAM_DATATRANSFER_DEFAULT_START_EXPORT);
  $stop = drush_get_option('stop', $default = PHYLOGRAM_DATATRANSFER__DEFAULT_END_EXPORT);
  $mode = drush_get_option('mode', $default = PHYLOGRAM_DATATRANSFER__DEFAULT_FILE_MODE);

  $exclude = drush_get_option('exclude');
  if ($exclude) {
    $exclude = str_replace('""', '', $exclude); # To Do: necessary?
    $exclude = str_replace("'", '', $exclude); # To Do: necessary?
    $exclude = explode(' ', $exclude);
  }
  $phylogram_datatransfer_export_csv = new \Drupal\phylogram_datatransfer\ctrl\ExportCSV($start, $stop, $mode, $exclude);
  $success = $phylogram_datatransfer_export_csv->excecute();

}

;