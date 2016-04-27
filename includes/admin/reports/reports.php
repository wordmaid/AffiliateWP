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

include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-metabox-base.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-metabox-overview-referrals.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-metabox-affiliate-leaderboard.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-metabox-references.php';

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

    $tabs               = array();
    $tabs['overview']   = __( 'Overview', 'affiliate-wp' );
    $tabs['affiliates'] = __( 'Affiliates', 'affiliate-wp' );
    $tabs['referrals']  = __( 'Referrals', 'affiliate-wp' );
    $tabs['visits']     = __( 'Visits', 'affiliate-wp' );
    $tabs['campaigns']  = __( 'Campaigns', 'affiliate-wp' );

    return apply_filters( 'affwp_reports_tabs', $tabs );
}

/**
 * Display the overview reports tab
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_overview() {

?>

    <div id="dashboard-widgets" class="metabox-holder reports-metabox-holder">
        <div id="postbox-container-1" class="postbox-container">
            <?php   // Reports meta boxes
                    do_action( 'affwp_reports_meta_boxes' );
                    do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'normal', null );
            ?>
        </div>
        <div id="postbox-container-2" class="postbox-container">
            <?php   // Reports meta boxes
                    do_action( 'affwp_reports_meta_boxes' );
                    do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'side', null );
            ?>
        </div>
        <div id="postbox-container-3" class="postbox-container">
            <?php   // Reports meta boxes
                    do_action( 'affwp_reports_meta_boxes' );
                    do_meta_boxes( 'affiliates_page_affiliate-wp-reports', 'advanced', null );
            ?>
        </div>
    </div>

    <div id="dashboard-widgets" class="metabox-holder reports-metabox-holder">



    </div>
    <?php
    $graph = new Affiliate_WP_Reports_Overview_Graph;
    $graph->set( 'x_mode', 'time' );
    $graph->display();

}

add_action( 'affwp_reports_tab_overview', 'affwp_reports_tab_overview' );

/**
 * Display the reports tab
 * Contains WP_List_Table view of general affiliate data
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_affiliates() {

    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-export-reports-list-table.php';

    // Output exporter class
    affwp_reports_exporter();

}
add_action('affwp_reports_tab_affiliates', 'affwp_reports_tab_affiliates' );

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
 * Display the campaigns reports tab
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_campaigns() {

    $graph = new Affiliate_WP_Campaigns_Graph;
    $graph->set( 'x_mode',   'time' );
    $graph->set( 'currency', false  );

?>
    <table id="affwp_total_earnings" class="affwp_table">

        <thead>

            <tr>

                <th><?php _e( 'Campaigns', 'affiliate-wp' ); ?></th>
                <th><?php _e( 'Successful Conversions', 'affiliate-wp' ); ?></th>
                <th><?php _e( 'Conversion Rate', 'affiliate-wp' ); ?></th>

            </tr>

        </thead>

        <tbody>

            <tr>
                <td></td>
                <td></td>
                <td>%</td>
            </tr>

        </tbody>

    </table>
<?php
    $graph->display();

}
add_action( 'affwp_reports_tab_campaigns', 'affwp_reports_tab_campaigns' );


/**
 *
 * To be replaced with a unified call to the AffWP_Data_Filters class.
 *
 */
function affwp_reports_exporter() {

    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-export-reports-list-table.php';
    $reports_table = new AffWP_Export_Reports_List_Table();
    $reports_table->prepare_items();

    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-reports.php';
    $exporter      = new Affiliate_WP_Reports_Export();
    $exporter->display();

    ?>
    <div class="wrap">
        <h1><?php echo __( 'Affiliate Reports','affiliate-wp' ); ?></h1>
        <?php do_action( 'affwp_reports_export_affiliates_page_top' ); ?>
        <form id="affwp-report-filter" method="get" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-reports&tab=affiliates' ); ?>">
        <?php

            // Output list table
            $reports_table->views();
            $reports_table->advanced_filters();
            $reports_table->display();
        ?>
        </form>
        <?php do_action( 'affwp_reports_export_affiliates_page_bottom' ); ?>
    </div>
<?php

}






