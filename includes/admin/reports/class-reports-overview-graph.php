<?php

class Affiliate_WP_Reports_Overview_Graph extends Affiliate_WP_Graph {

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct( $_data = array() ) {

		if( empty( $_data ) ) {

			$this->data = $this->get_data();

		}

		// Generate unique ID
		$this->id   = md5( rand() );

		// Setup default options
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
	 * Retrieve overview data
	 * Gets a combination of referrals, registrations, and visits
	 * @since 1.8
	 */
	public function get_data() {

		$paid     = array();
		$unpaid   = array();
		$rejected = array();
		$pending  = array();

		$dates = affwp_get_report_dates();

		$start = $dates['year'] . '-' . $dates['m_start'] . '-' . $dates['day'] . ' 00:00:00';
		$end   = $dates['year_end'] . '-' . $dates['m_end'] . '-' . $dates['day_end'] . ' 23:59:59';
		$date  = array(
			'start' => $start,
			'end'   => $end
		);

		//echo '<pre>'; print_r( $date ); echo '</pre>'; exit;

		// Get referral graph data
		$referrals = affiliate_wp()->referrals->get_referrals( array(
			'orderby'      => 'date',
			'order'        => 'ASC',
			'date'         => $date,
			'number'       => -1,
			'affiliate_id' => $this->get( 'affiliate_id' )
		) );

		$pending[] = array( strtotime( $start ) * 1000 );
		$pending[] = array( strtotime( $end ) * 1000 );

		if( $referrals ) {
			foreach( $referrals as $referral ) {

				switch( $referral->status ) {

					case 'paid' :

						$paid[] = array( strtotime( $referral->date ) * 1000, $referral->amount );

						break;

					case 'unpaid' :

						$unpaid[] = array( strtotime( $referral->date ) * 1000, $referral->amount );

						break;

					case 'rejected' :

						$rejected[] = array( strtotime( $referral->date ) * 1000, $referral->amount );

						break;

					case 'pending' :

						$pending[] = array( strtotime( $referral->date ) * 1000, $referral->amount );

						break;

					default :

						break;

				}

			}
		}

		// Get affiliate registration data
		$affiliates = affiliate_wp()->affiliates->get_affiliates( array(
			'orderby'  => 'date_registered',
			'order'    => 'ASC',
			'number'   => -1,
			'date'     => $date
		) );

		$affiliate_data = array();
		$affiliate_data[] = array( strtotime( $start ) * 1000 );
		$affiliate_data[] = array( strtotime( $end ) * 1000 );

		if( $affiliates ) {

			foreach( $affiliates as $affiliate ) {

				if( 'today' == $dates['range'] || 'yesterday' == $dates['range'] ) {

					$point = strtotime( $affiliate->date_registered ) * 1000;

					$affiliate_data[ $point ] = array( $point, 1 );

				} else {

					$time      = date( 'Y-n-d', strtotime( $affiliate->date_registered ) );
					$timestamp = strtotime( $time ) * 1000;

					if( array_key_exists( $time, $affiliate_data ) && isset( $affiliate_data[ $time ][1] ) ) {

						$count = $affiliate_data[ $time ][1] += 1;

						$affiliate_data[ $time ] = array( $timestamp, $count );

					} else {

						$affiliate_data[ $time ] = array( $timestamp, 1 );

					}


				}


			}

		}



		$data = array(
			__( 'Unpaid Referral Earnings', 'affiliate-wp' )   => $unpaid,
			__( 'Pending Referral Earnings', 'affiliate-wp' )  => $pending,
			__( 'Rejected Referral Earnings', 'affiliate-wp' ) => $rejected,
			__( 'Paid Referral Earnings', 'affiliate-wp' )     => $paid,
			__( 'Affiliate Registrations', 'affiliate-wp' )    => $affiliate_data,
		);

		return $data;

	}

}
