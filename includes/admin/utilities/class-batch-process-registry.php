<?php
namespace AffWP\Utils\Batch_Process;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch process registry class.
 *
 * @since 2.0
 */
class Registry {

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

		$this->includes();
		$this->register_core_processes();

		/**
		 * Fires during instantiation of the batch processing script.
		 *
		 * @since 2.0
		 *
		 * @param \AffWP\Utils\Batch_Process\Registry $this Registry instance.
		 */
		do_action( 'affwp_batch_process_init', $this );
	}

	/**
	 * Brings in core process files.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function includes() {
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-batch-process.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-batch-process-with-prefetch.php';

		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-migrate-users.php';
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
			'class' => 'AffWP\Utils\Batch_Process\Migrate_Users',
		) );

//		// WP Affiliate Migration.
//		$this->register_process( 'migrate-wp-affiliate', array(
//			'class' => 'Affiliate_WP_Migrate_WP_Affiliate',
//		) );
//
//		// Affiliates Pro Migration.
//		$this->register_process( 'migrate-affiliates-pro', array(
//			'class' => 'Affiliate_WP_Migrate_Affiliates_Pro',
//		) );

		//
		// Exporters
		//

//		// Export Settings.
//		$this->register_process( 'export-settings', array(
//			'class' => 'AffWP\Utils\Exporter\Settings',
//		) );
//
//		// Export Affiliates.
//		$this->register_process( 'export-affiliates', array(
//			'class' => 'Affiliate_WP_Affiliate_Export',
//		) );
//
//		// Export Referrals.
//		$this->register_process( 'export-referrals', array(
//			'class' => 'Affiliate_WP_Referral_Export',
//		) );
//
//		// Export Referrals Payout.
//		$this->register_process( 'export-referrals-payout', array(
//			'class' => 'Affiliate_WP_Referral_Payout_Export',
//		) );

		//
		// Importers
		//

//		// Import Settings.
//		$this->register_process( 'import-settings', array(
//			'class' => 'non-existent (yet)',
//		) );
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
			$process_args['step_method'] = 'process_step';
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
		if ( array_key_exists( $batch_id, $this->batch_ids ) ) {
			return $this->batch_ids[ $batch_id ];
		}
		return false;
	}

}
