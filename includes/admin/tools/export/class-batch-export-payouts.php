<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for exporting payouts based on affiliate ID or a date range
 * to a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Export_Payouts extends Batch\Export\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'export-payouts';

	/**
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'payout-logs';

	/**
	 * Capability needed to perform the current export.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $capability = 'export_payout_data';

	/**
	 * ID of affiliate to export payouts for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $affiliate_id = 0;

	/**
	 * Start and/or end dates to retrieve payouts for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $date = array();

	/**
	 * Status to export payouts for.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $status = '';

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init( $data = null ) {

		if ( null !== $data ) {

			if ( ! empty( $data['user_id'] ) ) {
				if ( $affiliate_id = affwp_get_affiliate_id( absint( $data['user_id'] ) ) ) {
					$this->affiliate_id = $affiliate_id;
				}
			}

			if ( ! empty( $data['start_date' ] ) ) {
				$this->date['start'] = sanitize_text_field( $data['start_date' ] );
			}

			if ( ! empty( $data['end_date'] ) ) {
				$this->date['end'] = sanitize_text_field( $data['end_date'] );
			}

			if ( ! empty( $data['status'] ) ) {
				$this->status = sanitize_text_field( $data['status'] );

				if ( 0 === $this->status ) {
					$this->status = '';
				}
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
		$total_to_export = $this->get_total_count();

		if ( false === $total_to_export  ) {
			$args = array(
				'number'       => -1,
				'fields'       => 'ids',
				'status'       => $this->status,
				'date'         => $this->date,
				'affiliate_id' => $this->affiliate_id,
			);

			$total_to_export = affiliate_wp()->affiliates->payouts->get_payouts( $args, true );

			$this->set_total_count( $total_to_export );
		}
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
			'payout_id'     => __( 'Payout ID', 'affiliate-wp' ),
			'affiliate_id'  => __( 'Affiliate ID', 'affiliate-wp' ),
			'referrals'     => __( 'Referrals', 'affiliate-wp' ),
			'amount'        => _x( 'Amount', 'payout', 'affiliate-wp' ),
			'owner'         => __( 'Owner', 'affiliate-wp' ),
			'payout_method' => __( 'Payout Method', 'affiliate-wp' ),
			'status'        => _x( 'Status', 'payout', 'affiliate-wp' ),
			'date'          => _x( 'Date', 'payout', 'affiliate-wp' ),
		);
	}

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since  2.0
	 */
	public function process_step() {
		if ( is_null( $this->status ) ) {
			return new \WP_Error( 'no_status_found', __( 'No valid referral status was selected for export.', 'affiliate-wp' ) );
		}

		return parent::process_step();
	}

	/**
	 * Retrieves the referral export data for a single step in the process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Data for a single step of the export.
	 */
	public function get_data() {

		$args = array(
			'status'       => $this->status,
			'date'         => $this->date,
			'affiliate_id' => $this->affiliate_id,
			'number'       => $this->per_step,
			'offset'       => $this->get_offset(),
		);

		$data         = array();
		$affiliates   = array();
		$referral_ids = array();
		$payouts    = affiliate_wp()->affiliates->payouts->get_payouts( $args );

		if( $payouts ) {

			$date_format = get_option( 'date_format' );

			foreach( $payouts as $payout ) {

				if ( $owner_user = get_user_by( 'id', $payout->owner ) ) {
					$owner = $owner_user->data->display_name;
				} else {
					$owner = $payout->owner;
				}

				/**
				 * Filters an individual line of payout data to be exported.
				 *
				 * @since 2.0
				 *
				 * @param array           $payout_data {
				 *     Single line of exported payout data
				 *
				 *     @type int    $payout_id     Payout ID.
				 *     @type int    $affiliate_id  Affiliate ID.
				 *     @type string $referrals     Comma-separated list of referral IDs.
				 *     @type float  $amount        Payout amount.
				 *     @type string $owner         Username of payout owner.
				 *     @type string $payout_method Payout method.
				 *     @type string $status        Payout status.
				 *     @type string $date          Payout date.
				 * }
				 * @param \AffWP\Affiliate\Payout $payout Payout object.
				 */
				$payout_data = apply_filters( 'affwp_payout_export_get_data_line', array(
					'payout_id'     => $payout->ID,
					'affiliate_id'  => $payout->affiliate_id,
					'referrals'     => $payout->referrals,
					'amount'        => $payout->amount,
					'owner'         => $owner,
					'status'        => $payout->status,
					'date'          => date_i18n( $date_format, strtotime( $payout->date ) ),
				), $payout );

				// Add slashing.
				$data[] = array_map( function( $column ) {
					return addslashes( preg_replace( "/\"/","'", $column ) );
				}, $payout_data );

				unset( $payout_data );
			}

		}

		return $data;
	}

	/**
	 * Retrieves a message for the given code.
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
						'%s payout log was successfully exported.',
						'%s payout logs were successfully exported.',
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
