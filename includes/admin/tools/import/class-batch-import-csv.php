<?php
namespace AffWP\Utils\Batch_Process\Import;

use AffWP\Utils\Batch_Process as Batch;
use AffWP\Utils\Importer;

class CSV extends Batch\Import implements Importer\CSV {

	/**
	 * The parsed CSV file being imported.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \parseCSV
	 */
	public $csv;

	/**
	 * Total rows in the CSV file.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $total;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $field_mapping = array();

	/**
	 * Instantiates the importer.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param resource $_file File to import.
	 * @param int      $_step Current step.
	 */
	public function __construct( $_file = '', $_step = 1 ) {

		if( ! class_exists( 'parseCSV' ) ) {
			require_once AFFILIATEWP_PLUGIN_DIR . 'includes/libraries/parsecsv.lib.php';
		}

		$this->csv = new parseCSV();
		$this->csv->auto( $this->file );

		$this->total = count( $this->csv->data );

		parent::__construct( $_file, $_step );
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {

	}

	/**
	 * Maps CSV columns to their corresponding import fields.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array $import_fields Import fields to map.
	 */
	public function map_fields( $import_fields = array() ) {
		$this->field_mapping = $import_fields;
	}
a

}
