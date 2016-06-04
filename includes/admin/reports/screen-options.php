<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add screen options to the Reports page
 *
 * @since 1.9
 */
function affwp_reports_screen_options() {

	$screen = get_current_screen();

	if ( $screen->id !== 'affiliates_page_affiliate-wp-reports' ) {
		return;
	}

	add_screen_option(
		'per_page',
		array(
			'label'   => __( 'Items per page', 'affiliate-wp' ),
			'option'  => 'affwp_reports_items_per_page',
			'default' => 30,
		)
	);

	add_screen_option(
		'layout_columns',
		array( 'max'  => 3,
			'default' => 3,
			'option'  => 'layout_columns'
		)
	);

	wp_enqueue_script( 'postbox' );

	do_action( 'affwp_reports_screen_options', $screen );
}

add_action( 'load-affiliates_page_affiliate-wp-reports', 'affwp_reports_screen_options' );

/**
 * Per-page screen option value for this page.
 *
 * @since  1.9
 * @param  bool|int $status
 * @param  string   $option
 * @param  mixed    $value
 * @return mixed
 */
function affwp_reports_set_screen_option( $status, $option, $value ) {

	if ( 'affwp_reports_items_per_page' === $option ) {
		return $value;
	}

	return $status;
}

add_filter( 'set-screen-option', 'affwp_reports_set_screen_option', 10, 3 );
