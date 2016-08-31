<?php
/**
 * Retrieves a REST consumer object.
 *
 * @since 1.9
 *
 * @param int|AffWP\REST\Consumer $consumer Consumer ID or object.
 * @return \AffWP\Affiliate|false Consumer object, otherwise false.
 */
function affwp_get_rest_consumer( $consumer = null ) {

	if ( is_object( $consumer ) && isset( $consumer->consumer_id ) ) {
		$consumer_id = $consumer->consumer_id;
	} elseif ( is_numeric( $consumer ) ) {
		$consumer_id = absint( $consumer );
	} elseif ( is_string( $consumer ) ) {
		if ( $user = get_user_by( 'login', $consumer ) ) {
			if ( $consumer = affiliate_wp()->REST->consumers->get_by( 'user_id', $user->ID ) ) {
				$consumer_id = $consumer->consumer_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}

	return affiliate_wp()->REST->consumers->get_object( $consumer_id );
}

/**
 * Generates a random hash.
 *
 * Note: This is primary used in the REST component and should not be used by itself.
 * It's used to re-hash already-hashed tokens used for REST authentication.
 *
 *
 * @since 1.9
 *
 * @return string Random hash. If openssl_random_pseudo_bytes() is available, bin2hex() is used,
 *                otherwise sha1().
 */
function affwp_rand_hash() {
	if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return bin2hex( openssl_random_pseudo_bytes( 20 ) );
	} else {
		return sha1( wp_rand() );
	}
}

/**
 * Generates a random hash for use with generating REST authentication tokens.
 *
 * @since 1.9
 *
 * @param string $data         Input data.
 * @param string $key          Key.
 * @param bool   $add_auth_key Optional. Whether to append the AUTH_KEY to `$data`.
 *                             Default true.
 * @return false|string Hashed string or false.
 */
function affwp_auth_hash( $data, $key, $add_auth_key = true ) {
	if ( true === $add_auth_key ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';

		$data = $data . $auth_key;
	}
	return hash_hmac( 'md5', $data, $key );
}
