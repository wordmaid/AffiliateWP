<?php
namespace AffWP\Utils\Batch_Process;

/**
 * Implements the base batch importer as an intermediary between a batch process
 * and the base importer class.
 *
 * @since 2.0
 *
 * @see \Affiliate_WP_Import
 */
class Import extends \Affiliate_WP_Import {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id;

	/**
	 * The file the export data will be stored in.
	 *
	 * @access protected
	 * @since  2.0
	 * @var    resource
	 */
	protected $file;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $step;

	/**
	 * The number of items to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 20;

	/**
	 * Map of CSV columns > database fields
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $field_mapping = array();


}
