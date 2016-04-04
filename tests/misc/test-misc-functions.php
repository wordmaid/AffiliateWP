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
	public function test_format_amount_floatval_remains_floatval_with_comma_thousands_seperator() {
		affiliate_wp()->settings->set( array(
			'thousands_separator' => ','
		) );

		add_filter( 'affwp_format_amount', function( $formatted, $amount ) use ( &$this ) {
			$this->amount = $amount;
			return $formatted;
		}, 10, 2 );

		$amount = affwp_format_amount( floatval( "1,999.99" ), true );

		$this->assertSame( 'double', gettype( $this->amount ) );
	}

	/**
	 * @covers affwp_get_currency()
	 */
	public function test_get_currency_should_default_to_USD() {
		$currency = affwp_get_currency();

		$this->assertEquals( 'USD', $currency );
	}

	/**
	 * @covers affwp_get_currency()
	 */
	public function test_get_currency_modified_should_return_new_currency() {
		$new = 'CZK';

		affiliate_wp()->settings->set( array( 'currency' => $new ) );

		$currency = affwp_get_currency();

		$this->assertEquals( $new, $currency );
	}

	/**
	 * @covers affwp_get_currency()
	 */
	public function test_get_currency_filtered_should_return_filtered_currency() {
		$currency = affwp_get_currency();

		$this->assertEquals( $currency, 'USD' );

		add_filter( 'affwp_currency', function() {
			return 'ZAR';
		} );

		$new_currency = affwp_get_currency();

		$this->assertEquals( 'ZAR', $new_currency );
		$this->assertNotEquals( 'USD', $new_currency );
	}

	/**
	 * @covers affwp_get_decimal_count()
	 */
	public function test_get_decimal_count_default_should_be_2() {
		$count = affwp_get_decimal_count();

		$this->assertEquals( 2, $count );
	}

	/**
	 * @covers affwp_get_decimal_count()
	 */
	public function test_filtered_get_decimal_count_should_return_filtered() {
		add_filter( 'affwp_decimal_count', function() {
			return 3;
		} );

		$count = affwp_get_decimal_count();

		$this->assertEquals( 3, $count );
	}
}
