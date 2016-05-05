<?php
/**
 * Tests for AffWP_Visit
 *
 * @covers AffWP_Visit
 * @covers AffWP_Object
 *
 * @group visits
 * @group objects
 */
class AffWP_Visit_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_invalid_visit_id_should_return_false() {
		$this->assertFalse( AffWP_Visit::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_visit_id_should_return_AffWP_Visit_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $affiliate_id
		) );

		$visit_id = affiliate_wp()->visits->add( array(
			'referral_id'  => $referral_id,
			'affiliate_id' => $affiliate_id
		) );

		$visit = AffWP_Visit::get_instance( $visit_id );

		$this->assertInstanceOf( 'AffWP_Visit', $visit );
	}
}
