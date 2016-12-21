<?php
namespace AffWP\Utils\Batch_Process;

/**
 * Base interface for registering a batch process.
 *
 * @since 2.0
 */
Interface Base {

	/**
	 * Determines if the current user can perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process();

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step in the process. Accepts either a step number or 'done'.
	 */
	public function process_step( $step );

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 *
	 * @param int|string $step Current step.
	 * @return int Percentage completed.
	 */
	public function get_percentage_complete( $step );

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code );

}
