<?php
/**
 * Tests for Affiliate_WP_Tracking.
 *
 * @covers Affiliate_WP_Tracking
 * @group tracking
 */
class Tracking_Tests extends WP_UnitTestCase {

	/**
	 * Tear down.
	 */
	public function tearDown() {
		$GLOBALS['wp_query']->is_paged = false;

		parent::tearDown();
	}

	/**
	 * @covers Affiliate_WP_Tracking::strip_referral_from_paged_urls()
	 */
	public function test_strip_referral_from_paged_urls_should_remove_query_string_referral_vars() {
		$this->is_paged();

		$referral_var = affiliate_wp()->tracking->get_referral_var();
		$url          = WP_TESTS_DOMAIN . "/foobar/page/2/?{$referral_var}=2";

		// Non-trailing slashed:
		$stripped = affiliate_wp()->tracking->strip_referral_from_paged_urls( $url );
		$this->assertSame( WP_TESTS_DOMAIN . '/foobar/page/2/', $stripped );
	}

	/**
	 * @covers Affiliate_WP_Tracking::strip_referral_from_paged_urls()
	 */
	public function test_strip_referral_from_paged_urls_should_remove_pretty_referral_vars() {
		$this->is_paged();

		$referral_var = affiliate_wp()->tracking->get_referral_var();
		$url          = WP_TESTS_DOMAIN . "/foobar/{$referral_var}/2/page/3/";

		$stripped = affiliate_wp()->tracking->strip_referral_from_paged_urls( $url );

		$this->assertSame( WP_TESTS_DOMAIN . '/foobar/page/3/', $stripped );
	}

	/**
	 * Set is_paged() to true.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function is_paged() {
		$GLOBALS['wp_query']->is_paged = true;
	}
}
