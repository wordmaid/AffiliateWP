<?php

final class AffWP_Affiliate extends AffWP_Object {

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Affiliate user ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Affiliate rate.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rate;

	/**
	 * Affiliate rate type.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rate_type;

	/**
	 * Affiliate payment email.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $payment_email;

	/**
	 * Affiliate status.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Affiliate earnings.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $earnings;

	/**
	 * Affiliate referrals
	 *
	 * @since 1.8\9
	 * @access public
	 * @var int
	 */
	public $referrals;

	/**
	 * Affiliate referral visits.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visits;

	/**
	 * Affiliate registration date.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date_registered;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $cache_token = 'affwp_affiliates';

	/**
	 * Affiliates cache group.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $cache_group = 'affiliates';

	/**
	 * Retrieves an affiliate based on an affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $affiliate_id Affiliate ID.
	 * @return AffWP_Affiliate|null Affiliate object or null if it doesn't exist,
	 */
	public static function get( $affiliate_id ) {
		return affiliate_wp()->affiliates->get( $affiliate_id );
	}

	/**
	 * Sanitizes an affiliate object field.
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
		if ( in_array( $field, array( 'affiliate_id', 'user_id', 'referrals', 'visits' ) ) ) {
			$value = (int) $value;
		}

		return $value;
	}
}
