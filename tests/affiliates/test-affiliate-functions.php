<?php
/**
 * Tests for Affiliate functions in affiliate-functions.php.
 *
 * @group affiliates
 * @group functions
 */
class Affiliate_Functions_Tests extends WP_UnitTestCase {

	/**
	 * User ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_user_id = 0;

	/**
	 * User ID 2.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_user_id2 = 0;

	/**
	 * First affiliate test ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_affiliate_id = 0;

	/**
	 * Second affiliate test ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_affiliate_id2 = 0;

	/**
	 * Affiliate test object.
	 *
	 * @access protected
	 * @var stdClass
	 */
	protected $_affiliate_object;

	/**
	 * Affiliate test object 2.
	 *
	 * @access protected
	 * @var stdClass
	 */
	protected $_affiliate_object_2;

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// First user/affiliate.
		$this->_user_id          = $this->factory->user->create();
		$this->_affiliate_id     = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->_user_id
		) );
		$this->_affiliate_object = affwp_get_affiliate( $this->_affiliate_id );

		// Second user/affiliate.
		$this->_user_id2           = $this->factory->user->create();
		$this->_affiliate_id2      = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->_user_id2
		) );
		$this->_affiliate_object_2 = affwp_get_affiliate( $this->_affiliate_id2 );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_set_affiliate_status( 'active' );
	}

	//
	// Tests
	//

	/**
	 * @covers affwp_is_affiliate()
	 */
	public function test_is_affiliate_with_invalid_user_id_should_return_false() {
		$this->assertFalse( affwp_is_affiliate() );
	}

	/**
	 * @covers affwp_is_affiliate()
	 */
	public function test_is_affiliate_with_real_user_should_return_true() {
		$this->assertTrue( affwp_is_affiliate( $this->_user_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_id()
	 */
	public function test_get_affiliate_id_with_invalid_user_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_id() );
	}

	/**
	 * @covers affwp_get_affiliate_id()
	 */
	public function test_get_affiliate_id_with_real_user_should_return_a_real_affiliate_id() {
		$this->assertEquals( $this->_affiliate_id, affwp_get_affiliate_id( $this->_user_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_username()
	 */
	public function test_get_affiliate_username_with_invalid_user_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_username() );
	}

	/**
	 * @covers affwp_get_affiliate_username()
	 */
	public function test_get_affiliate_username_with_valid_user_should_return_username() {
		$user = get_user_by( 'id', $this->_user_id );

		$this->assertEquals( $user->data->user_login, affwp_get_affiliate_username( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_is_active_affiliate()
	 */
	public function test_is_active_affiliate_with_invalid_user_should_return_false() {
		$this->assertFalse( affwp_is_active_affiliate() );
	}

	/**
	 * @covers affwp_is_active_affiliate()
	 */
	public function test_is_active_affiliate_with_valid_user_should_return_true() {
		$this->assertTrue( affwp_is_active_affiliate( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_user_id()
	 */
	public function test_get_affiliate_user_id_with_invalid_affiliate_id_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_user_id( 0 ) );
	}

	/**
	 * @covers affwp_get_affiliate_user_id()
	 */
	public function test_get_affiliate_user_id_with_valid_affiliate_id_should_return_valid_user_id() {
		$this->assertEquals( $this->_user_id, affwp_get_affiliate_user_id( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_user_id()
	 */
	public function test_get_affiliate_user_id_with_invalid_affiliate_object_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_user_id( new stdClass() ) );
	}

	/**
	 * @covers affwp_get_affiliate_user_id()
	 */
	public function test_get_affiliate_user_id_with_valid_affiliate_object_should_return_valid_user_id() {
		$this->assertEquals( $this->_user_id, affwp_get_affiliate_user_id( $this->_affiliate_object ) );
	}

	/**
	 * @covers affwp_get_affiliate()
	 */
	public function test_get_affiliate_should_accept_an_affiliate_id() {
		$this->assertEquals( $this->_affiliate_id, $this->_affiliate_object->affiliate_id );
	}

	/**
	 * @covers affwp_get_affiliate()
	 */
	public function test_get_affiliate_should_accept_an_affiliate_object() {
		$affiliate = affiliate_wp()->affiliates->get( $this->_affiliate_id );
		$affiliate = affwp_get_affiliate( $affiliate );

		$this->assertInstanceOf( 'stdClass', $affiliate );
		$this->assertEquals( $this->_affiliate_id, $affiliate->affiliate_id );
	}

	/**
	 * @covers affwp_get_affiliate()
	 */
	public function test_get_affiliate_passed_invalid_id_should_return_false() {
		$this->assertFalse( affwp_get_affiliate( null ) );
	}

	/**
	 * @covers affwp_get_affiliate()
	 */
	public function test_get_affiliate_passed_invalid_affiliate_object_should_return_false() {
		$this->assertFalse( affwp_get_affiliate( new stdClass() ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::add()
	 */
	function test_add_affiliate() {

		$args = array(
			'user_id'  => $this->_user_id
		);

		$affiliate_id = affiliate_wp()->affiliates->add( $args );

		$this->assertFalse( $affiliate_id );

		$args = array(
			'user_id'  => 2
		);

		$this->_affiliate_id2 = affiliate_wp()->affiliates->add( $args );

		$this->assertGreaterThan( 0, $this->_affiliate_id2 );

	}

	/**
	 * @covers affwp_update_affiliate()
	 */
	function test_update_affiliate() {

		$args = array(
			'affiliate_id'   => $this->_affiliate_id,
			'rate'           => '20',
			'account_email'  => 'testaccount@test.com'
		);

		$updated = affwp_update_affiliate( $args );

		$this->assertTrue( $updated );

	}

	/**
	 * @covers affwp_delete_affiliate()
	 */
	function test_delete_affiliate() {

		affwp_delete_affiliate( $this->_affiliate_id2 );

		// Re-retrieve following deletion.
		$affiliate = affwp_get_affiliate( $this->_affiliate_id2 );

		$this->assertNull( $affiliate );
	}

	/**
	 * @covers affwp_get_affiliate_status()
	 */
	public function test_get_affiliate_status_passed_invalid_affiliate_id_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_status( 1 ) );
	}

	/**
	 * @covers affwp_get_affiliate_status()
	 */
	public function test_get_affiliate_status_passed_valid_affiliate_id_should_return_status() {
		$this->assertEquals( 'active', affwp_get_affiliate_status( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_status()
	 */
	public function test_get_affiliate_status_passed_invalid_affiliate_object_should_return_false() {
		$this->assertFalse( affwp_get_affiliate_status( new stdClass() ) );
	}

	/**
	 * @covers affwp_get_affiliate_status()
	 */
	public function test_get_affiliate_status_passed_valid_affiliate_object_should_return_status() {
		$this->assertEquals( 'active', affwp_get_affiliate_status( $this->_affiliate_object ) );
	}

	/**
	 * @covers affwp_set_affiliate_status()
	 */
	public function test_set_affiliate_inactive_status() {
		$new_status = 'inactive';

		affwp_set_affiliate_status( $this->_affiliate_id, $new_status );

		$this->assertEquals( $new_status, affwp_get_affiliate_status( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_set_affiliate_status()
	 */
	public function test_set_affiliate_pending_status() {
		$new_status = 'pending';

		affwp_set_affiliate_status( $this->_affiliate_id, $new_status );

		$this->assertEquals( $new_status, affwp_get_affiliate_status( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_set_affiliate_status()
	 */
	public function test_set_affiliate_rejected_status() {
		$new_status = 'rejected';

		affwp_set_affiliate_status( $this->_affiliate_id, $new_status );

		$this->assertEquals( $new_status, affwp_get_affiliate_status( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_rate()
	 * @todo Separate tests for the other parameters
	 */
	public function test_get_affiliate_rate() {
		$this->assertEquals( '0.2', affwp_get_affiliate_rate( $this->_affiliate_id ) );
		$this->assertEquals( '20%', affwp_get_affiliate_rate( $this->_affiliate_id, true ) );
	}

	/**
	 * @covers affwp_affiliate_has_custom_rate()
	 */
	public function test_affiliate_has_custom_rate_passed_an_invalid_affiliate_id_should_always_return_false() {
		$this->assertFalse( affwp_affiliate_has_custom_rate() );
	}

	/**
	 * @covers affwp_affiliate_has_custom_rate()
	 */
	public function test_affiliate_has_custom_rate_passed_a_valid_affiliate_id_with_custom_rate_should_return_true() {
		$affiliate = affwp_update_affiliate( array(
			'affiliate_id' => $this->_affiliate_id,
			'rate'         => '0.1'
		) );

		$this->assertTrue( affwp_affiliate_has_custom_rate( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_affiliate_has_custom_rate()
	 */
	public function test_affiliate_has_custom_rate_passed_a_valid_affiliate_id_without_custom_rate_should_return_false() {
		$this->assertFalse( affwp_affiliate_has_custom_rate( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_rate_type()
	 */
	function test_get_affiliate_rate_type() {
		$this->assertEquals( 'percentage', affwp_get_affiliate_rate_type( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_rate_types()
	 */
	function test_get_affiliate_rate_types() {

		$this->assertArrayHasKey( 'percentage', affwp_get_affiliate_rate_types() );
		$this->assertArrayHasKey( 'flat', affwp_get_affiliate_rate_types() );
		$this->assertArrayNotHasKey( 'test', affwp_get_affiliate_rate_types() );

	}

	/**
	 * @covers affwp_get_affiliate_email()
	 */
	function test_get_affiliate_email() {

		$args = array(
			'affiliate_id'  => $this->_affiliate_id,
			'account_email' => 'affiliate@test.com'
		);

		affwp_update_affiliate( $args );

		$this->assertEquals( 'affiliate@test.com', affwp_get_affiliate_email( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_payment_email()
	 */
	function test_get_affiliate_payment_email() {

		$args = array(
			'affiliate_id'  => $this->_affiliate_id,
			'payment_email' => 'affiliate-payment@test.com'
		);

		affwp_update_affiliate( $args );

		$this->assertEquals( 'affiliate-payment@test.com', affwp_get_affiliate_payment_email( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_earnings()
	 */
	function test_get_affiliate_earnings() {

		$this->assertEquals( 0, affwp_get_affiliate_earnings( $this->_affiliate_id ) );

	}

	/**
	 * @covers affwp_get_affiliate_unpaid_earnings()
	 */
	function test_get_affiliate_unpaid_earnings() {

		$this->assertEquals( 0, affwp_get_affiliate_unpaid_earnings( $this->_affiliate_id ) );
		$this->assertEquals( '&#36;0', affwp_get_affiliate_unpaid_earnings( $this->_affiliate_id, true ) );

	}

	/**
	 * @covers affwp_increase_affiliate_earnings()
	 */
	public function test_increase_affiliate_earnings_should_increase_earnings() {
		$current = affwp_get_affiliate_earnings( $this->_affiliate_id );

		// Increase.
		affwp_increase_affiliate_earnings( $this->_affiliate_id, '10' );
		$this->assertEquals( $current + 10, affwp_get_affiliate_earnings( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_decrease_affiliate_earnings()
	 */
	public function test_decrease_affiliate_earnings_should_decrease_earnings() {
		$current = affwp_get_affiliate_earnings( $this->_affiliate_id );

		// Increase temporarily.
		affwp_increase_affiliate_earnings( $this->_affiliate_id, '10' );

		// Decrease.
		affwp_decrease_affiliate_earnings( $this->_affiliate_id, '10' );

		$this->assertEquals( $current, affwp_get_affiliate_earnings( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_referral_count()
	 */
	function test_get_affiliate_referral_count() {
		$this->assertEquals( 0, affwp_get_affiliate_referral_count( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_increase_affiliate_referral_count()
	 */
	public function test_increase_affiliate_referral_count_should_increase_count() {
		$current = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		// Increase.
		affwp_increase_affiliate_referral_count( $this->_affiliate_id );

		$this->assertEquals( ++$current, affwp_get_affiliate_referral_count( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_decrease_affiliate_referral_count()
	 */
	public function test_decrease_affiliate_referral_count_should_decrease_count() {
		$current = affwp_get_affiliate_referral_count( $this->_affiliate_id );

		// Increase temporarily.
		affwp_increase_affiliate_referral_count( $this->_affiliate_id );
		affwp_increase_affiliate_referral_count( $this->_affiliate_id );

		// Decrease.
		affwp_decrease_affiliate_referral_count( $this->_affiliate_id );

		$this->assertEquals( ++$current, affwp_get_affiliate_referral_count( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_visit_count()
	 */
	function test_get_affiliate_visit_count() {
		$this->assertEquals( 0, affwp_get_affiliate_visit_count( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_increase_affiliate_visit_count()
	 */
	public function test_increase_affiliate_visit_count_should_increase_count() {
		$current = affwp_get_affiliate_visit_count( $this->_affiliate_id );

		// ENHANCE!
		affwp_increase_affiliate_visit_count( $this->_affiliate_id );

		$new_count = affwp_get_affiliate_visit_count( $this->_affiliate_id );

		$this->assertNotEquals( $current, $new_count );
		$this->assertEquals( ++$current, $new_count );
	}

	/**
	 * @covers affwp_decrease_affiliate_visit_count()
	 */
	public function test_decrease_affiliate_visit_count_should_decrease_count() {
		$current = affwp_get_affiliate_visit_count( $this->_affiliate_id );

		// Increase temporarily.
		affwp_increase_affiliate_visit_count( $this->_affiliate_id );

		// Decrease. Should be back to the current count.
		affwp_decrease_affiliate_earnings( $current, affwp_get_affiliate_visit_count( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_decrease_affiliate_visit_count()
	 */
	public function test_decrease_affiliate_visit_count_for_no_visits_should_return_false() {
		$this->assertFalse( affwp_decrease_affiliate_visit_count(), $this->_affiliate_id2 );
	}

	/**
	 * @covers affwp_get_affiliate_conversion_rate()
	 */
	function test_get_affiliate_conversion_rate() {
		$this->assertEquals( '0%', affwp_get_affiliate_conversion_rate( $this->_affiliate_id ) );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_id()
	 */
	function test_get_affiliate_area_page_id_should_match_setting() {
		$page_id_from_settings = affiliate_wp()->settings->get( 'affiliates_page' );
		$this->assertSame( $page_id_from_settings, affwp_get_affiliate_area_page_id() );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_id()
	 */
	function test_get_affiliate_area_page_id_filtered_different_should_not_match_setting() {
		$page_id_from_settings = affiliate_wp()->settings->get( 'affiliates_page' );

		add_filter( 'affwp_affiliate_area_page_id', function() {
			return rand( 1, 100 );
		} );

		$page_id_from_helper = affwp_get_affiliate_area_page_id();

		$this->assertNotSame( $page_id_from_settings, $page_id_from_helper );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_url()
	 */
	function test_get_affiliate_area_page_url_should_match_settings() {
		$affiliates_page_id = affwp_get_affiliate_area_page_id();

		$this->assertSame( get_permalink( $affiliates_page_id ), affwp_get_affiliate_area_page_url() );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_url()
	 */
	function test_get_affiliate_area_page_url_with_valid_tab_should_return_tab_url() {
		$affiliates_page_id = affwp_get_affiliate_area_page_id();

		$tab_url = add_query_arg( 'tab', 'stats', get_permalink( $affiliates_page_id ) );

		$this->assertSame( $tab_url, affwp_get_affiliate_area_page_url( 'stats' ) );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_url()
	 */
	function test_get_affiliate_area_page_url_with_invalid_tab_should_return_page_url() {
		$this->assertSame( affwp_get_affiliate_area_page_url(), affwp_get_affiliate_area_page_url( rand_str( 1, 10 ) ) );
	}

	/**
	 * @covers affwp_get_affiliate_area_page_url()
	 */
	function test_get_affiliate_area_page_url_with_empty_tab_should_return_page_url() {
		$this->assertSame( affwp_get_affiliate_area_page_url(), affwp_get_affiliate_area_page_url( '' ) );
	}
}
