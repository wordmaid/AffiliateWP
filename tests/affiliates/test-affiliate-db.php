<?php
/**
 * Tests for Affiliate_WP_DB_Affiliates class
 *
 * @covers Affiliate_WP_DB_Affiliates
 * @group drew
 */
class Affiliate_DB_Tests extends WP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_by_display_name() {
		$display_name = 'Foo';

		$user = $this->factory->user->create_and_get( array(
			'display_name' => $display_name
		) );

		// Add the affiliate.
		affiliate_wp()->affiliates->add( array(
			'user_id' => $user->ID
		) );

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'foo'
		) );

		// Assert that results were found.
		$this->assertNotEmpty( $results );

		// Assert that the result we're looking for was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user->ID ) ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_by_user_login() {
		$user_login = 'foo_bar';

		$user = $this->factory->user->create_and_get( array(
			'user_login' => $user_login
		) );

		// Add the affiliate.
		affiliate_wp()->affiliates->add( array(
			'user_id' => $user->ID
		) );

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'foo'
		) );

		// Assert that results were found.
		$this->assertNotEmpty( $results );

		// Assert that the result we're looking for was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user->ID ) ) );
	}

	/**
	 * @covers Affiliate_WP_DB_Affiliates::get_affiliates()
	 */
	public function test_get_affiliates_method_should_allow_searching_either_display_name_or_user_login() {
		$display_name = 'Bar Baz';
		$user_login   = 'foo_bar';

		$user = $this->factory->user->create( array(
			'display_name' => $display_name,
			'user_login'   => 'Scooby Doo'
		) );

		$user2 = $this->factory->user->create( array(
			'display_name' => 'Garfield',
			'user_login'   => $user_login
		) );

		$user3 = $this->factory->user->create( array(
			'display_name' => 'Nemo',
			'user_login'   => 'Dory'
		) );

		// Add affiliates.
		foreach ( array( $user, $user2 ) as $id ) {
			affiliate_wp()->affiliates->add( array(
				'user_id' => $id
			) );
		}

		// Get affiliates based on the search term(s).
		$results = affiliate_wp()->affiliates->get_affiliates( array(
			'search' => 'bar'
		) );

		// Assert two results were found.
		$this->assertCount( 2, $results );

		// Assert that the first user was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user ) ) );

		// Assert that the second user was found.
		$this->assertNotEmpty( wp_list_filter( $results, array( 'user_id' => $user2 ) ) );

		// Assert that the third user wasn't found.
		$this->assertEmpty( wp_list_filter( $results, array( 'user_id' => $user3 ) ) );
	}

}
