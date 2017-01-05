<?php
namespace AffWP\Utils\Importer;

use AffWP\Utils\Importer;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promise for structuring CSV importers.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Importer\Base
 */
interface CSV extends Importer\Base {

	/**
	 * Sets the CSV columns.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array<string,string> CSV columns.
	 */
	public function csv_cols();

	/**
	 * Retrieves the CSV columns array.
	 *
	 * Alias for csv_cols(), usually used to implement a filter on the return.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array<string,string> CSV columns.
	 */
	public function get_csv_cols();

	/**
	 * Outputs the CSV columns.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function csv_cols_out();

	/**
	 * Outputs the CSV rows.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function csv_rows_out();

}
