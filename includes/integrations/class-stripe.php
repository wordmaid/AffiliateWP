<?php

class Affiliate_WP_stripe extends Affiliate_WP_Base {

	/**
	 * Get thigns started
	 *
	 * @access  public
	 * @since   1.9
	 */
	public function init() {

		$this->context = 'stripe'; 

		add_action( 'wp_footer', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_affwp_maybe_insert_stripe_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'wp_ajax_nopriv_affwp_maybe_insert_stripe_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'init', array( $this, 'process_webhook' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Add JS to site footer for detecting Stripe form submissions
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function scripts() {
?>
		<script type="text/javascript">
		var affwp_scripts;
		jQuery(document).ready(function($) {

			$('form .stripe-button-el').on('click', function(e) {

				e.preventDefault();

				var $form = $(this);

				$.ajax({
					type: "POST",
					data: {
						action: 'affwp_maybe_insert_stripe_referral'
					},
					url: affwp_scripts.ajaxurl,
					success: function (response) {

						$form.append( '<input type="hidden" name="custom" value="' + response.data.ref + '"/>' );

						$form.get(0).submit();

					}

				}).fail(function (response) {

					if ( window.console && window.console.log ) {
						console.log( response );
					}

				});

			});
		});
		</script>
<?php
	}

	/**
	 * Create a referral during stripe form submission if customer was referred
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function maybe_insert_referral() {

		$response = array();

		if( $this->was_referred() ) {

			$reference   = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . time();
			$referral_id = $this->insert_pending_referral( 0.01, $reference, __( 'Pending Stripe referral', 'affiliate-wp' ) );

			if( $referral_id && $this->debug ) {

				$this->log( 'Pending referral created successfully during maybe_insert_referral()' );

			} elseif ( $this->debug ) {

				$this->log( 'Pending referral failed to be created during maybe_insert_referral()' );

			}

			$response['ref'] = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . $referral_id;

		}

		wp_send_json_success( $response );

	}

	/**
	 * Process stripe IPN requests in order to mark referrals as Unpaid
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function process_webhook() {

		if( empty( $_GET['affwp-listener'] ) || 'stripe' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'stripe' != $referral->context ) {

			return $reference;

		}

		$url = 'https://dashboard.stripe.com/payments/' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_stripe;