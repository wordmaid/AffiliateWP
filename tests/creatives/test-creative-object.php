<?php
/**
 * Tests for AffWP_Creative
 *
 * @covers AffWP_Creative
 * @covers AffWP_Object
 *
 * @group creatives
 * @group objects
 */
class AffWP_Creative_Tests extends WP_UnitTestCase {

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( AffWP_Creative::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP_Object::get_instance()
	 */
	public function test_get_instance_with_creative_id_should_return_AffWP_Creative_object() {
		$creative_id = affiliate_wp()->creatives->add();

		$this->assertInstanceOf( 'AffWP_Creative', AffWP_Creative::get_instance( $creative_id ) );
	}
}
