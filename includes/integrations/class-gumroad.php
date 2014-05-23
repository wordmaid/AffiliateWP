<?php

class Affiliate_WP_Gumroad extends Affiliate_WP_Base {

	public function init() {

		$this->context = 'gumroad';
		add_action( 'init', array( $this, 'record_referral' ) );
	}

	public function record_referral() {

		if( empty( $_GET['affwp_listener'] ) ) {
			return;
		}

		if( 'gumroad' != $_GET['affwp_listener'] ) {
			return;
		}

		if( $this->was_referred() ) {

			$amount   = sanitize_text_field( $_POST['price'] ) / 100; // Amount is sent in cents
			$email    = sanitize_text_field( $_POST['email'] );
			$order_id = sanitize_text_field( $_POST['order_number'] );

			if( $this->get_affiliate_email() == $email && empty( $_POST['test'] ) ) {
				return; // Customers cannot refer themselves
			}

			$description = '';
			$this->insert_pending_referral( $amount, $order_id, sanitize_text_field( $_POST['product_permalink'] ) );
			$this->complete_referral( $order_id );
		}

	}

}
new Affiliate_WP_Gumroad;