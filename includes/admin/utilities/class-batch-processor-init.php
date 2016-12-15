<?php
namespace AffWP\Utils\Batch_Processor;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Batch processor initialization class.
 *
 * @since 2.0
 */
class Init {

	/**
	 * Batches processes ID registry.
	 *
	 * @access private
	 * @since  2.0
	 * @var    array
	 */
	private $batch_ids = array();

	/**
	 * Instantiates the batch processor class.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {

		$this->register_core_processes();

		/**
		 * Fires during instantiation of the batch processing script.
		 *
		 * Processes not registered before priority 9999 will be ignored.
		 *
		 * @since 2.0
		 */
		do_action( 'affwp_batch_processor_init', 9999 );
	}

	/**
	 * Registers core batch processes.
	 *
	 * @access protected
	 * @since  2.0
	 */
	protected function register_core_processes() {
		//
		// Migrations
		//

		// User Migration.
		$this->register_process( 'migrate-users', array(
			'class'       => 'Affiliate_WP_Migrate_Users',
			'step_method' => 'do_users',
		) );

		// WP Affiliate Migration.
		$this->register_process( 'migrate-wp-affiliate', array(
			'class' => 'Affiliate_WP_Migrate_WP_Affiliate',
		) );

		// Affiliates Pro Migration.
		$this->register_process( 'migrate-affiliates-pro', array(
			'class' => 'Affiliate_WP_Migrate_Affiliates_Pro',
		) );

		//
		// Exporters
		//

		// Export Settings.
		$this->register_process( 'export-settings', array(
			'class' => 'AffWP\Utils\Exporter\Settings',
		) );

		// Export Affiliates.
		$this->register_process( 'export-affiliates', array(
			'class' => 'Affiliate_WP_Affiliate_Export',
		) );

		// Export Referrals.
		$this->register_process( 'export-referrals', array(
			'class' => 'Affiliate_WP_Referral_Export',
		) );

		// Export Referrals Payout.
		$this->register_process( 'export-referrals-payout', array(
			'class' => 'Affiliate_WP_Referral_Payout_Export',
		) );

		//
		// Importers
		//

		// Import Settings.
		$this->register_process( 'import-settings', array(
			'class' => 'non-existent (yet)',
		) );
	}

	/**
	 * Registers a new batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id     Unique batch process ID.
	 * @param array  $process_args {
	 *     Arguments for registering a new batch process.
	 *
	 *     @type string $class    Batch processor class to use.
	 *     @type string $step_method Optional. Step method to use via `$class`.
	 * }
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 */
	public function register_process( $batch_id, $process_args ) {
		$process_args = wp_parse_args( $process_args,  array_fill_keys( array( 'class', 'step_method' ), '' ) );

		if ( empty( $process_args['class'] ) ) {
			return new \WP_Error( 'invalid_batch_class', __( 'A batch process class must be specified', 'affiliate-wp' ) );
		}

		if ( ! method_exists( $process_args['class'], $process_args['step_method'] )
			|| empty( $process_args['step_method'] )
		) {
			$process_args['step_method'] = 'step_forward';
		}

		return $this->add_process( $batch_id, $process_args['class'], $process_args['step_method'] );
	}

	/**
	 * Adds a batch process to the registry.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int    $batch_id    Batch process ID.
	 * @param string $class       Class the batch processor should instantiate.
	 * @param string $step_method Step method the batch processor should use.
	 * @return true Always true.
	 */
	private function add_process( $batch_id, $class, $step_method ) {
		$this->batch_ids[ $batch_id ] = array(
			'class'       => $class,
			'step_method' => $step_method
		);
		return true;
	}

	/**
	 * Removes a batch process from the registry by ID.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function remove_process( $batch_id ) {
		unset( $this->batch_ids[ $batch_id ] );
	}

	/**
	 * Retrieves a batch process and its associated attributes.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch process ID.
	 * @return array|false Array of attributes for the batch process if registered, otherwise false.
	 */
	public function get( $batch_id ) {
		if ( in_array( $batch_id, $this->batch_ids, true ) ) {
			return $this->batch_ids[ $batch_id ];
		}
		return false;
	}

	/**
	 * Retrieves stored data by key.
	 *
	 * Given a key, get the information from the database directly.
	 *
	 * @access public
	 * @since  1.9.5
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	public function get_stored_data( $key ) {
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = '%s'", $key ) );

		return empty( $value ) ? false : maybe_unserialize( $value );
	}

	/**
	 * Store some data based on key and value.
	 *
	 * @access public
	 * @since  1.9.5
	 *
	 * @param string $key     The option_name.
	 * @param mixed  $value   The value to store.
	 * @param array  $formats Optional. Array of formats to pass for key, value, and autoload.
	 *                        Default empty (all strings).
	 */
	public function store_data( $key, $value, $formats = array() ) {
		global $wpdb;

		$value = maybe_serialize( $value );

		$data = array(
			'option_name'  => $key,
			'option_value' => $value,
			'autoload'     => 'no',
		);

		if ( empty( $formats ) ) {
			$formats = array(
				'%s', '%s', '%s',
			);
		}

		$wpdb->replace( $wpdb->options, $data, $formats );
	}

	/**
	 * Deletes a piece of stored data by key.
	 *
	 * @access public
	 * @since  1.9.5
	 *
	 * @param string $key The stored option name to delete.
	 */
	public function delete_data( $key ) {
		global $wpdb;

		$wpdb->delete( $wpdb->options, array( 'option_name' => $key ) );
	}

}
