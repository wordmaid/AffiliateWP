<?php
/**
 * AffiliateWP Reports Data Filters Class
 *
 *
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 * @uses        AffWP_Data_Filters
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/class-data-filters.php';
    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-affiliate-reports-list-table.php';
    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-export-reports.php';

class AffWP_Reports_Data_Filters extends AffWP_Data_Filters {


    /**
     * The reports list table to use.
     *
     * @var   $reports_table
     * @since 1.8
     */
    public $reports_table;

    /**
     * The exporter to use.
     *
     * @var $exporter
     * @since 1.8
     */
    public $exporter;

    public function __construct() {

        $this->reports_table = new AffWP_Affiliate_Reports_List_Table();
        $this->exporter      = new Affiliate_WP_Reports_Export();
    }

    /**
     * Prepare list table items
     *
     * @since  1.8
     *
     * @return mixed The list table data to display
     */
    public function data_prepare_items() {
        $this->reports_table->prepare_items();
        $this->exporter->get_list_table_data( $this->reports_table->affiliate_date() );
    }

    /**
     * The views method of the list table.
     *
     * @since  1.8
     *
     * @return mixed List table views
     */
    public function data_views() {
        $this->reports_table->views();
    }

    /**
     * Display the advanced filters for the list table
     *
     * @since  1.8
     *
     * @return Advanced filters method from the list table.
     */
    public function data_advanced_filters() {
        $this->reports_table->advanced_filters();
    }

    /**
     * Display the exporter button and the list table
     *
     * @since  1.8
     *
     * @return Exporter and list table display methods.
     */
    public function data_display() {
        // Display exporter
        $this->exporter->display();
        // Output list table
        $this->reports_table->display();
    }
}
