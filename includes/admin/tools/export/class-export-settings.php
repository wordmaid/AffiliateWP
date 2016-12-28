<?php
/**
 * Export Settings Class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */
namespace AffWP\Utils\Exporter;

use AffWP\Utils\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements an exporter for AffiliateWP settings.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Exporter\Base
 */
class Settings implements Exporter\Base {

	/**
	 * Export type.
	 *
	 * Used for export-type specific filters/actions
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $export_type = 'settings';

	/**
	 * Whether the current user can export settings.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool Whether the current user can initiate an export.
	 */
	public function can_export() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Handles sending the appropriate headers for exporting settings.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! ini_get( 'safe_mode' ) )
			set_time_limit( 0 );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=affwp-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
	}

	/**
	 * Retrieves the settings to export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int $step Optional. Current step. Default 1.
	 * @return array $data Settings data for export.
	 */
	public function get_data( $step = 1 ) {
		return get_option( 'affwp_settings' );
	}

	/**
	 * Handles outputting the settings as a json file.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function export() {
		if ( ! $this->can_export() ) {
			wp_die( __( 'You do not have permission to export data.', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		$this->headers();

		echo json_encode( $this->get_data() );
	}
}
