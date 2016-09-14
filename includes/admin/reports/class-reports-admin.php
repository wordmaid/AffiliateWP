<?php
/**
 * Affiliates Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
namespace AffWP\Admin;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/abstracts/class-affwp-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-affiliates-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-referrals-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-visits-reports-tab.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/tabs/class-campaigns-reports-tab.php';

class Reports {

	/**
	 * Sets up the Reports admin.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function __construct() {
		add_action( 'affwp_reports_tabs_init', array( $this, 'register_core_tabs' ) );

		$this->display();
	}

	/**
	 * Renders the admin area.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function display() {
		/**
		 * Initializes Reports tabs.
		 *
		 * @since 1.9
		 */
		do_action( 'affwp_reports_tabs_init' );

		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->get_reports_tabs() ) ? $_GET['tab'] : 'referrals';

		?>
		<div class="wrap">

			<h1><?php _e( 'Reports', 'affiliate-wp' ); ?></h1>

			<?php do_action( 'affwp_reports_page_top' ); ?>

			<h2 class="nav-tab-wrapper">
				<?php
				$tabs = $this->get_reports_tabs();
				foreach ( $tabs as $tab_id => $tab_name ) {

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
	 * Retrieves the Reports tabs.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return array $tabs Tabs array.
	 */
	public function get_reports_tabs() {
		/**
		 * Filters the tabs displayed on the Reports screen.
		 *
		 * Tabs are added by extending AffWP\Admin\Reports\Tab.
		 *
		 * @since 1.1
		 *
		 * @see \AffWP\Admin\Reports\Tab
		 *
		 * @param array $tabs Tabs array.
		 */
		return apply_filters( 'affwp_reports_tabs', array() );
	}

	/**
	 * Registers the core Reports tabs.
	 *
	 * Hooked to {@see 'affwp_reports_tabs_init'}.
	 *
	 * @access public
	 * @since  1.9
	 */
	public function register_core_tabs() {
		new \AffWP\Referral\Admin\Reports\Tab;
		new \AffWP\Affiliate\Admin\Reports\Tab;
		new \AffWP\Visit\Admin\Reports\Tab;
		new \AffWP\Campaign\Admin\Reports\Tab;
	}
}
