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
     * This method must retrieve data from the current instance
     * of the AffWP_Export_Reports_List_Table class.
     *
     * @access public
     * @since 1.8
     * @return array $data Data for Export
     */
    public function get_list_table_data( $affiliates ) {

        return $affiliates;
    }

    /**
     * Output the CSV rows
     *
     * @access public
     * @since 1.8
     * @return void
     */
    public function csv_rows_out() {
        $data = $this->get_list_table_data( $affiliates );

        $cols = $this->get_csv_cols();

        // Output each row
        foreach ( $data as $row ) {
            $i = 1;
            foreach ( $row as $col_id => $column ) {
                // Make sure the column is valid
                if ( array_key_exists( $col_id, $cols ) ) {
                    echo '"' . $column . '"';
                    echo $i == count( $cols ) + 1 ? '' : ',';
                }

                $i++;
            }
            echo "\r\n";
        }
    }

}
