<?php
/**
 * Created by PhpStorm.
 * User: phylogram – Philip Röggla
 * Date: 31.03.18
 * Time: 13:33
 */

namespace Drupal\phylogram_datatransfer\ctrl;

/**
 * Class TransferSettings
 *
 * Configures settings default and concrete topics like folder structure/naming,
 * frequency, input columns, output headers
 *
 * @package Drupal\phylogram_datatransfer
 */
class TransferSettings {

	/**
	 * Defines the default folder structure. Valid identifiers will be replaced
	 * with @link http://php.net/manual/de/book.datetime.php php DateTime.
	 * @endlink
	 *
	 * The folders will be created in the default data folder found in
	 * phylogram_datatransfer.config.
	 *
	 * The replacement will take the beginning of a period as argument. See
	 * examples below.
	 *
	 * A list of valid identifiers is listed
	 *
	 * @link http://php.net/manual/de/datetime.formats.date.php here (date)
	 *     @endlink and @link
	 *     http://php.net/manual/de/datetime.formats.time.php here (time).
	 *     @endlink
	 *
	 * @example '/Y/m/' for the period Jan, 2017 will lead to
	 *     $data_folder/2017/01
	 * @example '/y/M/D' for the period from Oct, 2017 to Oct 2018 will lead to
	 *     $data_folder/17/Oct/Sun
	 *
	 *
	 * @var string
	 */
	protected $default_folder_structure = '/Y/m/';

	/**
	 * Defines the default file name
	 *
	 * Using the default file names for content with different headers may lead
	 * to to unexpected results.
	 *
	 * @var string
	 */
	protected $default_file_name = 'export';

	/**
	 * The default file extension will be added after a period ".".
	 *
	 * @var string
	 */
	protected $default_file_extension = 'csv';

	/**
	 * The smallest period in that data will be junked. Best to be the deepest
	 * and shortest time span in the folder structure.
	 *
	 * For Parsing again the @link http://php.net/manual/de/book.datetime.php
	 * php DateTime Library @endlink is used. The following string is used, to
	 * modify the end of the period: @code $start->modify('first day of next
	 * $default_frequenycy'
	 *
	 * @endcode
	 *
	 * Valid units can be found
	 * @link http://php.net/manual/de/datetime.formats.relative.php here.
	 *     @endlink
	 *
	 * At the time written, this units where availible:
	 * 'sec' | 'second' | 'min' | 'minute' | 'hour' | 'day' | 'fortnight' |
	 *     'forthnight' | 'month' | 'year'
	 *
	 * @var string
	 */
	protected $default_frequency = 'month';


