<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 07.04.18
 * Time: 15:19
 */

namespace Drupal\phylogram_datatransfer\ctrl;


trait SortFieldsTrait {

  /**
   * @var array For sorting fields with multiple aliases [['Draft' =>
   *   ['is_draft', 'submitted.is_draft', 'if(submitted.is_draft, 'y', 'n'],
   *   ...]
   */
  public $sort_fields = [];

  /**
   * Sort $fields by $keys
   *
   * @param array $fields array [any => $value, ...] to sort
   * @param array $keys array ['output_name' => [$key_name, $alt_key_name,
   *   ...],'on' => [$kn, $akn, ...], ...] to sort by
   *
   * @return array ['output_name' => 'value', ...]
   */
  public function sortFields($fields, array $keys) {
    $sorted_fields = [];
    foreach ($keys as $output_name => $key_synonyms) {
      $value = NULL;
      foreach ($key_synonyms as $key_synonym) {
        if (array_key_exists($key_synonym, $fields)) {
          $value = $fields['key_synonym'];
          break;
        }
      }
      $sorted_fields[$output_name] = $value;
    }

    return $sorted_fields;
  }

  /**
   * Sort end flattens field arrays
   *
   * @param $fields $fields array [any => $value, ...] to sort
   * @param array $keys
   *
   * @return array
   */
  public function sortNestedFields($fields, array $keys) {
    $sorted_fields = [];
    $fields = (array) $fields;


    foreach ($keys as $key => $nested_keys) {
      if (is_array($nested_keys)) {
        $value = $fields;
        foreach ($nested_keys as $nested_key) {
          if (!$value) {
            $value = NULL;
            continue;
          }
          $value = $nested_key === '%CURRENT%' ? current($value) : $value[$nested_key];
        }
        $sorted_fields[$key] = $value;

      }
      else {
        $sorted_fields[$nested_keys] = array_key_exists($nested_keys, $fields) ? $fields[$nested_keys] : NULL;
      }
    }
    return $sorted_fields;
  }

}
