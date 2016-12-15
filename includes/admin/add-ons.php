<?php
/**
 * Admin Add-ons
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

/**
 * Add-ons Page
 *
 * Renders the add-ons page content.
 *
 * @return void
 */
function affwp_add_ons_admin() {
	/**
	 * Filters the add-ons tabs.
	 *
	 * @param array $tabs Add-ons tabs.
	 */
	$add_ons_tabs = (array) apply_filters( 'affwp_add_ons_tabs', array(
		'pro'           => 'Pro',
		'official-free' => 'Official Free'
	) );

	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $add_ons_tabs ) ? $_GET['tab'] : 'pro';

	ob_start();
	?>
	<div class="wrap" id="affwp-add-ons">
		<h1>
			<?php _e( 'Add-ons for AffiliateWP', 'affiliate-wp' ); ?>
			<span>
				&nbsp;&nbsp;<a href="https://affiliatewp.com/addons/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=All%20Add-ons" class="button-primary" title="<?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?>" target="_blank"><?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?></a>
			</span>
		</h1>
		<p><?php _e( 'These add-ons <em><strong>add functionality</strong></em> to your AffiliateWP-powered site.', 'affiliate-wp' ); ?></p>
		<h2 class="nav-tab-wrapper">
			<?php affwp_navigation_tabs( $add_ons_tabs, $active_tab, array( 'settings-updated' => false ) ); ?>
		</h2>

		<div id="tab_container">

			<?php if ( 'pro' === $active_tab ) : ?>
				<p><?php printf( __( 'Pro add-ons are only available with a Professional or Ultimate license. If you already have one of these licenses, simply <a href="%s">log in to your account</a> to download any of these add-ons.', 'affiliate-wp' ), 'https://affiliatewp.com/account/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=Account' ); ?></p>
				<p><?php printf( __( 'If you have a Personal or Plus license, you can easily upgrade from your account page to <a href="%s">get access to all of these add-ons</a>!', 'affiliate-wp' ), 'https://affiliatewp.com/account/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=Account' ); ?></p>

			<?php else : ?>
				<p><?php _e( 'Our official free add-ons are available to all license holders!', 'affiliate-wp' ); ?></p>
			<?php endif; ?>
			<?php

			$add_ons = affwp_add_ons_get_feed( $active_tab );
			if( empty( $add_ons['error'] ) ) {

				foreach( $add_ons as $add_on ) {

					echo '<div class="affwp-add-on">';

						$url = add_query_arg( array(
							'utm_source'   => 'plugin-addons-page',
							'utm_medium'   => 'plugin',
							'utm_campaign' => 'AffWPAddonsPage',
							'utm_content'  => $add_on->info->title
						), $add_on->info->link );

						echo '<h3 class="affwp-add-on-title">' . $add_on->info->title . '</h3>';
						echo '<a href="' . esc_url( $url ) . '" target="_blank">';
							echo '<img class="wp-post-image" src="' . esc_url( $add_on->info->thumbnail ) . '"/>';
						echo '</a>';
						echo '<p>' . $add_on->info->excerpt . '</p>';
						echo '<a href="' . esc_url( $url ) . '" class="button-secondary" target="_blank">' . __( 'Learn more', 'affiliate-wp' ) . '</a>';

						if( ! affwp_has_pro_add_on_access() && 'pro' == $active_tab ) {
							echo '<a href="#" class="alignright button-primary affwp-upgrade">' . __( 'Upgrade for access', 'affiliate-wp' ) . '</a>';
						} else if( 'official-free' == $active_tab || ( affwp_has_pro_add_on_access() && 'pro' == $active_tab ) ) {
							if( ! empty( $add_on->files[0]->file ) ) {

								$install_url = $add_on->files[0]->file;
								echo '<a href="' . esc_url( $install_url ) . '" class="alignright button-primary affwp-upgrade affwp-install-add-on" data-name="' . esc_attr( $add_on->info->title ) . '">' . __( 'Install now', 'affiliate-wp' ) . '</a>';
							
							}
						}

					echo '</div>';

				}

			} else {
				echo '<p>' . $response['error'] . '</p>';
			}
			echo '<pre>'; print_r( $add_ons ); echo '</pre>';
			?>
			<div class="affwp-add-ons-footer">
				<a href="https://affiliatewp.com/addons/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=All%20Add-ons" class="button-primary" title="<?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?>" target="_blank"><?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	echo ob_get_clean();
}

/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @return void
 */
function affwp_add_ons_get_feed( $tab = 'pro' ) {

	$add_ons = get_transient( 'affiliatewp_json_add_ons_feed_' . $tab );

	if ( false === $add_ons ) {

		$url  = 'https://affiliatewp.com/edd-api/v2/products/?number=100&category=' . $tab;
		$feed = wp_remote_get( esc_url_raw( $url ), array( 'sslverify' => false ) );

		if ( ! is_wp_error( $feed ) ) {

			$body    = wp_remote_retrieve_body( $feed );
			$body    = json_decode( $body );
			$add_ons = array_map( 'affwp_sanitize_json_add_on_feed_item', $body->products );

			set_transient( 'affiliatewp_add_ons_feed_' . $tab, $add_ons, DAY_IN_SECONDS );

		} else {
			$add_ons = array(
				'error' => __( 'There was an error retrieving the add-ons list from the server. Please try again later.', 'affiliate-wp' )
			);
		}

	}

	return $add_ons;

}

function affwp_sanitize_json_add_on_feed_item( $add_on ) {

	// Remove post content field
	if( isset( $add_on->info->content ) ) {
		unset( $add_on->info->content );
	}

	return $add_on;
}

function affwp_has_pro_add_on_access() {
	return affiliate_wp()->settings->is_license_pro_or_ultimate();
}

function affwp_process_add_on_install() {

	$status  = array();
	$url     = sanitize_text_field( $_POST['url'] );
	$name    = sanitize_text_field( $_POST['name'] );
	$result  = affwp_install_add_on( $url );

	if ( is_wp_error( $result ) ) {
		$status['errorCode']    = $result->get_error_code();
		$status['errorMessage'] = $result->get_error_message();
		wp_send_json_error( $status );
	}

	$status['data'] = $result;

	wp_send_json_success( $status );

}
add_action( 'wp_ajax_affwp_install_add_on', 'affwp_process_add_on_install' );

function affwp_install_add_on( $download_url = '' ) {

	if( ! current_user_can( 'install_plugins' ) ) {
		return new WP_Error( 'no_permission', __( 'You do not have permission to install add-ons' ) );
	}

	if( empty( $download_url ) ) {
		return new WP_Error( 'missing_download', __( 'No download URL was provided' ) );
	}

	if( ! class_exists( 'Plugin_Upgrader' ) ) {
		return new WP_Error( 'wp_46', __( 'Installing add-ons directly requires WordPress 4.6 or later' ) );
	}

	$args = array( 
		'title'  => __( 'Installing add-on', 'affiliate-wp' ),
		'url'    => admin_url( 'admin.php?page=affiliate-wp-add-ons' ),
		'type'   => 'web',
		'nonce'  => 'plugin-upload',
	);

	$skin     = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	return $upgrader->install( $download_url );

}
