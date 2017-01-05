<?php

class Affiliate_WP_Stripe extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'stripe';

		add_filter( 'sc_meta_values', array( $this, 'maybe_insert_referral' ) );
		add_filter( 'sc_redirect_args', array( $this, 'mark_referral_complete' ), 10, 2 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}


	/**
	 * Create a referral during stripe form submission if customer was referred
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function maybe_insert_referral( $meta ) {

		if( $this->was_referred() ) {

			$token       = sanitize_text_field( $_POST['stripeToken'] );
			$amount      = round( sanitize_text_field( $_POST['sc-amount'] ) / 100, 2 );
			$email       = sanitize_text_field( $_POST['stripeEmail'] );
			$description = sanitize_text_field( $_POST['sc-description'] );

			// This is used to look up the referral after completion
			$_POST['affwpStripeToken'] = $token;

			if( $this->is_affiliate_email( $email, $this->affiliate_id ) ) {

				if( $this->debug ) {
					$this->log( 'Referral not created because affiliate\'s own account was used.' );
				}

				return;

			}

			$referral_total = $this->calculate_referral_amount( $amount, $token );

			$referral_id = $this->insert_pending_referral( $referral_total, $token, $description );

			if( $referral_id && $this->debug ) {

				$this->log( 'Pending referral created successfully during maybe_insert_referral()' );

			} elseif ( $this->debug ) {

				$this->log( 'Pending referral failed to be created during maybe_insert_referral()' );

			}

		}

		return $meta;

	}

	/**
	 * Mark referral complete
	 *
	 * @function mark_referral_complete()
	 * @access public
	 * @return array
	 */
	public function mark_referral_complete( $query_args, $charge ) {

		if( $this->was_referred() && empty( $query_args['charge_failed'] ) && ! empty( $_POST['affwpStripeToken'] ) ) {

			$token = sanitize_text_field( $_POST['affwpStripeToken'] );

			$referral = affiliate_wp()->referrals->get_by( 'reference', $token, $this->context );
			$referral = affwp_get_referral( $referral );

			if( $referral ) {

				$referral->reference = $charge->id;
				$referral->custom    = $token;
				$referral->save();

			}

			$this->complete_referral( $charge->id );

		}

		return $query_args;

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'stripe' != $referral->context ) {

			return $reference;

		}

		$url = 'https://dashboard.stripe.com/payments/' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_Stripe;