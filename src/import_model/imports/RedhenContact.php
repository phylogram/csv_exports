<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 03.04.18
 * Time: 15:28
 */

namespace import_model\imports;


class RedhenContact implements \Drupal\phylogram_datatransfer\import_model\ImportInterface {

	public $start;

	public $stop;

	public $fields;

	public $query;
	public $result;

	public static $oldest_entry_stm = <<<STM_OLDEST
  SELECT created
    FROM redhen_contact
ORDER BY created ASC
   LIMIT 1;
STM_OLDEST;


	/**
	 * ImportInterface constructor.
	 *
	 * @param string $start TimeString
	 * @param string $stop TimeString
	 */
	public function __construct( string $start, string $stop, array $fields ) { # To Do: Add fields
		$dt1         = new \DateTime( $start );
		$this->start = $dt1->getTimestamp();
		$dt2         = new \DateTime( $stop );
		$this->stop  = $dt2->getTimestamp();

		$this->fields = array_combine( array_column( $fields, 'export_name' ), array_column( $fields, 'import_name' ) );

		$this->query = new \EntityFieldQuery();
		$this->query->entityCondition( 'entity_type', 'redhen_contact' );
		$this->query->propertyCondition( 'created', $this->start, '>' );
		$this->query->propertyCondition( 'created', $this->stop, '<' );
	}


	public function execute() {
		$this->result = $this->query->execute();
	}

	/**
	 * Like db-fetchAssoc
	 *
	 * @yield array
	 */
	public function fetchRow() {
		foreach ( array_keys( $this->result['redhen_contact'] ) as $id ) {
			$entity = entity_load( 'redhen_contact', $id );
			if ( ! $entity ) {
				continue;
			}
			//$entity = [id => data]. id is also in data
			$entity = current( $entity );


			// Order the array and get rid of nesting
			$ordered_row = [];
			foreach ( $this->fields as $field ) {
				// Check if deeper value is needed
				if ( is_array( $field ) ) {
					$value = $entity;
					// %CURRENT% will be replaced with current() to avoid errors due to unknown keys
					foreach ( $field as $key_level ) {
						if ( ! $value ) {
							$value = NULL;
							continue;
						}
						$value = $key_level === '%CURRENT%' ? current( $value ) : $value[ $key_level ];
					}
				} else {
					$value = $entity[ $field ];
				}

				$ordered_row[ $field ] = $value;
			}
			yield $ordered_row;
			cache_clear_all();
		}
	}

	/**
	 * Oldest entry in main table concerning topic
	 *
	 * @return string
	 */
	public static function getOldestEntryTime() {
		$query      = db_query( self::$oldest_entry_stm );
		$unix_tmstp = $query->fetchField();
		$dt         = new \Datetime();
		$dt->setTimestamp( $unix_tmstp );
		$string = $dt->format( 'Y-m-d' );

		return $string;
	}

}