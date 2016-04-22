<?php

abstract class AffWP_Object {

	/**
	 * Whether the object members have been filled.
	 *
	 * @since 1.9
	 * @access protected
	 * @var bool|null
	 */
	protected $filled = null;

	/**
	 * Retrieves the object instance.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $object Object ID.
	 * @return object|false Object instance or false.
	 */
	public static function get_instance( $object_id ) {
		if ( ! (int) $object_id ) {
			return false;
		}

		$subClass = get_called_class();

		$cache_key = md5( $subClass::$cache_token . '_' . $object_id );

		$_object = wp_cache_get( $cache_key, $subClass::$cache_group );

		if ( false === $_object ) {
			$_object = static::get( $object_id );

			if ( ! $_object ) {
				return false;
			}

			$_object = self::fill_vars( $_object );

			wp_cache_add( $cache_key, $subClass::$cache_group );
		} elseif ( empty( $_object->filled ) ) {
			$_object = self::fill_vars( $_object );
		}
		return new $subClass( $_object );
	}

	/**
	 * Retrieves an object based on ID.
	 *
	 * Sub-classes must override this method.
	 *
	 * @since 1.9
	 * @access public
	 * @abstract
	 * @static
	 *
	 * @param int $object_id Object ID.
	 * @return object|null Object corresponding to the given ID. Null if it doesn't exist,
	 */
	abstract public static function get( $object_id );

	/**
	 * Object constructor.
	 *
	 * @since 1.9
	 * @access public
	 * @param mixed $object Object to populate members for.
	 */
	public function __construct( $object ) {
		foreach ( get_object_vars( $object ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Fills object members.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param object|array $object Object or array of object data.
	 * @return object|array Object or data array with filled members.
	 */
	public static function fill_vars( $object ) {
		if ( is_object( $object ) ) {
			if ( isset( $object->filled ) ) {
				return $object;
			}

			foreach ( array_keys( get_object_vars( $object ) ) as $field ) {
				$object->$field = static::sanitize_field( $field, $object->$field );
				$object->filled = true;
			}
		} elseif ( is_array( $object ) ) {
			if ( isset( $object['filled'] ) ) {
				return $object;
			}

			foreach ( array_keys( $object ) as $field ) {
				$object[ $field ] = static::sanitize_field( $field, $object[ $field ] );
				$object['filled'] = true;
			}
		}
		return $object;
	}

	/**
	 * Sanitizes a given object field's value.
	 *
	 * Sub-class must override this method.
	 *
	 * @since 1.9
	 * @access public
	 * @abstract
	 * @static
	 *
	 * @param string $field Object field.
	 * @param mixed  $value Object field value.
	 * @return mixed Sanitized value for the given field.
	 */
	abstract public static function sanitize_field( $field, $value );

}
