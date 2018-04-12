<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 10.04.18
 * Time: 11:29
 */

namespace Drupal\phylogram_datatransfer\import_model;

/**
 * Class AbstractImportEntity
 *
 * Parent class for executing simple EntityAPI queries
 *
 * @package Drupal\phylogram_datatransfer\import_model
 */
abstract class AbstractImportEntity extends \Drupal\phylogram_datatransfer\import_model\AbstractImport
{
    use   \Drupal\phylogram_datatransfer\ctrl\SortFieldsTrait;

    /**
     * @var The result of the main query
     */
    public $result;

    /**
     * @var str $entity_type to query against
     */
    public $entity_type;

    /**
     * @var int counts the loaded entities
     */
    public $row_number = 0;

    public function execute() {
        $this->result = $this->query->execute();
    }

    /**
     * Overwrites parent with foreach instead of while and cleans drupal cache in loop
     *
     * @return array|\Generator
     */
    public function fetchRow()
    {
        $rows = $this->_getRow();
        foreach ( $rows as $this->row_number => $this->row ) {
            $this->_modifyRow();
            yield $this->row;
        }

        cache_clear_all();
    }

    /**
     * Calls sort fields each loop
     *
     * @return bool|void
     */
    protected function _modifyRow()
    {
        $this->row = $this->sortFields($this->row, $this->sort_fields);
    }

    /**
     * Creates fields and makes them sortable
     *
     * @param array $fields
     */
    protected function _createFields(array $fields)
    {
        parent::_createFields($fields);
        $this->sort_fields = [];
        // Reorder data, to keep sortFields() simple
        foreach ($this->fields as $export_name => $import_name) {
            $this->sort_fields[$export_name] = [$import_name];
        }
    }



    /**
     * Like db-fetchAssoc
     *
     * Returns data each loop with entity_load
     *
     * @yield array
     */
    protected function _getRow() {
        if (!$this->result) {
            return NULL;
        }
        foreach ( array_keys( $this->result[$this->entity_type] ) as $id ) {
            $entity = entity_load( $this->entity_type, $id );
            if ( ! $entity ) {
                continue;
            }
            //$entity = [id => data]. id is also in data
            yield current( $entity );
        }
    }



    protected function _query() {
        $this->query = new \EntityFieldQuery();
        $this->query->entityCondition( 'entity_type', $this->entity_type );
        $this->query->propertyCondition( 'created', $this->start, '>' );
        $this->query->propertyCondition( 'created', $this->stop, '<' );
    }

}