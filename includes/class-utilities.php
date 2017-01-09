<?php
use AffWP\Utils;

/**
 * Utilities class for AffiliateWP.
 *
 * @since 2.0
 */
class Affiliate_WP_Utilities {

	/**
	 * Batch process registry class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Batch_Process\Registry
	 */
	public $batch;

	/**
	 * Temporary data storage class instance variable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    \AffWP\Utils\Data_Storage
	 */
	public $data;

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
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-process-registry.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-data-storage.php';
	}

	/**
	 * Sets up utility objects.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function setup_objects() {
		$this->batch = new Utils\Batch_Process\Registry;
		$this->data  = new Utils\Data_Storage;
	}

	/**
	 * Performs processes on post data depending on the given context.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array  $data    Post data.
	 * @param string $old_key Optional. Old key under which to process data. Default empty.
	 * @return array (Maybe) processed post data.
	 */
	public function process_post_data( $data, $old_key = '' ) {
		switch ( $old_key ) {
			case 'user_name':
			case '_affwp_affiliate_user_name':
			case 'affwp_pms_user_name':
				if ( ! empty( $data[ $old_key ] ) ) {
					$username = sanitize_text_field( $data[ $old_key ] );

					if ( $user = get_user_by( 'login', $username ) ) {
						$data['user_id'] = $user->ID;

						unset( $data[ $old_key ] );
					} else {
						$data['user_id'] = 0;
					}
				}
				break;

			default : break;
		}
		return $data;
	}

}
