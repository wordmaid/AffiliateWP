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
    public $export_type = 'affiliates';

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
                <?php submit_button( __( 'Export CSV', 'affiliate-wp' ), 'primary', 'submit', false ); ?>
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
     * This method must retrieve data from the current instance
     * of the AffWP_Export_Reports_List_Table class.
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
