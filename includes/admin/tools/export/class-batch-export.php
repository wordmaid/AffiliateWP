<?php
namespace AffWP\Utils\Batch_Process;

/**
 * Implements the base batch exporter as an intermediary between a batch process
 * and the base exporter class.
 *
 * @since 2.0
 *
 * @see \Affiliate_WP_Export
 */
class Export extends \Affiliate_WP_Export {

	/**
	 * The file the export data will be stored in.
	 *
	 * @access private
	 * @since  2.0
	 * @var    resource
	 */
	private $file;

	/**
	 * The name of the file the export data will be stored in.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filename;

	/**
	 * The export file type, e.g. '.csv'.
	 *
	 * @access public
	 * @since  2.0
	 * @var    string
	 */
	public $filetype;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  2.0
	 * @var    int|string Step number or 'done'.
	 */
	public $step;

	/**
	 * Whether the the export file is writable.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_writable = true;

	/**
	 * Whether the export file is empty.
	 *
	 * @access public
	 * @since  2.0
	 * @var    bool
	 */
	public $is_empty = false;

	/**
	 * Sets up the batch export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|string $step Step number or 'done'.
	 */
	public function __construct( $step = 1 ) {

		$upload_dir     = wp_upload_dir();
		$this->filename = 'affwp-' . $this->export_type . $this->filetype;
		$this->file     = trailingslashit( $upload_dir['basedir'] ) . $this->filename;

		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			$this->is_writable = false;
		}

		$this->step = $step;
		$this->done = false;
	}

	/**
	 * Sets the export headers.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! affwp_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) )
			set_time_limit( 0 );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=affiliate-wp-export-' . $this->export_type . '-' . date( 'm-d-Y' ) . $this->filetype );
		header( "Expires: 0" );
	}

	/**
	 * Retrieves the file that data will be written to.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return string File data.
	 */
	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
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
		$file  = $this->get_file();
		$file .= $data;

		@file_put_contents( $this->file, $file );
	}

}
