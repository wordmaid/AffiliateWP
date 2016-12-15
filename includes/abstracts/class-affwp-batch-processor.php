<?php
namespace AffWP\Utils\Batch_Processor;

/**
 * Base batch processor.
 *
 * @since 2.0
 * @abstract
 */
abstract class Base {

	/**
	 * Batch ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    null|string
	 */
	public $batch_id = null;

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function init( $data = null );

	/**
	 * Logic to run prior to the next step in the process.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function before_process_step();

	/**
	 * Instantiates the batch processor.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {
		if ( null !== $this->batch_id ) {
			add_action( "wp_ajax_{$this->batch_id}_process_step", array( $this, 'process_step' ) );
		}
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @param int|string $step Step in the process. Accepts either a step number or 'done'.
	 */
	abstract public function process_step( $step );

	/**
	 * Logic to run following the next step in the process.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function after_process_step();

	/**
	 * Logic to run once batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function finish();
}
