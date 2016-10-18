<?php
namespace AffWP\Util;

/**
 * Batch processor.
 *
 * @since 2.0
 */
abstract class Batch_Processor {

	/**
	 * Initializes the batch processor.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'localize_script' ) );
	}

	/**
	 * Passes defined JS vars to the admin script for batch processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function localize_script() {
		wp_localize_script( 'admin', 'affwpl10n', $this->js_vars() );
	}

	/**
	 * Outputs the batch items to JS vars.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @return array Array of variables to pass when localizing the script.
	 */
	abstract public function js_vars();
}
