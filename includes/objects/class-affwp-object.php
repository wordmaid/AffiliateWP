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

		$subClass    = get_called_class();
		$object_type = $subClass::$object_type;
		$cache_key   = self::get_cache_key( $object_id );

		$_object = wp_cache_get( $cache_key, $object_type );

		if ( false === $_object ) {
			$_object = affiliate_wp()->$object_type->get( $object_id );

			if ( ! $_object ) {
				return false;
			}

			$_object = self::fill_vars( $_object );

			wp_cache_add( $cache_key, $object_type );
		} elseif ( empty( $_object->filled ) ) {
			$_object = self::fill_vars( $_object );
		}
		return new $subClass( $_object );
	}

	/**
	 * Retrieves the built cache key for the given single object.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $object_id Object ID.
	 * @return string Cache key for the object type and ID.
	 */
	public static function get_cache_key( $object_id ) {
		$subClass = get_called_class();

		return md5( $subClass::$cache_token . '_' . $object_id );
	}

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
	 * Retrieves the value of a given property.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param string $key Property to retrieve a value for.
	 * @return mixed If `$key` is 'ID', uses the primary_key to get the row ID. Otherwise, the value of the key.
	 */
	public function __get( $key ) {
		if ( 'ID' === $key ) {
			return $this->{$this->primary_key};
		} elseif ( isset( $this->$key ) ) {
			return $this->$key;
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
