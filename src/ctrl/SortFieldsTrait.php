<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 07.04.18
 * Time: 15:19
 */

namespace Drupal\phylogram_datatransfer\ctrl;


trait SortFieldsTrait
{

    public $sort_fields = array();
    /**
     * Sort $fields by $keys
     *
     * @param array $fields array [any => $value, ...] to sort
     * @param array $keys array ['output_name' => [$key_name, $alt_key_name, ...],'on' => [$kn, $akn, ...], ...] to sort by
     *
     * @return array ['output_name' => 'value', ...]
     */
    public function sortFields(array $fields, array $keys)
    {
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

}
