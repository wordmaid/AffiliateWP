<?php
namespace AffWP\Utils;

/**
 * Initializes a temporary data storage engine used by core in various capacities.
 *
 * @since 2.0
 * @access private
 */
class Data_Storage {

	/**
	 * Retrieves stored data by key.
	 *
	 * Given a key, get the information from the database directly.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string     $key     The stored option key.
	 * @param null|mixed $default Optional. A default value to retrieve should `$value` be empty.
	 *                            Default null.
	 * @return mixed|false The stored data, value of `$default` if not null, otherwise false.
	 */
	public function get( $key, $default = null ) {
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = '%s'", $key ) );

		if ( empty( $value ) && ! is_null( $default ) ) {
			$value = $default;
		}

		return empty( $value ) ? false : maybe_unserialize( $value );
	}

	/**
	 * Write some data based on key and value.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $key     The option_name.
	 * @param mixed  $value   The value to store.
	 * @param array  $formats Optional. Array of formats to pass for key, value, and autoload.
	 *                        Default empty (all strings).
	 */
	public function write( $key, $value, $formats = array() ) {
		global $wpdb;

		$value = maybe_serialize( $value );

		$data = array(
			'option_name'  => $key,
			'option_value' => $value,
			'autoload'     => 'no',
		);

		if ( empty( $formats ) ) {
			$formats = array(
				'%s', '%s', '%s',
			);
		}

		$wpdb->replace( $wpdb->options, $data, $formats );
	}

	/**
	 * Deletes a piece of stored data by key.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $key The stored option name to delete.
	 */
	public function delete( $key ) {
		global $wpdb;

		$wpdb->delete( $wpdb->options, array( 'option_name' => $key ) );
	}

}
