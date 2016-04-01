<?php
/**
 * Tests for Affiliate_WP_Settings
 *
 * @covers Affiliate_WP_Settings
 * @group drew
 */
class Afilliate_Settings_Tests extends WP_UnitTestCase {

	/**
	 * Settings instance.
	 *
	 * @access public
	 * @var Affiliate_WP_Settings
	 */
	public $settings;

	/**
	 * Tests set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->settings = new Affiliate_WP_Settings();
	}

	/**
	 * @covers Affiliate_WP_Settings::set()
	 */
	public function test_set_method_should_update_options_property_in_memory_only() {
		$affiliates_area = rand( 1, 200 );
		$this->settings->set( array( 'affiliates_page' => $affiliates_area ) );

		$options = $this->settings->get_all();
		$actual  = get_option( 'affwp_settings', array() );

		$this->assertSame( $options['affiliates_page'], $affiliates_area );
		$this->assertNotSame( $actual['affiliates_page'], $affiliates_area );
	}

	/**
	 * @covers Affiliate_WP_Settings::set()
	 */
	public function test_set_method_with_save_trigger_should_update_settings() {
		$affiliates_area = rand( 1, 200 );
		$this->settings->set( array(
			'affiliates_page' => $affiliates_area
		), $save = true );

		$options = $this->settings->get_all();
		$actual  = get_option( 'affwp_settings', array() );

		$this->assertSame( $options['affiliates_page'], $affiliates_area );
		$this->assertSame( $actual['affiliates_page'], $affiliates_area );
	}

}
