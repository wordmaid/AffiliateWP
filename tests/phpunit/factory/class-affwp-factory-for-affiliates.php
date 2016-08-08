<?php

class AffWP_Factory_For_Affiliates extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
	}

	function create_many( $count, $args = array(), $generation_definitions = null ) {
		// Parent create_many() uses initial value of 1 and < $count.
		$count = $count - 1;

		return parent::create_many( $count, $args, $generation_definitions );
	}

	function create_object( $args ) {
		$user = new WP_UnitTest_Factory_For_User();

		$args = array_merge( array(
			'user_id' => $user->create()
		), $args );

		return affiliate_wp()->affiliates->add( $args );
	}

	function update_object( $affiliate_id, $fields ) {
		return affiliate_wp()->affiliates->update( $affiliate_id, $fields, '', 'affiliate' );
	}

	function get_object_by_id( $affiliate_id ) {
		return affwp_get_affiliate( $affiliate_id );
	}
}
