<?php

class AffWP_Factory_For_Referrals extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
	}

	function create_many( $count, $args = array(), $generation_definitions = null ) {
		// Parent create_many() uses initial value of 1 and < $count.
		$count = $count - 1;

		return parent::create_many( $count, $args, $generation_definitions );
	}

	function create_object( $args ) {
		$affiliate = new AffWP_Factory_For_Affiliates();

		$args = array_merge( array(
			'affiliate_id' => $affiliate->create()
		), $args );
		return affiliate_wp()->referrals->add( $args );
	}

	function update_object( $referral_id, $fields ) {
		return affiliate_wp()->referrals->update( $referral_id, $fields, '', 'referral' );
	}

	function get_object_by_id( $referral_id ) {
		return affwp_get_referral( $referral_id );
	}
}
