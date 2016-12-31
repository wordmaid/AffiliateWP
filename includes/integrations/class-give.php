<?php

class Affiliate_WP_Give extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function init() {

		$this->context = 'give';

		add_action( 'give_insert_payment', array( $this, 'add_pending_referral' ), 99999, 2 );

	}

	/**
	 * Records a pending referral when a pending payment is created
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function add_pending_referral( $payment_id = 0, $payment_data = array() ) {

		if ( ! $this->was_referred() ) {
			return false;
		}

		// get affiliate ID
		$affiliate_id = $this->get_affiliate_id( $payment_id );

		// get customer email
		$customer_email = give_get_payment_user_email( $payment_id );

		// Customers cannot refer themselves
		if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {

			if e( $this->debug ) {
				$this->log( 'Referral not created because affiliate\'s own account was used.' );
			}

			return false;
		}

	}

}
new Affiliate_WP_Give;
