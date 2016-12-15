<?php
namespace AffWP\Utils\Batch_Processor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch processor for migrating existing users to affiliate accounts.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Processor\Base
 */
class Migrate_Users extends Base {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $batch_id = 'migrate-users';

	/**
	 * Roles to migrate found users from.
	 *
	 * Should be set following instantiation via __get().
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Executes a single step in the batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 * @return bool False if there is an error, otherwise true.
	 */
	public function process_step( $step ) {
		if ( ! $this->roles ) {

			return false;
		}

		$affiliate_user_ids = get_transient( 'affwp_migrate_users_user_ids' );

		if ( false === $affiliate_user_ids ) {
			$affiliate_user_ids = affiliate_wp()->affiliates->get_affiliates( array(
				'number' => -1,
				'fields' => 'user_id',
			) );

			set_transient( 'affwp_migrate_users_user_ids', $affiliate_user_ids, 10 * MINUTE_IN_SECONDS );
		}

		$args = array(
			'number'     => 100,
			'offset'     => ( $step - 1 ) * 100,
			'exclude'    => $affiliate_user_ids,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'role__in'   => $this->roles,
			'fields'     => array( 'ID', 'user_email', 'user_registered' )
		);

		$users = get_users( $args );

		if ( empty( $users ) ) {
			return false;
		}

		$inserted = array();

		foreach ( $users as $user ) {

			$args = array(
				'status'          => 'active',
				'user_id'         => $user->ID,
				'payment_email'	  => $user->user_email,
				'date_registered' => $user->user_registered
			);

			$inserted[] = affiliate_wp()->affiliates->insert( $args, 'affiliate' );

		}

		if ( ! $inserted ) {
			return false;
		}

		if ( ! $current_count = affiliate_wp()->utils->batch->get_stored_data( 'affwp_migrate_users_total_count' ) ) {
			$current_count = 0;
		}

		$current_count = $current_count + count( $inserted );

		affiliate_wp()->utils->batch->store_data( 'affwp_migrate_users_total_count', $current_count, array( '%s', '%d', '%s' ) );

		return true;

	}

	/**
	 * Retrieves the total count of migrated items.
	 *
	 * @access public
	 * @since  2.0
	 * @static
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	public static function get_items_total( $key ) {
		return affiliate_wp()->utils->batch->get_stored_data( $key );
	}

	/**
	 * Deletes the total count of migrated items.
	 *
	 * @access public
	 * @since  2.0
	 * @static
	 *
	 * @param string $key The stored option name to delete.
	 */
	public static function clear_items_total( $key ) {
		affiliate_wp()->utils->batch->delete_data( $key );
	}

	/**
	 * Defines logic to execute after the batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function finish() {
		// Clean up.
		delete_transient( 'affwp_migrate_users_user_ids' );
	}
}