	/**
	 * ['name'] => settings
	 *
	 * Use 'default' for class defaults
	 *
	 * @var array
	 */
	public $settings = [
		'Donations'       => [
			'class'            => '\Drupal\phylogram_datatransfer\import_model\imports\Donation',
			'folder_structure' => 'default',
			'file_name'        => 'Donations',
			'file_extension'   => 'default',
			'frequency'        => 'default',
			'fields'           => [
				// Please provide table_name.field_name as import_name
				0  => [
					'export_name' => 'nid',
					'import_name' => 'webform_submissions.nid',
				],
				1  => [
					'export_name' => 'sid',
					'import_name' => 'webform_submissions.sid',
				],
				2  => [
					'export_name' => 'Draft',
					'import_name' => 'if(webform_submissions.is_draft, \'Yes\', \'No\')',
				],
				3  => [
					'export_name' => 'Submitted',
					'import_name' => 'from_unixtime(webform_submissions.submitted)',
				],
				4  => [
					'export_name' => 'Modified',
					'import_name' => 'from_unixtime(webform_submissions.modified)',
				],
				5  => [
					'export_name' => 'Completed',
					'import_name' => 'from_unixtime(webform_submissions.completed)',
				],
				6  => [
					'export_name' => 'Remote Adress',
					'import_name' => 'webform_submissions.remote_addr',
				],
				7  => [
					'export_name' => 'External Referer',
					'import_name' => 'webform_tracking.external_referer',
				],
				8  => [
					'export_name' => 'Referer',
					'import_name' => 'webform_tracking.referer',
				],
				9  => [
					'export_name' => 'Source',
					'import_name' => 'webform_tracking.source',
				],
				10 => [
					'export_name' => 'Medium',
					'import_name' => 'webform_tracking.medium',
				],
				11 => [
					'export_name' => 'Version',
					'import_name' => 'webform_tracking.version',
				],
				12 => [
					'export_name' => 'Other',
					'import_name' => 'webform_tracking.other',
				],
				13 => [
					'export_name' => 'Country',
					'import_name' => 'webform_tracking.country',
				],
				14 => [
					'export_name' => 'Term',
					'import_name' => 'webform_tracking.term',
				],
				15 => [
					'export_name' => 'Campaign',
					'import_name' => 'webform_tracking.campaign',
				],
				16 => [
					'export_name' => 'Payment status',
					'import_name' => 'payment_status_item.status',
				],
			],
		],
		'Activity'        => [
			'class'            => '\Drupal\phylogram_datatransfer\import_model\imports\Activity',
			'folder_structure' => 'default',
			'file_name'        => 'Activities',
			'file_extension'   => 'default',
			'frequency'        => 'default',
			'fields'           => [
				// Please provide table_name.field_name as import_name
				0 => [
					'export_name' => 'Activity Id',
					'import_name' => 'campaignion_activity.activity_id',
				],
				1 => [
					'export_name' => 'Contact Id',
					'import_name' => 'campaignion_activity.contact_id',
				],
				2 => [
					'export_name' => 'Activity Type',
					'import_name' => 'campaignion_activity.type',
				],
				3 => [
					'export_name' => 'First Name',
					'import_name' => 'redhen_contact.first_name',
				],
				4 => [
					'export_name' => 'Middle Name',
					'import_name' => 'redhen_contact.middle_name',
				],
				5 => [
					'export_name' => 'Last Name',
					'import_name' => 'redhen_contact.last_name',
				],
				6 => [
					'export_name' => 'Redhen State',
					'import_name' => 'redhen_contact.redhen_state',
				],
				7 => [
					'export_name' => 'Creation of Contact',
					'import_name' => 'from_unixtime(redhen_contact.created)',
				],
				8 => [
					'export_name' => 'Update of Contact',
					'import_name' => 'from_unixtime(redhen_contact.updated)',
				],
			],
		],
		'Actions'         => [
			'class'            => '\Drupal\phylogram_datatransfer\import_model\imports\Action',
			'folder_structure' => 'default',
			'file_name'        => 'Actions',
			'file_extension'   => 'default',
			'frequency'        => 'default',
			'fields'           => [
				// Please provide table_name.field_name as import_name
				0  => [
					'export_name' => 'nid',
					'import_name' => 'webform_submissions.nid',
				],
				1  => [
					'export_name' => 'sid',
					'import_name' => 'webform_submissions.sid',
				],
				2  => [
					'export_name' => 'Draft',
					'import_name' => 'if(webform_submissions.is_draft, \'Yes\', \'No\')',
				],
				3  => [
					'export_name' => 'Submitted',
					'import_name' => 'from_unixtime(webform_submissions.submitted)',
				],
				4  => [
					'export_name' => 'Modified',
					'import_name' => 'from_unixtime(webform_submissions.modified)',
				],
				5  => [
					'export_name' => 'Completed',
					'import_name' => 'from_unixtime(webform_submissions.completed)',
				],
				6  => [
					'export_name' => 'Remote Adress',
					'import_name' => 'webform_submissions.remote_addr',
				],
				7  => [
					'export_name' => 'External Referer',
					'import_name' => 'webform_tracking.external_referer',
				],
				8  => [
					'export_name' => 'Referer',
					'import_name' => 'webform_tracking.referer',
				],
				9  => [
					'export_name' => 'Source',
					'import_name' => 'webform_tracking.source',
				],
				10 => [
					'export_name' => 'Medium',
					'import_name' => 'webform_tracking.medium',
				],
				11 => [
					'export_name' => 'Version',
					'import_name' => 'webform_tracking.version',
				],
				12 => [
					'export_name' => 'Other',
					'import_name' => 'webform_tracking.other',
				],
				13 => [
					'export_name' => 'Country',
					'import_name' => 'webform_tracking.country',
				],
				14 => [
					'export_name' => 'Term',
					'import_name' => 'webform_tracking.term',
				],
				15 => [
					'export_name' => 'Campaign',
					'import_name' => 'webform_tracking.campaign',
				],
				16 => [
					'export_name' => 'Payment status',
					'import_name' => 'payment_status_item.status',
				],
			],
		],
		'Redhen Contacts' => [
			'class'            => '\Drupal\phylogram_datatransfer\import_model\imports\RedhenContact',
			'folder_structure' => 'default',
			'file_name'        => 'Redhen Contacts',
			'file_extension'   => 'default',
			'frequency'        => 'default',
			'fields'           => [
				// This are Entity properties/keys . %CURRENT% will be replaced, with first entry
				0  => [
					'export_name' => 'Contact ID',
					'import_name' => 'contact_id',
				],
				1  => [
					'export_name' => 'First Name',
					'import_name' => 'first_name',
				],
				2  => [
					'export_name' => 'Middle Name',
					'import_name' => 'middle_name',
				],
				3  => [
					'export_name' => 'Last Name',
					'import_name' => 'last_name',
				],
				4  => [
					'export_name' => 'Redhen State',
					'import_name' => 'redhen_state',
				],
				5  => [ 'export_name' => 'Type', 'import_name' => 'type' ],
				6  => [
					'export_name' => 'Created',
					'import_name' => 'created',
				],
				7  => [
					'export_name' => 'Updated',
					'import_name' => 'updated',
				],
				8  => [
					'export_name' => 'Email',
					'import_name' => [
						// nested data
						'redhen_contact_email',
						'%CURRENT%',
						'%CURRENT%',
						'value',
					],
				],
				9  => [
					'export_name' => 'Country',
					'import_name' => [
						// nested data
						'field_adress',
						'%CURRENT%',
						'%CURRENT%',
						'country',
					],
				],
				10 => [
					'export_name' => 'Postal Code',
					'import_name' => [
						'field_adress',
						'%CURRENT%',
						'%CURRENT%',
						'postal_code',
					],
				],
				11 => [
					'export_name' => 'Gender',
					'import_name' => [
						// nested data
						'field_gender',
						'%CURRENT%',
						'%CURRENT%',
						'value',
					],
				],
				12 => [
					'export_name' => 'Phone Number',
					'import_name' => [
						// nested data
						'field_phone_number',
						'%CURRENT%',
						'%CURRENT%',
						'value',
					],
				],
				13 => [
					'export_name' => 'Prefered Language',
					'import_name' => [
						// nested data
						'field_prefered_language',
						'%CURRENT%',
						'%CURRENT%',
						'code',
					],
				],
				14 => [
					'export_name' => 'Social Network',
					'import_name' => [
						// nested data
						'field_social_network_links',
						'%CURRENT%',
						'%CURRENT%',
						'code',
					],
				],
			],
		],
	];


	public function __construct() {
		// replace default array values with default properties

		$settings = $this->settings;
		foreach ( $settings as $key => $topic ) {
			foreach ( $topic as $area => $setting ) {
				if ( $setting === 'default' ) {
					if ( property_exists( $this, 'default_' . $area ) ) {
						$property                        = $this->{'default_' . $area};
						$this->settings[ $key ][ $area ] = $property;
					} // else will lead eventually to NULL value in row
				}
			}

		}
	}


	public function iterateSettings() {
		foreach ( $this->settings as $setting ) {
			yield $setting;
		}
	}

}
