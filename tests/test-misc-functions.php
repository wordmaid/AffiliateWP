<?php
/**
 * Tests for includes/misc-functions.php
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
	 * @covers affwp_format_amount()
	 */
	public function test_affwp_format_amount_floatval_remains_floatval_with_comma_thousands_seperator() {
		affiliate_wp()->settings->set( 'thousands_separator', ',' );

		add_filter( 'affwp_format_amount', function( $formatted, $amount ) {
			$this->amount = $amount;
			return $formatted;
		}, 10, 2 );

		$value = floatval( "1,999.99" );

		$amount = affwp_format_amount( $value, true );

		$this->assertSame( 'double', gettype( $this->amount ) );
	}
}
