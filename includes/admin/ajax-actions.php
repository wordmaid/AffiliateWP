<?php

// retrieves a list of users via live search
function affwp_search_users() {
	if ( empty( $_REQUEST['term'] ) ) {
		wp_die( -1 );
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( -1 );
	}

	$search_query = htmlentities2( trim( $_REQUEST['term'] ) );

	do_action( 'affwp_pre_search_users', $search_query );

	$args = array(
		'search_columns' => array( 'user_login', 'display_name', 'user_email' )
	);

	if ( isset( $_REQUEST['status'] ) ) {
		$status = mb_strtolower( htmlentities2( trim( $_REQUEST['status'] ) ) );

		switch ( $status ) {
			case 'none':
				$affiliates = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => 9999,
					)
				);
				$args = array( 'exclude' => array_map( 'absint', wp_list_pluck( $affiliates, 'user_id' ) ) );
				break;
			case 'any':
				$affiliates = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => 9999,
					)
				);
				$args = array( 'include' => array_map( 'absint', wp_list_pluck( $affiliates, 'user_id' ) ) );
				break;
			default:
				$affiliates = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => 9999,
						'status' => $status,
					)
				);
				$args = array( 'include' => array_map( 'absint', wp_list_pluck( $affiliates, 'user_id' ) ) );
		}
	}

	// Add search string to args.
	$args['search'] = '*' . mb_strtolower( htmlentities2( trim( $_REQUEST['term'] ) ) ) . '*';

	// Get users matching search.
	$found_users = get_users( $args );

	$user_list = array();

	if ( $found_users ) {
		foreach( $found_users as $user ) {
			$user_list[] = array(
				'label'   => $user->user_login . " ({$user->user_email})",
				'value'   => $user->user_login,
				'user_id' => $user->ID
			);
		}
	}

	wp_die( json_encode( $user_list ) );
}
add_action( 'wp_ajax_affwp_search_users', 'affwp_search_users' );

function affwp_change_affiliate_status() {
	// Need the affiliate ID to verify the nonce.
	if ( empty( $affiliate_id = $_REQUEST['affiliate'] ) ) {
		wp_send_json_error( new WP_Error( __( 'missing_affiliate_id', __( 'Missing affiliate ID', 'affiliate-wp' ) ) ) );
	}

	// Nonce.
	if ( ! isset( $_REQUEST['nonce'] )
	     || ( isset( $_REQUEST['nonce'] )
			&& ! wp_verify_nonce( $_REQUEST['nonce'], "affwp_change_affiliate_status_{$affiliate_id}" )
	     )
	) {
		wp_send_json_error( new WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'affiliate-wp' ) ) );
	}

	// Sanitize.
	$affiliate_id = ! empty( $_REQUEST['affiliate'] ) ? absint( $_REQUEST['affiliate'] ) : 0;
	$status       = ! empty( $_REQUEST['status'] ) ? sanitize_text_field( $_REQUEST['status'] ) : '';
	$statuses     = affwp_get_affiliate_status_labels();

	// We need all of these things to update the status.
	if ( 0 == $affiliate_id || empty( $status ) || ! array_key_exists( $status, $statuses ) ) {
		wp_send_json_error( new WP_Error( 'invalid_data', __( 'Invalid data', 'affiliate-wp' ) ) );
	}

	// Regenerate the nonce to allow for multiple in-place updates.
	$new_nonce = wp_create_nonce( "affwp_change_affiliate_status_{$affiliate_id}" );

	// Update the status.
	$updated = affwp_set_affiliate_status( $affiliate_id, $status );

	// Success.
	if ( $updated ) {
		wp_send_json_success( array(
			'nonce'      => $new_nonce,
			'new_status' => $status
		) );
	}
}
add_action( 'wp_ajax_affwp_change_affiliate_status', 'affwp_change_affiliate_status' );
