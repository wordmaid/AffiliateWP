<?php
namespace AffWP\Misc\Functions;

use AffWP\Tests\UnitTestCase;

/**
 * Tests for includes/misc-functions.php
 *
 * @group misc
 * @group functions
 */
class Tests extends UnitTestCase {

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
	 * @covers ::affwp_format_amount()
	 */
	public function test_format_amount_floatval_remains_floatval_with_comma_thousands_seperator() {
		affiliate_wp()->settings->set( array(
			'thousands_separator' => ','
		) );

		$object = $this;

		add_filter( 'affwp_format_amount', function ( $formatted, $amount ) use ( $object ) {
			$object->assertSame( 'double', gettype( $amount ) );
		}, 10, 2 );

		$amount = affwp_format_amount( floatval( "1,999.99" ), true );
	}

	/**
	 * @covers ::affwp_get_currency()
	 */
	public function test_get_currency_should_default_to_USD() {
		$currency = affwp_get_currency();

		$this->assertEquals( 'USD', $currency );
	}

	/**
	 * @covers ::affwp_get_currency()
	 */
	public function test_get_currency_modified_should_return_new_currency() {
		$new = 'CZK';

		affiliate_wp()->settings->set( array( 'currency' => $new ) );

		$currency = affwp_get_currency();

		$this->assertEquals( $new, $currency );
	}

	/**
	 * @covers ::affwp_get_currency()
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
	 * @covers ::affwp_get_decimal_count()
	 */
	public function test_get_decimal_count_default_should_be_2() {
		$count = affwp_get_decimal_count();

		$this->assertEquals( 2, $count );
	}

	/**
	 * @covers ::affwp_get_decimal_count()
	 */
	public function test_filtered_get_decimal_count_should_return_filtered() {
		add_filter( 'affwp_decimal_count', function() {
			return 3;
		} );

		$count = affwp_get_decimal_count();

		$this->assertEquals( 3, $count );

		remove_all_filters( 'affwp_decimal_count' );
	}

	/**
	 * @dataProvider _make_url_human_readable_dp
	 * @covers ::affwp_make_url_human_readable()
	 */
	public function test_make_url_human_readable( $ugly, $human ) {
		$this->assertEquals( $human, affwp_make_url_human_readable( $ugly ) );
	}

	/**
	 * Data provider for affwp_make_url_human_readable()
	 *
	 * @since 1.8
	 */
	public function _make_url_human_readable_dp() {
		return array(
			array( 'http://www.example.com', 'www.example.com' ),
			array( 'http://www.example.com/', 'www.example.com' ),
			array( 'http://www.example.com/blog', '../blog/' ),
			array( 'http://www.example.com/blog/', '../blog/' ),
			array( 'http://www.example.com/2016/04/01/april-fools', '../2016/04/01/april-fools/' ),
			array( 'http://www.example.com/2016/04/01/april-fools/', '../2016/04/01/april-fools/' ),
			array( 'http://www.example.com/?s=My+query', 'www.example.com/?s=My+query' ),
			array( 'http://www.example.com/?privateVar=stuff', 'www.example.com' ),
			array( 'http://www.example.com/blog/?s=My+query', '../blog/?s=My+query' ),
			array( 'http://www.example.com/blog/?privateVar=stuff', '../blog/' )
		);
	}

	/**
	 * @covers ::affwp_format_rate()
	 */
	public function test_format_rate_should_format_percentage_as_percentage() {
		$this->assertSame( '20%', affwp_format_rate( 0.2 ) );
	}

	/**
	 * @covers ::affwp_format_rate()
	 */
	public function test_format_rate_should_format_non_percentage_as_flat() {
		$this->assertSame( '&#36;20', affwp_format_rate( 20, 'flat' ) );
	}

