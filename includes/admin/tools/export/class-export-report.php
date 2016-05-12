<?php
/**
 * AffiliateWP Report Export Class
 *
 * Handles exports of reports.
 * Each data export type (referrals, affiliates, visits, reports) extends the Affiliate_WP_Export class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Affiliate_WP_Report_Export Class
 *
 * @since 1.8
 */
class Affiliate_WP_Report_Export extends Affiliate_WP_Export {

    /**
     * Our export type. Used for export-type specific filters/actions
     * @var string
     * @since 1.8
     */
    public $export_type = 'affiliates';

    /**
     * Our export type. Used for export-type specific filters/actions
     * @var string
     * @since 1.8
     */
    public $tab = 'affiliates';

    /**
     * Date
     * @var array
     * @since 1.8
     */
    public $date;

    /**
     * Status
     * @var string
     * @since 1.8
     */
    public $status;

    /**
     * Affiliate ID
     * @var int
     * @since 1.8
     */
    public $affiliate = null;

    /**
     * Displays the exporter interface
     *
     * @access public
     * @since 1.8
     * @return array $cols All the columns
     */
    public function display() { ?>

        <form method="post" enctype="multipart/form-data" class="affwp-reports-export-submit" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-reports&tab=' . $this->tab ); ?>">

            <p>
                <input type="hidden" name="affwp_action" value="export_report_affiliates" />
                <?php wp_nonce_field( 'affwp_export_report_affiliates_nonce', 'affwp_export_report_affiliates_nonce' ); ?>
                <?php submit_button( __( 'Export CSV', 'affiliate-wp' ), 'primary export-submit', 'submit', false ); ?>
            </p>
        </form>
    <?php
    }

    /**
     * Set the CSV columns
     * This method must retrieve data from the current instance
     * of the AffWP_Export_Reports_List_Table class.
     *
     * @access public
     * @since 1.8
     * @return array $cols All the columns
     */
    public function csv_cols() {

        $cols = array(
            'name'            => __( 'Name', 'affiliate-wp' ),
            'username'        => __( 'Username', 'affiliate-wp' ),
            'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
            'earnings'        => __( 'Earnings', 'affiliate-wp' ),
            'rate'            => __( 'Rate', 'affiliate-wp' ),
            'unpaid'          => __( 'Unpaid Referrals', 'affiliate-wp' ),
            'referrals'       => __( 'Paid Referrals', 'affiliate-wp' ),
            'visits'          => __( 'Visits', 'affiliate-wp' ),
            'status'          => __( 'Status', 'affiliate-wp' ),
            'date_registered' => __( 'Registered', 'affiliate-wp' )
        );
        return $cols;
    }

    /**
     * Get the data being exported
     *
     * @access public
     * @since 1.8
     * @return array $data Data for Export
     */
    public function get_data() {

        $this->status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] )     : null;

        $reg_start = isset( $_GET['reg_start'] ) ? sanitize_text_field( $_GET['reg_start'] )     : null;
        $reg_end = isset( $_GET['reg_end'] ) ? sanitize_text_field( $_GET['reg_end'] )     : null;

        $args = array(
            'status' => $this->status,
            'number' => -1,
            'date_query' => array(
                array( 'after'     => $reg_start,
                       'before'    => $reg_end,
                       'inclusive' => true,
                )
            )
        );

        $data       = array();
        $affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

        if( $affiliates ) {

            foreach( $affiliates as $affiliate ) {

                $data[] = array(
                    'affiliate_id'    => $affiliate->affiliate_id,
                    'email'           => affwp_get_affiliate_email( $affiliate->affiliate_id ),
                    'payment_email'   => affwp_get_affiliate_payment_email( $affiliate->affiliate_id ),
                    'username'        => affwp_get_affiliate_login( $affiliate->affiliate_id ),
                    'rate'            => affwp_get_affiliate_rate( $affiliate->affiliate_id ),
                    'rate_type'       => affwp_get_affiliate_rate_type( $affiliate->affiliate_id ),
                    'earnings'        => $affiliate->earnings,
                    'referrals'       => $affiliate->referrals,
                    'visits'          => $affiliate->visits,
                    'status'          => $affiliate->status,
                    'date_registered' => $affiliate->date_registered,
                    'date_query'      => array(
                        array( 'after'     => $reg_start,
                               'before'    => $reg_end,
                               'inclusive' => true,
                        )
                    )
                );

            }

        }

        $data = apply_filters( 'affwp_export_get_data', $data );
        $data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

        return $data;
    }



}
