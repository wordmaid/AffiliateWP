<?php
/**
 * Reports Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/screen-options.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/reports-functions.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-metabox.php';

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function affwp_reports_admin() {

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_reports_tabs() ) ? $_GET[ 'tab' ] : 'overview';

?>
	<div class="wrap">

		<?php do_action( 'affwp_reports_page_top' ); ?>

		<h2 class="nav-tab-wrapper">
			<?php
			foreach( affwp_get_reports_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
					'affwp_notice'     => false
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>

		<?php do_action( 'affwp_reports_page_middle' ); ?>

		<div id="tab_container">
			<?php do_action( 'affwp_reports_tab_' . $active_tab ); ?>
		</div><!-- #tab_container-->

		<?php do_action( 'affwp_reports_page_bottom' ); ?>

	</div>
<?php
}

/**
 * Retrieve reports tabs
 *
 * @since 1.1
 * @return array $tabs
 */
function affwp_get_reports_tabs() {

	$tabs                  = array();
	$tabs['overview']      = __( 'Overview', 'affiliate-wp' );
	$tabs['referrals']     = __( 'Referrals', 'affiliate-wp' );
	$tabs['visits']        = __( 'Visits', 'affiliate-wp' );
	$tabs['registrations'] = __( 'Affiliate Registrations', 'affiliate-wp' );
	$tabs['exporter']      = __( 'Exporter', 'affiliate-wp' );

	return apply_filters( 'affwp_reports_tabs', $tabs );
}

/**
 * Display the overview reports tab
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_overview() {

	$affwp_reports_earnings_today = affiliate_wp()->referrals->paid_earnings( 'today' );

?>
	<div id="dashboard-widgets" class="metabox-holder reports-metabox-holder">
		<div id="postbox-container-1" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="screen-reader-text">
							Toggle panel: Affiliates At a Glance
						</span>
						<span class="toggle-indicator" aria-hidden="false"></span>
					</button>
					<h2 class="hndle ui-sortable-handle">
						<span>
							Affiliates at a Glance
						</span>
					</h2>
					<div class="inside">
						<div class="main">
							<ul>
								<li>
									<h2>
									<?php echo $affwp_reports_earnings_today . __(' earned today.', 'affiliate-wp' ); ?>

									</h2>
								</li>
								<li>
									<h2>
									<?php echo __('You\'ve paid out a total of ', 'affiliate-wp' ) . affiliate_wp()->referrals->paid_earnings( '' ) . __(' all-time.', 'affiliate-wp' ); ?>
									</h2>
								</li>
								<li>
									<h2>
										<?php echo __('You have a total of ', 'affiliate-wp' ) . affiliate_wp()->referrals->unpaid_earnings( '' ) . __(' pending payment.', 'affiliate-wp' ); ?>
									</h2>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Top Performers</span><span class="toggle-indicator" aria-hidden="false"></span></button>
					<h2 class="hndle ui-sortable-handle"><span>Top Performers</span></h2>
					<div class="inside">
						<div class="main">
							<?php

								/**
								 * An inline call to shortcode_exists is used below, and makes me
								 * cringe.
								 * If this data will be included, some better options may be:
								 * 1. Adding some manner of the leaderboard functionality into core.
								 * 2. Adding a general-purpose add-on exists-checker method, which
								 * can be referenced here, using the slug of the add-on
								 * as a parameter, eg:
								 *
								 * affwp_addon_exists( 'affiliatewp_leaderboard' )
								 *
								 */

								if ( class_exists( 'AffiliateWP_Leaderboard' ) && shortcode_exists( 'affiliate_leaderboard' ) ) { ?>

									<h3>
										<?php echo __( 'Affiliates', 'affiliate-wp' ); ?>
									</h3>
									<?php
										echo do_shortcode( '[affiliate_leaderboard referrals="yes" earnings="yes" visits="yes"]' );
									} else {
										echo __('To show this information, you\'ll need to install the', 'affiliate-wp' ) . '<a href="https://wordpress.org/plugins/affiliatewp-leaderboard/" target="_blank">AffiliateWP Leaderboard add-on.</a>';
									}
									?>
									<hr />
									<h3>
										<?php echo __( 'References', 'affiliate-wp' ); ?>
									</h3>

									<?php echo affwp_get_referrals_by_reference(); ?>

									<hr />
									<h3>
										<?php echo __( 'Creatives', 'affiliate-wp' ); ?>
									</h3>
									<?php echo affwp_get_referrals_by_creatives(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'affwp_reports_tab_overview', 'affwp_reports_tab_overview' );

/**
 * Display the referrals reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_referrals() {
?>
	<table id="affwp_total_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Paid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Earnings This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Earnings Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
				<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<table id="affwp_unpaid_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Earnings Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<table id="affwp_unpaid_counts" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Referrals This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Referrals Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<?php
	$graph = new Affiliate_WP_Referrals_Graph;
	$graph->set( 'x_mode', 'time' );
	$graph->display();

}
add_action( 'affwp_reports_tab_referrals', 'affwp_reports_tab_referrals' );

/**
 * Display the visits reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_visits() {

	$graph = new Affiliate_WP_Visits_Graph;
	$graph->set( 'x_mode',   'time' );
	$graph->set( 'currency', false  );

?>
	<table id="affwp_total_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Successful Conversions', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Conversion Rate', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo absint( $graph->total ); ?></td>
				<td><?php echo absint( $graph->converted ); ?></td>
				<td><?php echo $graph->get_conversion_rate(); ?>%</td>
			</tr>

		</tbody>

	</table>
<?php
	$graph->display();

}
add_action( 'affwp_reports_tab_visits', 'affwp_reports_tab_visits' );

/**
 * Display the affiliate registration reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_registrations() {

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-registrations-graph.php';

	$graph = new Affiliate_WP_Registrations_Graph;
	$graph->set( 'x_mode',   'time' );
	$graph->set( 'currency', false  );
	$graph->display();

}
add_action( 'affwp_reports_tab_registrations', 'affwp_reports_tab_registrations' );

/**
 * Display the affiliate reports exporter
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_exporter() {

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-exporter.php';

	$exporter = new Affiliate_WP_Reports_Exporter;
	$exporter->display();

}
add_action( 'affwp_reports_tab_exporter', 'affwp_reports_tab_exporter' );
