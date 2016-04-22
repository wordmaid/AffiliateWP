<?php

final class AFFWP_Affiliate {

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
	 * @var bool|null
	 */
	public $filled = null;

	/**
	 * Retrieves the AFFWP_Affiliate instance.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int Affiliate ID.
	 *
	 * @return AFFWP_Affiliate|false AFFWP_Affiliate instance or false.
	 */
	public static function get_instance( $affiliate_id ) {
		if ( ! (int) $affiliate_id ) {
			return false;
		}

		$cache_key = md5( 'affwp_affiliates' . $affiliate_id );
		$_affiliate = wp_cache_get( $cache_key, 'affiliates' );

		if ( false === $_affiliate ) {
			$_affiliate = affiliate_wp()->affiliates->get( $affiliate_id );

			if ( ! $_affiliate ) {
				return false;
			}

			$_affiliate = self::fill_vars( $_affiliate );

			wp_cache_add( $cache_key, 'affiliates' );
		} elseif ( empty( $_affiliate->filled ) ) {
			$_affiliate = self::fill_vars( $_affiliate );
		}
		return new AFFWP_Affiliate( $_affiliate );
	}

	/**
	 * AFFWP_Affiliate constructor.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param AFFWP_Affiliate $affiliate Affiliate object.
	 */
	public function __construct( $affiliate ) {
		foreach ( get_object_vars( $affiliate ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Fill AFFWP_Affiliate members.
	 *
	 * @since 1.9
	 * @access private
	 * @static
	 *
	 * @param AFFWP_Affiliate|array $affiliate Affiliate object or array of affiliate data.
	 * @return AFFWP_Affiliate|array Affiliate object or data array with filled members.
	 */
	private static function fill_vars( $affiliate ) {
		if ( ( $affiliate instanceof AFFWP_Affiliate ) || is_object( $affiliate ) ) {
			if ( isset( $affiliate->filled ) ) {
				return $affiliate;
			}

			foreach ( array_keys( get_object_vars( $affiliate ) ) as $field ) {
				$affiliate->$field = self::sanitize_field( $field, $affiliate->$field );
				$affiliate->filled = true;
			}
		} elseif ( is_array( $affiliate ) ) {
			if ( isset( $affiliate['filled'] ) ) {
				return $affiliate;
			}

			foreach ( array_keys( $affiliate ) as $field ) {
				$affiliate[ $field ] = self::sanitize_field( $field, $affiliate[ $field ] );
				$affiliate['filled'] = true;
			}
		}
		return $affiliate;
	}

	/**
	 * Sanitizes an affiliate object field.
	 *
	 * @since 1.9
	 * @access private
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	private static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'affiliate_id', 'user_id', 'referrals', 'visits' ) ) ) {
			$value = (int) $value;
		}

		return $value;
	}
}
