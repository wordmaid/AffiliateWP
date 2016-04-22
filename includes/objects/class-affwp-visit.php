<?php

final class AffWP_Visit extends AffWP_Object {

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * Visit URL.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Visit referrer.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $referrer;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Visit IP address (IPv4).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $ip;

	/**
	 * Date the visit was created.
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
	public static $cache_token = 'affwp_visits';

	/**
	 * Visits cache group.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $cache_group = 'visits';

	/**
	 * Retrieves a visit based on a visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $visit_id Visit ID.
	 * @return AffWP_Visit|null Affiliate object or null if it doesn't exist,
	 */
	public static function get( $visit_id ) {
		return affiliate_wp()->visits->get( $visit_id );
	}

	/**
	 * Sanitizes a visit object field.
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
		if ( in_array( $field, array( 'visit_id', 'affiliate_id', 'referral_id' ) ) ) {
			$value = (int) $value;
		}
		return $value;
	}

}