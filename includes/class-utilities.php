<?php
use AffWP\Utils;

/**
 * Utilities class for AffiliateWP.
 *
 * @since 2.0
 */
class Affiliate_WP_Utilities {

	/**
	 * Batch processor class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Batch_Processor\Init
	 */
	public $batch;

	/**
	 * Temporary storage class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Data_Storage
	 */
	public $storage;

	/**
	 * Instantiates the utilities class.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function __construct() {
		$this->includes();
		$this->setup_objects();
	}

	/**
	 * Includes necessary utility files.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function includes() {
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-processor-init.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-temp-storage-init.php';
	}

	/**
	 * Sets up utility objects.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function setup_objects() {
		$this->batch   = new Utils\Batch_Processor\Init;
		$this->storage = new Utils\Data_Storage;
	}
}
