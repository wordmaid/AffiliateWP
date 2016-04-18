<?php
/**
 * Graphs
 *
 * This class handles building and display of the AffiliateWP Reports Exporter tab
 *
 * @package     AffiliateWP
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */
class Affiliate_WP_Reports_Exporter {

	/**
	 * Output the exporter view
	 *
	 * @since 1.8
	 */
	public function display() {
		do_action( 'affwp_before_reports_exporter', $this );
		echo $this->exporter();
		do_action( 'affwp_after_reports_exporter', $this );
	}

	/**
	 * Build the exporter UI
	 *
	 * @since 1.8
	 */
	public function exporter() {
	?>

	<div id="affwp-dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div class="postbox">
					<h3><span><?php _e( 'Export a Report', 'affiliate-wp' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Export a report to a CSV file.', 'affiliate-wp' ); ?></p>
						<form method="post" enctype="multipart/form-data" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-reports&tab=exporter' ); ?>">
							<p>
								<span class="affwp-ajax-search-wrap">
									<input type="text" name="user_name" id="user_name" class="affwp-user-search" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
									<input type="hidden" name="user_id" id="user_id" value=""/>
									<img class="affwp-ajax waiting" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;"/>
								</span>
								<input type="text" class="affwp-datepicker" autocomplete="off" name="start_date" placeholder="<?php _e( 'From - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
								<input type="text" class="affwp-datepicker" autocomplete="off" name="end_date" placeholder="<?php _e( 'To - mm/dd/yyyy', 'affiliate-wp' ); ?>"/>
								<select name="status" id="status">
									<option value="0"><?php _e( 'All Statuses', 'affiliate-wp' ); ?></option>
									<option value="paid"><?php _e( 'Paid', 'affiliate-wp' ); ?></option>
									<option value="unpaid"><?php _e( 'Unpaid', 'affiliate-wp' ); ?></option>
									<option value="pending"><?php _e( 'Pending', 'affiliate-wp' ); ?></option>
									<option value="rejected"><?php _e( 'Rejected', 'affiliate-wp' ); ?></option>
								</select>
								<div id="affwp_user_search_results"></div>
								<div class="description"><?php _e( 'To search for an affiliate, enter the affiliate\'s login name, first name, or last name. Leave blank to export a report for all affiliates.', 'affiliate-wp' ); ?></div>
							</p>
							<p>
								<input type="hidden" name="affwp_action" value="export_report" />
								<?php wp_nonce_field( 'affwp_export_report_nonce', 'affwp_export_report_nonce' ); ?>
								<?php submit_button( __( 'Export Report', 'affiliate-wp' ), 'primary', 'submit', false ); ?>
							</p>
						</form>
					</div><!-- .inside -->
				</div><!-- .postbox -->
		</div><!-- .metabox-holder -->
	</div><!-- #affwp-dashboard-widgets-wrap -->
<?php
	}

	/**
	 * Build the CSV export file
	 *
	 * @since 1.8
	 */
	public function build_csv() {

	}
}

$affiliate_wp_reports_exporter = new Affiliate_WP_Reports_Exporter;
