<?php
namespace AffWP\Affiliate\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements an 'Affiliates' tab for the Reports screen.
 *
 * @since 1.9
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Sets up the Affiliates tab for Reports.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->tab_id   = 'affiliates';
		$this->label    = __( 'Affiliates', 'affiliate-wp' );
		$this->priority = 5;
		$this->graph    = new \Affiliate_WP_Registrations_Graph;

		parent::__construct();
	}

	/**
	 * Registers the Affiliates tab tiles.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_tiles() {
		$this->register_tile( 'total_affiliates', array(
			'label'           => __( 'Total Affiliates', 'affiliate-wp' ),
			'type'            => 'number',
			'data'            => affiliate_wp()->affiliates->count(),
			'comparison_data' => __( 'All Time', 'affiliate-wp' ),
		) );

		$top_affiliate = affiliate_wp()->affiliates->get_affiliates( array(
			'number'  => 1,
			'orderby' => 'earnings',
			'status'  => 'active',
			'date'    => $this->date_query,
		) );

		if ( ! empty( $top_affiliate[0] ) ) {
			$affiliate  = $top_affiliate[0];
			$name       = affwp_get_affiliate_name( $affiliate->ID );
			$data_link  = sprintf( '<a href="%1$s">%2$s</a>',
				esc_url( add_query_arg( array(
					'page'         => 'affiliate-wp-affiliates',
					'affiliate_id' => $affiliate->ID,
					'action'       => 'view_affiliate',
				), admin_url( 'admin.php ') ) ),
				empty( $name ) ? sprintf( __( 'Affiliate #%d', 'affiliate-wp' ), $affiliate->ID ) : $name
			);

			$this->register_tile( 'top_earning_affiliate', array(
				'label' => __( 'Top Earning Affiliate', 'affiliate-wp' ),
				'type'  => 'text-special',
				'data'  => $data_link,
				'comparison_data' => sprintf( '%1$s (%2$s)',
					$this->get_date_comparison_label(),
					affwp_currency_filter( affwp_format_amount( $affiliate->earnings ) )
				),
			) );

			unset( $affiliate, $data_link );
		}

		$this->register_tile( 'new_affiliates', array(
			'label'           => __( 'New Affiliates', 'affiliate-wp' ),
			'type'            => 'number',
			'data'            => affiliate_wp()->affiliates->count( array(
				'date' => $this->date_query
			) ),
			'comparison_data' => $this->get_date_comparison_label(),
			'context'         => 'secondary',
		) );

		$highest_converter = affiliate_wp()->affiliates->get_affiliates( array(
			'number'  => 1,
			'orderby' => 'referrals',
			'status'  => 'active',
			'date'    => $this->date_query,
		) );

		if ( ! empty( $highest_converter[0] ) ) {
			$affiliate = $highest_converter[0];
			$name       = affwp_get_affiliate_name( $affiliate->ID );
			$data_link  = sprintf( '<a href="%1$s">%2$s</a>',
				esc_url( add_query_arg( array(
					'page'         => 'affiliate-wp-referrals',
					'affiliate_id' => $affiliate->ID,
					'orderby'      => 'status',
					'order'        => 'ASC',
				), admin_url( 'admin.php ') ) ),
				empty( $name ) ? sprintf( __( 'Affiliate #%d', 'affiliate-wp' ), $affiliate->ID ) : $name
			);

			$this->register_tile( 'highest_converting_affiliate', array(
				'label'           => __( 'Highest Converting Affiliate', 'affiliate-wp' ),
				'type'            => 'text-special',
				'context'         => 'secondary',
				'data'            => $data_link,
				'comparison_data' => sprintf( '%1$s (%2$d referrals)',
					$this->get_date_comparison_label(),
					affwp_get_affiliate_referral_count( $affiliate->ID )
				),
			) );

			unset( $affiliate, $data_link );
		}

		$payouts = affiliate_wp()->affiliates->payouts->get_payouts( array(
			'number' => -1,
			'fields' => 'amount',
			'date'   => $this->date_query,
		) );

		if ( ! $payouts ) {
			$payouts = array( 0 );
		}

		$this->register_tile( 'average_payout_amount', array(
			'label'           => __( 'Average Payout', 'affiliate-wp' ),
			'type'            => 'amount',
			'context'         => 'tertiary',
			'data'            => array_sum( $payouts ) / count( $payouts ),
			'comparison_data' => $this->get_date_comparison_label(),
		) );
	}

	/**
	 * Handles displaying the 'Trends' graph.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display_trends() {
		$this->graph->set( 'show_controls', false );
		$this->graph->set( 'x_mode',   'time' );
		$this->graph->set( 'currency', false  );
		$this->graph->display();
	}

}
