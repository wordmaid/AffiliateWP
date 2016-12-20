<?php
namespace AffWP\Utils\Batch_Process;

/**
 * Base process for batch processing.
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
	 */
	public function init( $data = null ) {}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {}

	/**
	 * Determines if the current user can perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	abstract public function can_process();

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
	 * Defines logic to execute following the next step in the process.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function after_process_step() {}

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	abstract public function finish();
}
