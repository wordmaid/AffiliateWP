<?php
namespace AffWP\Admin;

// Load WP_List_Table if not loaded
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List Table base class for use in the admin.
 *
 * @since 1.9
 *
 * @see \WP_List_Table
 */
abstract class List_Table extends \WP_List_Table {

	/**
	 * Optional arguments to pass when preparing items.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $query_args = array();

	/**
	 * Optional arguments to pass when preparing items for display.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $display_args = array();

	/**
	 * Current screen object.
	 *
	 * @access public
	 * @since  1.9
	 * @var    \WP_Screen
	 */
	public $screen;

	/**
	 * Sets up the list table instance.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args {
	 *     Optional. Arbitrary display and query arguments to pass through to the list table.
	 *     Default empty array.
	 *
	 *     @type array $query_args   Arguments to pass through to the query used for preparing items.
	 *     @type array $display_args Arguments to pass through for use when displaying queried items.
	 *     @type string $singular    Singular version of the list table item.
	 *     @type string $plural      Plural version of the list table item.
	 * }
	 */
	public function __construct( $args = array() ) {
		$this->screen = get_current_screen();

		$display_args = array(
			'pre_table_callback' => '',
			'hide_table_nav'     => false,
			'columns_to_hide'    => array(),
		);

		if ( ! empty( $args['query_args'] ) ) {
			$this->query_args = $args['query_args'];

			unset( $args['query_args'] );
		}

		if ( ! empty( $args['display_args'] ) ) {
			$this->display_args = wp_parse_args( $args['display_args'], $display_args );

			unset( $args['display_args'] );
		}

		$args = wp_parse_args( $args, array(
			'ajax' => false,
		) );

		parent::__construct( $args );
	}

	/**
	 * Builds and retrieves the HTML markup for a row action link.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param string $label      Row action link label.
	 * @param array  $query_args Query arguments.
	 * @param array  $args {
	 *     Optional. Additional arguments for building a row action link.
	 *
	 *     @type false|string $nonce Whether to nonce the URL. Accepts false (disabled) or a nonce name
	 *                               to use. Default false.
	 *     @type string       $class Class attribute value for the link.
	 *
	 * }
	 * @return string Row action link markup.
	 */
	public function get_row_action_link( $label, $query_args, $args = array() ) {

		if ( empty( $args['nonce'] ) ) {
			$url = esc_url( add_query_arg( $query_args ) );
		} else {
			$url = wp_nonce_url( add_query_arg( $query_args ), $args['nonce'] );
		}

		$class = empty( $args['class'] ) ? '' : sprintf( ' class="%s"', esc_attr( $args['class'] ) );

		return sprintf( '<a href="%1$s"%2$s>%3$s</a>', $url, $class, esc_html( $label ) );
	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param string $which Which location the builk actions are being rendered for.
	 *                      Will be 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}

		if ( ! empty( $this->display_args['pre_table_callback'] )
			&& is_callable( $this->display_args['pre_table_callback'] )
			&& 'top' === $which
		) {

			echo call_user_func( $this->display_args['pre_table_callback'] );
		}

		if ( true !== $this->display_args['hide_table_nav'] ) : ?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>">

				<?php if ( $this->has_items() ): ?>
					<div class="alignleft actions bulkactions">
						<?php $this->bulk_actions( $which ); ?>
					</div>
				<?php endif;
				$this->extra_tablenav( $which );
				$this->pagination( $which );
				?>

				<br class="clear" />
			</div>
		<?php endif;
	}

	/**
	 * Prepares columns for display.
	 *
	 * Applies display arguments passed in the constructor to the list of columns.
	 *
	 * @access protected
	 * @since  1.9
	 *
	 * @param array $columns List of columns.
	 * @return array (Maybe) filtered list of columns.
	 */
	public function prepare_columns( $columns ) {
		if ( ! empty( $this->display_args['columns_to_hide'] ) ) {
			$columns_to_hide = $this->display_args['columns_to_hide'];

			foreach ( $columns_to_hide as $column ) {
				if ( array_key_exists( $column, $columns ) ) {
					unset( $columns[ $column ] );
				}
			}
		}
		return $columns;
	}

}
