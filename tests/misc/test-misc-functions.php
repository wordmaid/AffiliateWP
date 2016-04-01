<?php
/**
 * Tests for includes/misc-functions.php
 *
 * @group functions
 */
class Misc_Functions_Tests extends WP_UnitTestCase {

	/**
	 * Amount.
	 *
	 * @since 1.8
	 * @var float
	 */
	public $amount;

	/**
	 * Settings instance.
	 *
	 * @since 1.8
	 * @var Affiliate_WP_Settings
	 */
	public $settings;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->settings = new Affiliate_WP_Settings();
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		// Reset to USD at the end of each test.
		affiliate_wp()->settings->set( array( 'currency' => 'USD' ) );

		parent::tearDown();
	}

	/**
	 * @covers affwp_format_amount()
	 */
	public function test_affwp_format_amount_floatval_remains_floatval_with_comma_thousands_seperator() {
		affiliate_wp()->settings->set( array(
			'thousands_separator' => ','
		) );

		add_filter( 'affwp_format_amount', function( $formatted, $amount ) {
			$this->amount = $amount;
			return $formatted;
		}, 10, 2 );

		$amount = affwp_format_amount( floatval( "1,999.99" ), true );

		$this->assertSame( 'double', gettype( $this->amount ) );
	}
}
