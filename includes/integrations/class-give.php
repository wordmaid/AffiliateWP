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

	}

}
new Affiliate_WP_Give;
