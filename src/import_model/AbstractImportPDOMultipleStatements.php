<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 06.04.18
 * Time: 23:37
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportPDOMultipleStatements extends AbstractImportPDO
{

    # To Do: override _query, with arguments and tablenames!

    protected $statement_where_condition_property_stub = 'stm_where_';

    /**
     * Overrides parent method
     *
     * Creates the field list for the select statement and passes it to the right method(s).
     *
     * SELECT $fields FROM ...
     */
    protected function _createStatements() {

        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (substr($method, 0, strlen($this->create_statement_method_stub)) === $this->create_statement_method_stub) {
                $this->$method();
            }
        }
    }

    protected function _modifyRow()
    {
        $this->_getAdditionalData();
        // sort with trait
    }

    protected function _getAdditionalData() {
        $properties = get_obj_vars($this);

        foreach ($properties as $property) {
            if (substr($property, 0, strlen($this->statement_property_stub)) === $this->statement_property_stub) {
                # To Do:
                # find property $statement_where_condition_property_stub (array/string)
                # Add to query with somehow comparision to ... [sid] or else (passing?)
                # fetch and add to row [with table_names if already in row]

                # Use _query
                $query = db_query($this->$property, [
                    'start' => $this->start,
                    'stop' => $this->stop,
                ]);


            }
        }
    }



}