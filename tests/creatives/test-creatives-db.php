<?php
/**
 * Tests for Affiliate_WP_Creatives_DB
 *
 * @covers Affiliate_WP_Creatives_DB
 * @group creatives
 */
class Creatives_DB_Tests extends WP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_Creatives_DB::__construct()
	 */
	public function test_creatives_network_wide_table_name_should_be_affiliate_wp_creatives() {
		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			$this->assertEquals( 'affiliate_wp_creatives', affiliate_wp()->creatives->table_name );
		}
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::__construct()
	 */
	public function test_creatives_not_network_wide_table_name_should_be_prefix_affiliate_wp_creatives() {
		if ( ! defined( 'AFFILIATE_WP_NETWORK_WIDE' ) ) {
			global $wpdb;

			$this->assertEquals( $wpdb->prefix . 'affiliate_wp_creatives', affiliate_wp()->creatives->table_name );
		}
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::__construct()
	 */
	public function test_creatives_primary_key_should_be_creative_id() {
		$this->assertEquals( 'creative_id', affiliate_wp()->creatives->primary_key );
	}


}
