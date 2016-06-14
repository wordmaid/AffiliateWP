<?php
/**
 * Tests for AffWP_Affiliate
 *
 * @covers AffWP_Affiliate
 * @covers AffWP_Object
 *
 * @group affiliates
 * @group objects
 */
class AffWP_Affiliate_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_invalid_affiliate_id_should_return_false() {
		$this->assertFalse( AffWP_Affiliate::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_affiliate_id_should_return_AffWP_Affiliate_object() {
		$user_id = $this->factory->user->create();

		$affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $user_id
		) );

		$affiliate = AffWP_Affiliate::get_instance( $affiliate_id );

		$this->assertInstanceOf( 'AffWP_Affiliate', $affiliate );
	}

}
