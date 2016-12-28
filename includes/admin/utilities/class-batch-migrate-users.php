<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch processor for migrating existing users to affiliate accounts.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Migrate_Users implements Batch_Process\With_PreFetch {

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
	 * Should be set following instantiation via __set().
	 *
	 * @access public
	 * @since  2.0
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Number of users to migrate per step.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int
	 */
	public $step_number = 100;

	/**
	 * Initializes values needed following instantiation.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param null|array $data Optional. Form data. Default null.
	 */
	public function init( $data = null ) {
		if ( null !== $data && ! empty( $data['roles'] ) ) {
			$this->roles = $data['roles'];
		}
	}

	/**
	 * Handles pre-fetching user IDs for accounts in migration.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_fetch() {
		$affiliate_user_ids = affiliate_wp()->utils->data->get( "{$this->batch_id}_user_ids" );

		if ( false === $affiliate_user_ids ) {
			$affiliate_user_ids = affiliate_wp()->affiliates->get_affiliates( array(
				'number' => -1,
				'fields' => 'user_id',
			) );

			affiliate_wp()->utils->data->write( "{$this->batch_id}_user_ids", $affiliate_user_ids );
		}

		$total_to_migrate = affiliate_wp()->utils->data->get( "{$this->batch_id}_total_count" );

		if ( false === $total_to_migrate ) {
			$users = get_users( array(
				'fields'   => 'ids',
				'role__in' => $this->roles,
				'number'   => -1,
				'exclude'  => $affiliate_user_ids,
			) );

			$total_to_migrate = count( $users );

			affiliate_wp()->utils->data->write( "{$this->batch_id}_total_count", $total_to_migrate, array( '%s', '%d', '%s' ) );
		}
	}

	/**
	 * Determines if the current user can run the user migration script.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool True if the user has permission, otherwise false.
	 */
	public function can_process() {
		return current_user_can( 'manage_affiliates' );
	}

	/**
	 * Executes a single step in the batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 * @return int|string|\WP_Error Next step number, 'done', or a WP_Error object.
	 */
	public function process_step( $step ) {
		if ( ! $this->roles ) {
			return new \WP_Error( 'no_roles_found', __( 'No user roles were selected for migration.', 'affiliate-wp' ) );
		}

		$current_count = affiliate_wp()->utils->data->get( "{$this->batch_id}_current_count", 0 );

		$args = array(
			'number'     => $this->step_number,
			'offset'     => ( $step - 1 ) * $this->step_number,
			'exclude'    => affiliate_wp()->utils->data->get( "{$this->batch_id}_user_ids", array() ),
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'role__in'   => $this->roles,
			'fields'     => array( 'ID', 'user_email', 'user_registered' )
		);

		$users = get_users( $args );

		if ( empty( $users ) ) {
			return 'done';
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

		$current_count = $current_count + count( $inserted );

		affiliate_wp()->utils->data->write( "{$this->batch_id}_current_count", $current_count, array( '%s', '%d', '%s' ) );

		$step++;

		return $step;
	}

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Current step.
	 * @return int Percentage completed.
	 */
	public function get_percentage_complete( $step ) {

		$percentage = 0;

		$current_count = affiliate_wp()->utils->data->get( "{$this->batch_id}_current_count", 0 );
		$total_count   = affiliate_wp()->utils->data->get( "{$this->batch_id}_total_count", 0 );

		if ( $total_count > 0 ) {
			$percentage = ( $current_count / $total_count ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Retrieves a message for the given code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {

		switch( $code ) {

			case 'done':
				$final_count = affiliate_wp()->utils->data->get( "{$this->batch_id}_current_count", 0 );

				$message = sprintf(
					_n(
						'%d affiliate was added successfully.',
						'%d affiliates were added successfully.',
						$final_count,
						'affiliate-wp'
					), number_format_i18n( $final_count )
				);
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

	/**
	 * Defines logic to execute after the batch processing is complete.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function finish() {
		// Clean up.
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_user_ids" );
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_total_count" );
		affiliate_wp()->utils->data->delete( "{$this->batch_id}_current_count" );
	}
}
