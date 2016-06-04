<?php
/**
 * Affiiates Overview
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Overview
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/screen-options.php';

// Overview Metaboxes
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-metabox-base.php';

include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-totals.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-registrations.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-most-valuable.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-recent-referrals.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/overview/metaboxes/class-metabox-overview-recent-referral-visits.php';

function affwp_affiliates_dashboard() { ?>
	<div class="wrap">

		<h2><?php _e( 'Overview', 'affiliate-wp' ); ?></h2>

		<?php do_action( 'affwp_overview_top' ); ?>

		<div id="affwp-dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">
					<?php do_action( 'affwp_overview_left' ); ?>
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'normal',   null ); ?>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_action( 'affwp_overview_center' ); ?>
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'side',     null ); ?>
				</div>

				<div id="postbox-container-3" class="postbox-container">
					<?php do_action( 'affwp_overview_right' ); ?>
					<?php do_meta_boxes( 'toplevel_page_affiliate-wp', 'advanced', null ); ?>
				</div>

			</div>
		</div>

		<?php do_action( 'affwp_overview_bottom' ); ?>

	</div>
<?php }
