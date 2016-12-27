<?php
namespace AffWP\Utils\Batch_Process\Export;

use AffWP\Utils\Batch_Process;
use AffWP\Utils\Exporter;

/**
 * Implements a base CSV batch exporter.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Export
 * @see \AffWP\Utils\Exporter\CSV
 */
class CSV extends Batch_Process\Export implements Exporter\CSV {

	/**
	 * The export file type, e.g. '.csv'.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filetype = '.csv';

	/**
	 * Retrieves and stores the CSV columns for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string Column data.
	 */
	public function csv_cols_out() {
		ob_start();

		parent::csv_cols_out();

		return ob_get_clean();
	}

	/**
	 * Retrieves and stores the CSV rows for the current step.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string Rows data.
	 */
	public function csv_rows_out() {
		ob_start();

		parent::csv_rows_out();

		return ob_get_clean();
	}

	/**
	 * Appends data to the export file.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @param string $data Optional. Data to append to the export file. Default empty.
	 */
	protected function stash_step_data( $data = '' ) {

		$file = $this->get_file();
		$file .= $data;
		@file_put_contents( $this->file, $file );

		// If we have no rows after this step, mark it as an empty export
		$file_rows    = file( $this->file, FILE_SKIP_EMPTY_LINES);
		$default_cols = $this->get_csv_cols();
		$default_cols = empty( $default_cols ) ? 0 : 1;

		$this->is_empty = count( $file_rows ) == $default_cols ? true : false;

	}

}
