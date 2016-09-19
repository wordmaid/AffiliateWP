<?php
namespace AffWP\Campaign\Admin\Reports;

use AffWP\Admin\Reports;

/**
 * Implements a core 'Campaigns' tab for the Reports screen.
 *
 * @since 1.9
 *
 * @see \AffWP\Admin\Reports\Tab
 */
class Tab extends Reports\Tab {

	/**
	 * Sets up the Campaigns tab for Reports.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		$this->tab_id   = 'campaigns';
		$this->label    = __( 'Campaigns', 'affiliate-wp' );
		$this->priority = 0;
		$this->graph    = new \Affiliate_WP_Visits_Graph;

		parent::__construct();
	}

	/**
	 * Registers the Campaigns tab tiles.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_tiles() {
		$top_campaign = affiliate_wp()->campaigns->get_campaigns( array(
			'orderby' => 'conversion_rate',
			'number'  => 1,
		) );

		if ( ! empty( $top_campaign[0] ) ) {
			$campaign = $top_campaign[0];

			$affiliate_link = add_query_arg( array(
				'page'         => 'affiliate-wp-referrals',
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			), admin_url( 'admin.php ') );

			$this->register_tile( 'best_converting_campaign', array(
				'label'           => __( 'Best Converting Campaign (All Time)', 'affiliate-wp' ),
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					affwp_get_affiliate_name( $campaign->affiliate_id ),
					$campaign->visits
				)
			) );

			unset( $campaign, $affiliate_link );
		}

		$top_campaign_date = affiliate_wp()->campaigns->get_campaigns( array(
			'orderby' => 'conversion_rate',
			'number'  => 1,
			'date'    => $this->date_query,
		) );

		if ( ! empty( $top_campaign_date[0] ) ) {
			$campaign = $top_campaign_date[0];

			$affiliate_link = add_query_arg( array(
				'page'         => 'affiliate-wp-referrals',
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			), admin_url( 'admin.php ') );

			$this->register_tile( 'best_converting_campaign_date', array(
				'label'           => sprintf( __( 'Best Converting Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'tertiary',
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					affwp_get_affiliate_name( $campaign->affiliate_id ),
					$campaign->visits
				)
			) );

			unset( $campaign, $affiliate_link );
		}


		$active_campaigns = affiliate_wp()->campaigns->get_campaigns( array(
			'number'  => 1,
			'orderby' => 'visits',
			'date'    => $this->date_query,
		) );

		if ( ! empty( $active_campaigns[0] ) ) {
			$campaign = $active_campaigns[0];

			$affiliate_link = add_query_arg( array(
				'page'         => 'affiliate-wp-referrals',
				'affiliate_id' => $campaign->affiliate_id,
				'orderby'      => 'status',
				'order'        => 'ASC',
			), admin_url( 'admin.php ') );

			$this->register_tile( 'most_active_campaign', array(
				'label'           => sprintf( __( 'Most Active Campaign (%s)', 'affiliate-wp' ),
					$this->get_date_comparison_label( __( 'Custom', 'affiliate-wp' ) )
				),
				'context'         => 'secondary',
				'data'            => empty( $campaign->campaign ) ? __( 'n/a', 'affiliate-wp' ) : $campaign->campaign,
				'comparison_data' => sprintf( __( 'Affiliate: <a href="%1$s">%2$s</a> | Visits: %3$d', 'affiliate-wp' ),
					esc_url( $affiliate_link ),
					affwp_get_affiliate_name( $campaign->affiliate_id ),
					$campaign->visits
				),
			) );

			unset( $campaign, $affiliate_link );
		}

	}

	/**
	 * Handles displaying the 'Trends' graph.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display_trends() {
		$this->graph->set( 'show_controls', false );
		$this->graph->set( 'x_mode', 'time' );
		$this->graph->display();
	}

}
