<?php
namespace AffWP\Creative\Database;

use AffWP\Tests\UnitTestCase;

/**
 * Tests for Affiliate_WP_Creatives_DB class
 *
 * @covers Affiliate_WP_Creatives_DB
 * @group database
 * @group creatives
 */
class Tests extends UnitTestCase {

	/**
	 * Creatives fixture.
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	public static $creatives = array();

	/**
	 * Set up fixtures once.
	 */
	public static function wpSetUpBeforeClass() {
		self::$creatives = parent::affwp()->creative->create_many( 4 );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::$cache_group
	 */
	public function test_cache_group_should_be_creatives() {
		$this->assertSame( 'creatives', affiliate_wp()->creatives->cache_group );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::$query_object_type
	 */
	public function test_query_object_type_should_be_AffWP_Creative() {
		$this->assertSame( 'AffWP\Creative', affiliate_wp()->creatives->query_object_type );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::$primary_key
	 */
	public function test_primary_key_should_be_creative_id() {
		$this->assertSame( 'creative_id', affiliate_wp()->creatives->primary_key );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::$REST
	 */
	public function test_REST_should_be_AffWP_Creative_REST_v1_Endpoints() {
		$this->assertSame( 'AffWP\Creative\REST\v1\Endpoints', get_class( affiliate_wp()->creatives->REST ) );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::get_object()
	 */
	public function test_get_object_with_invalid_creative_id_should_return_false() {
		$this->assertFalse( affiliate_wp()->creatives->get_object( 0 ) );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::get_object()
	 */
	public function test_get_object_with_valid_creative_id_should_return_a_valid_object() {
		$this->assertInstanceOf( 'AffWP\Creative', affiliate_wp()->creatives->get_object( self::$creatives[0] ) );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::get_object()
	 */
	public function test_get_object_with_valid_creative_object_should_return_a_valid_object() {
		$object = affwp_get_creative( self::$creatives[1] );

		$this->assertInstanceOf( 'AffWP\Creative', affiliate_wp()->creatives->get_object( $object ) );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::get_columns()
	 */
	public function test_get_columns_should_return_all_columns() {
		$expected = array(
			'creative_id'  => '%d',
			'name'         => '%s',
			'description'  => '%s',
			'url'          => '%s',
			'text'         => '%s',
			'image'        => '%s',
			'status'       => '%s',
			'date'         => '%s',
		);

		$this->assertEqualSets( $expected, affiliate_wp()->creatives->get_columns() );
	}

	/**
	 * @covers \Affiliate_WP_Creatives_DB::get_column_defaults()
	 */
	public function test_get_column_defaults_should_return_all_column_defaults() {
		$expected = array(
			'date' => date( 'Y-m-d H:i:s' ),
		);

		$this->assertEqualSets( $expected, affiliate_wp()->creatives->get_column_defaults() );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_should_return_array_of_Creative_objects_if_not_count_query() {
		$results = affiliate_wp()->creatives->get_creatives();

		// Check a random creative.
		$this->assertInstanceOf( 'AffWP\Creative', $results[0] );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_should_return_integer_if_count_query() {
		$results = affiliate_wp()->creatives->get_creatives( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_fields_ids_should_return_an_array_of_ids_only() {
		$results = affiliate_wp()->creatives->get_creatives( array(
			'fields' => 'ids'
		) );

		$this->assertEqualSets( self::$creatives, $results );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_invalid_fields_arg_should_return_regular_Creative_object_results() {
		$creatives = array_map( 'affwp_get_creative', self::$creatives );

		$results = affiliate_wp()->creatives->get_creatives( array(
			'fields' => 'foo'
		) );

		$this->assertEqualSets( $creatives, $results );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_single_creative_id_should_return_only_that_creative() {
		$results = affiliate_wp()->creatives->get_creatives( array(
			'creative_id' => self::$creatives[2],
			'fields'      => 'ids',
		) );

		$this->assertEqualSets( array( self::$creatives[2] ), $results );
	}

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_multiple_creative_ids_should_return_only_those_creatives() {
		$creatives = array( self::$creatives[1], self::$creatives[3] );

		$results = affiliate_wp()->creatives->get_creatives( array(
			'creative_id' => $creatives,
			'fields'      => 'ids',
		) );

		$this->assertEqualSets( $creatives, $results );
	}
}
