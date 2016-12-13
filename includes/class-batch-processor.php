<?php
namespace AffWP\Util;

/**
 * Batch processing utility class.
 *
 * @since 2.0
 */
class Batch_Processor {

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
	 * Registers a new batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param array $process_args {
	 *     Arguments for registering a new batch process.
	 *
	 * @type string $batch_id Unique batch process ID.
	 * @type string $class    Batch processor class to use.
	 * @type string $step_method Optional. Step method to use via `$class`.
	 *
	 * }
	 *
	 * @return \WP_Error
	 */
	public function register_process( $process_args ) {
		$process_args = wp_parse_args( $process_args,  array_fill_keys( array(
			'batch_id', 'class', 'step_method'
		), '' ) );

		if ( empty( $process_args['batch_id'] ) ) {
			return new \WP_Error( 'invalid_batch_id', __( 'A batch ID must be specified.', 'affiliate-wp' ) );
		}

		if ( empty( $process_args['class'] ) ) {
			return new \WP_Error( 'invalid_batch_class', __( 'A batch process class must be specified', 'affiliate-wp' ) );
		}

		if ( ! method_exists( $process_args['class'], $process_args['step_method'] )
			|| empty( $process_args['step_method'] )
		) {
			$process_args['step_method'] = 'step_forward';
		}

		$this->add_process( $process_args['batch_id'], $process_args['class'], $process_args['step_method'] );
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
	 */
	private function add_process( $batch_id, $class, $step_method ) {
		$this->batch_ids[ $batch_id ] = array(
			'class'       => $class,
			'step_method' => $step_method
		);
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

}
