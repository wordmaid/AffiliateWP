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
     * [$reports_table description]
     *
     * @var [type]
     */
    public $reports_table;

    /**
     * [$exporter description]
     *
     * @var [type]
     */
    public $exporter;

    public function __construct() {

        $this->reports_table = new AffWP_Affiliate_Reports_List_Table();
        $this->exporter      = new Affiliate_WP_Reports_Export();
    }

    public function prepare_items() {
        $this->reports_table->prepare_items();
        $this->exporter->get_list_table_data( $this->reports_table->affiliate_date() );
    }

    public function views() {
        $this->reports_table->views();
    }

    public function advanced_filters() {
        $this->reports_table->advanced_filters();
    }

    public function display() {

            // Display exporter
            $this->exporter->display();
            // Output list table
            $this->reports_table->display();
    }
}
