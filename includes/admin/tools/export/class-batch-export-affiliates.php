<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for migrating existing users to affiliate accounts.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Affiliates extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export_affiliates';

	/**
	 * Export type.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'affiliates';

	/**
	 * Affiliates status to export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $status;

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {
		if ( null !== $data && isset( $data['status'] ) ) {
			$this->status = sanitize_text_field( $data['status'] );

			if ( 0 === $this->status ) {
				$this->status = '';
			}
		}
	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {

	}

	/**
	 * Retrieves the columns for the CSV export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array The list of CSV columns.
	 */
	public function csv_cols() {
		return array(
			'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
			'email'           => __( 'Email', 'affiliate-wp' ),
			'name'            => __( 'Name', 'affiliate-wp' ),
			'payment_email'   => __( 'Payment Email', 'affiliate-wp' ),
			'username'        => __( 'Username', 'affiliate-wp' ),
			'rate'            => __( 'Rate', 'affiliate-wp' ),
			'rate_type'       => __( 'Rate Type', 'affiliate-wp' ),
			'earnings'        => __( 'Earnings', 'affiliate-wp' ),
			'referrals'       => __( 'Referrals', 'affiliate-wp' ),
			'visits'          => __( 'Visits', 'affiliate-wp' ),
			'conversion_rate' => __( 'Conversion Rate', 'affiliate-wp' ),
			'status'          => __( 'Status', 'affiliate-wp' ),
			'date_registered' => __( 'Date Registered', 'affiliate-wp' )
		);
	}

	/**
	 * Determines if the current user can perform an affiliates export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process() {
		return $this->can_export();
	}

	/**
	 * Retrieves the affiliate export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int $step Optional. Step number. 'done' should be handled prior to calling this method. Default 1.
	 * @return array Data for a single step of the export.
	 */
	public function get_data( $step = 1 ) {

		$args = array(
			'status' => $this->status,
			'number' => $this->per_step,
			'offset' => $this->get_offset( $step )
		);

		$data       = array();
		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if( $affiliates ) {

			foreach( $affiliates as $affiliate ) {

				$data[] = array(
					'affiliate_id'    => $affiliate->affiliate_id,
					'email'           => affwp_get_affiliate_email( $affiliate->affiliate_id ),
					'name'            => affwp_get_affiliate_name( $affiliate->affiliate_id ),
					'payment_email'   => affwp_get_affiliate_payment_email( $affiliate->affiliate_id ),
					'username'        => affwp_get_affiliate_login( $affiliate->affiliate_id ),
					'rate'            => affwp_get_affiliate_rate( $affiliate->affiliate_id ),
					'rate_type'       => affwp_get_affiliate_rate_type( $affiliate->affiliate_id ),
					'earnings'        => $affiliate->earnings,
					'referrals'       => $affiliate->referrals,
					'visits'          => $affiliate->visits,
					'conversion_rate' => affwp_get_affiliate_conversion_rate( $affiliate->affiliate_id ),
					'status'          => $affiliate->status,
					'date_registered' => $affiliate->date_registered,
				);

			}

		}

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data', $data );

		/** This filter is documented in includes/admin/tools/export/class-export.php */
		$data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step in the process. Accepts either a step number or 'done'.
	 */
	public function process_step( $step ) {
		// Nonce.

		$data = $this->get_data( $step );

		if ( empty( $data ) ) {
			return 'done';
		}
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

	}

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 * @abstract
	 */
	public function finish() {

	}

}
