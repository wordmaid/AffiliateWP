<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;
use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch process to recount all affiliate stats.
 *
 * @see \AffWP\Utils\Batch_Process\Base
 * @see \AffWP\Utils\Batch_Process
 * @package AffWP\Utils\Batch_Process
 */
class Recount_Affiliate_Stats extends Utils\Batch_Process implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'recount-affiliate-stats';

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'manage_affiliates';

	/**
	 * Number of affiliates to process per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $per_step = 1;

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
	public function pre_fetch() {
		$total_to_recount = $this->get_total_count();

		if ( false === $total_to_recount ) {
			$affiliate_ids = affiliate_wp()->affiliates->get_affiliates( array(
				'number' => -1,
				'fields' => 'ids'
			) );

			$this->set_total_count( count( $affiliate_ids ) );

			affiliate_wp()->utils->data->write( "{$this->batch_id}_all_affiliate_ids", $affiliate_ids );
		}
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
	 * Retrieves a message based on the given message code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {
		switch( $code ) {

			case 'done':
				$final_count = $this->get_current_count();

				$message = sprintf(
					_n(
						'%s affiliate&#8217;s was successfully processed.',
						'%s affiliates&#8217; were successfully processed.',
						$final_count,
						'affiliate-wp'
					), number_format_i18n( $final_count )
				);
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}


}