	/*
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_remove_commas() {
		$this->assertSame( '20000.20', affwp_sanitize_amount( '20,000.20' ) );
	}

	/**
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_remove_spaces() {
		$this->assertSame( '20000.20', affwp_sanitize_amount( '20 000.20' ) );
	}

	/**
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_default_to_two_decimals() {
		$this->assertSame( '20.20', affwp_sanitize_amount( '20.2' ) );
		$this->assertSame( '25.42', affwp_sanitize_amount( '25.42221112993' ) );
	}

	/**
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_remove_currency_symols() {
		$this->assertSame( '20.20', affwp_sanitize_amount( '$20.2' ) );
	}

	/**
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_remove_currency_symbols_and_default_to_two_decimals() {
		$this->assertSame( '10.00', affwp_sanitize_amount( '£10') );
		$this->assertSame( '20.20', affwp_sanitize_amount( '₱20.2' ) );
	}

	/**
	 * @covers ::affwp_sanitize_amount
	 */
	public function test_sanitize_amount_should_handle_nonstandard_thousands_and_decimal_separators() {
		affiliate_wp()->settings->set( array(
			'thousands_separator' => '.',
			'decimal_separator' => ','
		) );

		$this->assertSame( '10000.00', affwp_sanitize_amount( '10.000,00' ) );
		$this->assertSame( '10.00', affwp_sanitize_amount( '10,00' ) );

		// Clean up.
		affiliate_wp()->settings->set( array(
			'thousands_separator' => ',',
			'decimal_separator' => '.'
		) );

	}

	/**
	 * @covers ::affwp_get_logout_url
	 */
	public function test_affwp_get_logout_url() {
		$this->assertSame( wp_logout_url( get_permalink() ), affwp_get_logout_url() );
	}

	/**
	 * @covers ::affwp_get_logout_url
	 */
	public function test_affwp_get_logout_url_with_empty_filter() {
		add_filter( 'affwp_logout_url', '__return_empty_string' );
		$this->assertSame('', affwp_get_logout_url() );
	}

	/**
	 * @covers ::affwp_get_logout_url
	 */
	public function test_affwp_get_logout_url_with_custom_url() {
		add_filter( 'affwp_logout_url', function() {
			return home_url( '?action=logout' );
		} );
		$this->assertSame( home_url( '?action=logout' ), affwp_get_logout_url() );
	}

	/**
	 * @dataProvider _admin_urls
	 * @covers ::affwp_admin_url()
	 */
	public function test_admin_url_types( $expected, $actual ) {
		$this->assertSame( $expected, $actual );
	}


	/**
	 * Data provider for admin URLs tests.
	 */
	public function _admin_urls() {
		return array(
			array( $this->_build_admin_url( '' ),           affwp_admin_url( '' ) ),
			array( $this->_build_admin_url( 'affiliates' ), affwp_admin_url( 'affiliates' ) ),
			array( $this->_build_admin_url( 'creatives' ),  affwp_admin_url( 'creatives' ) ),
			array( $this->_build_admin_url( 'payouts' ),    affwp_admin_url( 'payouts' ) ),
			array( $this->_build_admin_url( 'referrals' ),  affwp_admin_url( 'referrals' ) ),
			array( $this->_build_admin_url( 'visits' ),     affwp_admin_url( 'visits' ) ),
			array( $this->_build_admin_url( 'reports' ),    affwp_admin_url( 'reports' ) ),
			array( $this->_build_admin_url( 'settings' ),   affwp_admin_url( 'settings' ) ),
			array( $this->_build_admin_url( 'tools' ),      affwp_admin_url( 'tools' ) ),
			array( $this->_build_admin_url( 'add-ons' ),    affwp_admin_url( 'add-ons' ) ),
		);
	}

	/**
	 * Helper that builds a simple admin URL.
	 *
	 * @param string $type Optional. Admin URL type. Default empty.
	 * @return string Admin URL.
	 */
	protected function _build_admin_url( $type = '' ) {
		if ( ! empty( $type ) ) {
			$type = "-{$type}";
		}

		return 'http://' . WP_TESTS_DOMAIN . "/wp-admin/admin.php?page=affiliate-wp{$type}";
	}

	/**
	 * @covers ::affwp_admin_url()
	 */
	public function test_admin_url_should_return_affiliate_wp_page_if_invalid_type() {
		$this->assertSame( 'http://' . WP_TESTS_DOMAIN . '/wp-admin/admin.php?page=affiliate-wp', affwp_admin_url( 'foo' ) );
	}

}
