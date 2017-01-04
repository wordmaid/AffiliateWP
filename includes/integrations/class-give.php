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

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
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

			if ( $this->debug ) {
				$this->log( 'Referral not created because affiliate\'s own account was used.' );
			}

			return false;
		}

		// Get referral total
		$referral_total = $this->get_referral_total( $payment_id, $affiliate_id );

		// Get referral description
		$desc = $this->get_referral_description( $payment_id );

		if ( empty( $desc ) ) {

			if ( $this->debug ) {
				$this->log( 'Referral not created due to empty description.' );
			}

			return;
		}

		// Insert a pending referral
		$referral_id = $this->insert_pending_referral( $referral_total, $payment_id, $desc );

	}


	/**
	 * Get the referral total
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function get_referral_total( $payment_id = 0, $affiliate_id = 0 ) {

		$payment_amount = give_get_payment_amount( $payment_id );
		$referral_total = $this->calculate_referral_amount( $payment_amount, $payment_id, '', $affiliate_id );

		return $referral_total;

	}

	/**
	 * Get the referral description
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function get_referral_description( $payment_id = 0 ) {

		$payment_meta = give_get_payment_meta( $payment_id );

		$form_id    = isset( $payment_meta['form_id'] ) ? $payment_meta['form_id'] : 0;
		$price_id   = isset( $payment_meta['price_id'] ) ? $payment_meta['price_id'] : null;

		$referral_description = isset( $payment_meta['form_title'] ) ? $payment_meta['form_title'] : '';

		$separator  = '';

		// If multi-level, append to the form title.
		if ( give_has_variable_prices( $form_id ) ) {

			//Only add separator if there is a form title.
			if ( ! empty( $referral_description ) ) {
				$referral_description .= ' ' . $separator . ' ';
			}

			if ( $price_id == 'custom' ) {

				$custom_amount_text = get_post_meta( $form_id, '_give_custom_amount_text', true );
				$referral_description .= ! empty( $custom_amount_text ) ? $custom_amount_text : __( 'Custom Amount', 'affiliate-wp' );

			} else {

				$referral_description .= give_get_price_option_name( $form_id, $price_id );

			}

		}

		return $referral_description;

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'give' != $referral->context ) {
			return $reference;
		}

		$url = admin_url( 'edit.php?post_type=give_forms&page=give-payment-history&view=view-order-details&id=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

}
new Affiliate_WP_Give;
