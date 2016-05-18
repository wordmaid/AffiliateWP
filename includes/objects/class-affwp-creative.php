<?php

final class AffWP_Creative extends AffWP_Object {

	/**
	 * Creative ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $creative_id = 0;

	/**
	 * Name of the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Description for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Text for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $text;

	/**
	 * Image URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $image;

	/**
	 * Status for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Creation date for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $cache_token = 'affwp_creatives';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'creative';

	/**
	 * Sanitizes a creative object field.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( 'creative_id' === $field ) {
			$value = (int) $value;
		}
		return $value;
	}

}