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
class Affiliate_WP_Reports_Export extends Affiliate_WP_Export {

    /**
     * Our export type. Used for export-type specific filters/actions
     * @var string
     * @since 1.8
     */
    public $export_type = 'reports';

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


        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-tools&tab=export_import' ); ?>">
            <p>
                <span class="affwp-ajax-search-wrap">
                    <input type="text" name="user_name" id="user_name" class="affwp-user-search" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
                    <input type="hidden" name="user_id" id="user_id" value=""/>
                    <img class="affwp-ajax waiting" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;"/>
                </span>
                <input type="text" class="affwp-datepicker" autocomplete="off" name="reg_start_date" placeholder="<?php _e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
                <input type="text" class="affwp-datepicker" autocomplete="off" name="reg_end_date" placeholder="<?php _e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
                <select name="status" id="status">
                    <option value="0"><?php _e( 'All Statuses', 'affiliate-wp' ); ?></option>
                    <option value="paid"><?php _e( 'Paid', 'affiliate-wp' ); ?></option>
                    <option value="unpaid"><?php _e( 'Unpaid', 'affiliate-wp' ); ?></option>
                    <option value="pending"><?php _e( 'Pending', 'affiliate-wp' ); ?></option>
                    <option value="rejected"><?php _e( 'Rejected', 'affiliate-wp' ); ?></option>
                </select>
                <div id="affwp_user_search_results"></div>
                <div class="description"><?php _e( 'To search for an affiliate, enter the affiliate\'s login name, first name, or last name. Leave blank to export referrals for all affiliates.', 'affiliate-wp' ); ?></div>
            </p>
            <p>
                <input type="hidden" name="affwp_action" value="export_referrals" />
                <?php wp_nonce_field( 'affwp_export_referrals_nonce', 'affwp_export_referrals_nonce' ); ?>
                <?php submit_button( __( 'Export', 'affiliate-wp' ), 'primary', 'submit', false ); ?>
            </p>
        </form>
    <?php
    }

    /**
     * Set the CSV columns
     *
     * @access public
     * @since 1.8
     * @return array $cols All the columns
     */
    public function csv_cols() {
        $cols = array(
            'affiliate_id'  => __( 'Affiliate ID', 'affiliate-wp' ),
            'email'         => __( 'Email', 'affiliate-wp' ),
            'payment_email' => __( 'Payment Email', 'affiliate-wp' ),
            'amount'        => __( 'Amount', 'affiliate-wp' ),
            'currency'      => __( 'Currency', 'affiliate-wp' ),
            'description'   => __( 'Description', 'affiliate-wp' ),
            'campaign'      => __( 'Campaign', 'affiliate-wp' ),
            'reference'     => __( 'Reference', 'affiliate-wp' ),
            'context'       => __( 'Context', 'affiliate-wp' ),
            'status'        => __( 'Status', 'affiliate-wp' ),
            'date'          => __( 'Date', 'affiliate-wp' )
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

        $args = array(
            'status'       => $this->status,
            'date'         => ! empty( $this->date ) ? $this->date : '',
            'affiliate_id' => $this->affiliate,
            'number'       => -1
        );

        $data         = array();
        $affiliates   = array();
        $referral_ids = array();
        $referrals    = affiliate_wp()->referrals->get_referrals( $args );

        if( $referrals ) {

            foreach( $referrals as $referral ) {

                $data[] = array(
                    'affiliate_id'  => $referral->affiliate_id,
                    'email'         => affwp_get_affiliate_email( $referral->affiliate_id ),
                    'payment_email' => affwp_get_affiliate_payment_email( $referral->affiliate_id ),
                    'amount'        => $referral->amount,
                    'currency'      => $referral->currency,
                    'description'   => $referral->description,
                    'campaign'      => $referral->campaign,
                    'reference'     => $referral->reference,
                    'context'       => $referral->context,
                    'status'        => $referral->status,
                    'date'          => $referral->date,
                );

            }

        }

        $data = apply_filters( 'affwp_export_get_data', $data );
        $data = apply_filters( 'affwp_export_get_data_' . $this->export_type, $data );

        return $data;
    }

}
