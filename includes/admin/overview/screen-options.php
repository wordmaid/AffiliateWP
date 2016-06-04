<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add screen options to the Overview page
 *
 * @since 1.9
 */
function affwp_overview_screen_options() {

	$screen = get_current_screen();
	if ( $screen->id !== 'toplevel_page_affiliate-wp' ) {
		return;
	}

	add_screen_option(
		'layout_columns',
		array( 'max'  => 3,
			'default' => 3,
			'option'  => 'layout_columns'
		)
	);

	wp_enqueue_script( 'postbox' );

	do_action( 'affwp_overview_screen_options', $screen );
}

add_action( 'load-toplevel_page_affiliate-wp', 'affwp_overview_screen_options' );
