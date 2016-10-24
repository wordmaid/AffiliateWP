<?php
namespace AffWP\Util;

/**
 * Batch processor.
 *
 * @since 2.0
 */
abstract class Batch_Processor {

	public $type = '';

	/**
	 * Initializes the batch processor.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {
		$this->setup();

		add_action( 'wp_enqueue_scripts',          array( $this, 'localize_script' ) );
		add_action( 'wp_ajax_process_single_item', array( $this, 'process_item'    ) );
	}

	protected function setup() {
		if ( isset( $this->export_type ) ) {
			$this->type = $this->export_type;
		}


	}
	/**
	 * Passes defined JS vars to the admin script for batch processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function localize_script() {
		wp_localize_script( 'admin', 'affwpProcess', array( 'items' => $this->get_items() ) );
	}

	/**
	 * Retrieves the list of registered JS vars.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @return array Array of JS vars to print.
	 */
	private function get_items() {
		return $this->set_items();
	}

	/**
	 * Defines items for processing.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @return array Array of variables to pass when localizing the script.
	 */
	abstract public function set_items();

	/**
	 * Handles processing a single item and returning a JSON response to the Ajax script.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function process_item();
}
