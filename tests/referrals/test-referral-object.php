<?php
/**
 * Tests for AffWP_Referral
 *
 * @covers AffWP_Referral
 * @covers AffWP_Object
 *
 * @group referrals
 * @group objects
 */
class AffWP_Referral_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_invalid_referral_id_should_return_false() {
		$this->assertFalse( AffWP_Referral::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_referral_id_should_return_AffWP_Referral_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $affiliate_id
		) );

		$referral = AffWP_Referral::get_instance( $referral_id );

		$this->assertInstanceOf( 'AffWP_Referral', $referral );
	}
}
