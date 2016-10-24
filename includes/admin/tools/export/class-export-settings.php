<?php
/**
 * Export Class
 *
 * This is the base class for all export methods. Each data export type (referrals, affiliates, visits) extends this class.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
namespace AffWP\Util\Exporter;

use AffWP\Util\Exporter;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements an exporter for AffiliateWP settings.
 *
 * @since 2.0
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
	 * @return array $data Settings data for export.
	 */
	public function get_data() {
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
		$this->headers();

		echo json_encode( $this->get_data() );
	}
}
