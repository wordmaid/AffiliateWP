<?php

class Affiliate_WP_Campaigns_Graph extends Affiliate_WP_Graph {

	public $total     = 0;
	public $converted = 0;

	/**
	 * Get things started
	 *
	 * @since 1.8
	 */
	public function __construct( $_data = array() ) {

		if( empty( $_data ) ) {

			$this->data = $this->get_data();

		}

		// Generate unique ID
		$this->id   = md5( rand() );

		// Setup default options;
		$this->options = array(
			'y_mode'          => null,
			'y_decimals'      => 0,
			'x_decimals'      => 0,
			'y_position'      => 'right',
			'time_format'     => '%d/%b',
			'ticksize_unit'   => 'day',
			'ticksize_num'    => 1,
			'multiple_y_axes' => false,
			'bgcolor'         => '#f9f9f9',
			'bordercolor'     => '#ccc',
			'color'           => '#bbb',
			'borderwidth'     => 2,
			'bars'            => false,
			'lines'           => true,
			'points'          => true,
			'affiliate_id'    => false,
		);

	}

	/**
	 * Retrieve campaign data
	 *
	 * @since 1.8
	 */
	public function get_data() {

		$converted   = array();
		$unconverted = array();

		$dates = affwp_get_report_dates();

		$start = $dates['year'] . '-' . $dates['m_start'] . '-' . $dates['day'] . ' 00:00:00';
		$end   = $dates['year_end'] . '-' . $dates['m_end'] . '-' . $dates['day_end'] . ' 23:59:59';

		$date  = array(
			'start' => $start,
			'end'   => $end
		);

        // Get campaign data
		$campaigns = '';

		if( $campaigns ) {

			// Loop through each campaign, display data
			// (TODO - vars are invalid presently)
			foreach( $campaigns as $campaign ) {

				$date = date( 'Y-m-d', strtotime( $campaign->date ) );

				$this->total += 1;

				$campaign_visits          = $campaign->visits;
				$campaign_unique_visits   = $campaign->unique_visits;
				$campaign_referrals       = $campaign->referrals;
				$campaign_conversion_rate = affwp_format_amount( $campaign->conversion_rate );

			}

			$data = array(
				__( 'Campaign Visits',          'affiliate-wp' ) => $campaign_visits,
				__( 'Campaign Unique Visits',   'affiliate-wp' ) => $campaign_unique_visits,
				__( 'Campaign Referrals',       'affiliate-wp' ) => $campaign_referrals,
				__( 'Campaign Conversion Rate', 'affiliate-wp' ) => $campaign_conversion_rate
			);

		} else {
			$data = __( 'No campaigns are available to display.', 'affiliate-wp' );
		}
		return $data;
	}

	/**
	 * Retrieve conversion rate for successful campaigns
	 *
	 * @since 1.8
	 */
	public function get_conversion_rate() {
		return $this->total > 0 ? round( ( $this->converted / $this->total ) * 100, 2 ) : 0;
	}

}
