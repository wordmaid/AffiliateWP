<?php
namespace AffWP\REST\v1;

/**
 * Base REST controller.
 *
 * @since 1.9
 * @abstract
 */
abstract class Controller {

	/**
	 * AffWP REST namespace.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $namespace = 'affwp/v1';

	/**
	 * The base of this controller's route.
	 *
	 * Should be defined and used by subclasses.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * Looks for a register_routes() method in the sub-class and hooks it up to 'rest_api_init'.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 15 );
	}

	/**
	 * Converts an object or array of objects into a \WP_REST_Response object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param object|array $response Object or array of objects.
	 * @return \WP_REST_Response REST response.
	 */
	public function response( $response ) {
		if ( is_array( $response ) ) {
			$response = array_map( function( $object ) {
				$object->id = $object->ID;

				return $object;
			}, $response );
		}
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the query parameters for collections.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param(),
			'number'  => array(
				'description'       => __( 'The number of items to query for. Use -1 for all.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'offset'  => array(
				'description'       => __( 'The number of items to offset in the query.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'order'   => array(
				'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
				'validate_callback' => function( $param, $request, $key ) {
					return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
				},
			),
			'fields'  => array(
				'description'       => __( "Fields to limit the selection for. Accepts 'ids'. Default '*' for all.", 'affiliate-wp' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $param, $request, $key ) {
					return is_string( $param );
				},
			),
		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * Ensures consistent description between endpoints, and populates enum from schema.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \WP_REST_Controller::get_context_param()
	 *
	 * @param array $args {
	 *     Optional. Parameter details. Default empty array.
	 *
	 *     @type string   $description       Parameter description.
	 *     @type string   $type              Parameter type. Accepts 'string', 'integer', 'array',
	 *                                       'object', etc. Default 'string'.
	 *     @type callable $sanitize_callback Parameter sanitization callback. Default 'sanitize_key'.
	 *     @type callable $validate_callback Parameter validation callback. Default empty.
	 * }
	 * @return array Context parameter details.
	 */
	public function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.', 'affiliate-wp' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => '',
		);

		return array_merge( $param_details, $args );
	}
}
