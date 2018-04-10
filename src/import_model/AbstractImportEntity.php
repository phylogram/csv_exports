<?php
/**
 * Created by PhpStorm.
 * User: phylogram
 * Date: 10.04.18
 * Time: 11:29
 */

namespace Drupal\phylogram_datatransfer\import_model;


abstract class AbstractImportEntity extends \Drupal\phylogram_datatransfer\import_model\AbstractImport
{
    use   \Drupal\phylogram_datatransfer\ctrl\SortFieldsTrait;

    public $result;
    public $sort_fields;
    public $entity_type;

    public function execute() {
        $this->result = $this->query->execute();
    }

    public function fetchRow()
    {
        parent::fetchRow();
        cache_clear_all();
    }

    protected function _modifyRow()
    {
        $this->row = $this->sortFields($this->row, $this->sort_fields);
    }

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
     * @yield array
     */
    protected function _getRow() {
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