<?php

class Affiliate_WP_Caldera_Forms extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function init() {

		$this->context = 'caldera-forms';

		add_action( 'caldera_forms_entry_saved', array( $this, 'add_pending_referral' ), 10, 3 );
		add_action( 'caldera_forms_general_settings_panel', array( $this, 'add_settings' ) );

		add_action( 'cf_stripe_post_successful_charge', array( $this, 'complete_payment_stripe' ), 10, 4 );
		add_action( 'cf_braintree_success', array( $this, 'complete_payment_braintree' ), 10, 6 );

	}

	/**
	 * Records a pending referral
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function add_pending_referral( $entry_id, $new_entry, $form ) {

		$affiliate_id = $this->affiliate_id;

		// Return if the customer was not referred or the affiliate ID is empty
		if ( ! $this->was_referred() && empty( $affiliate_id ) ) {
			return;
		}

		// Prevent referral creation unless referrals enabled for the form
		if ( empty( $form['affwp_allow_referrals'] ) ) {
			return;
		}

		// get customer email
		$customer_email = $this->get_field_value( 'email', $form );

		// Customers cannot refer themselves
		if ( $this->is_affiliate_email( $customer_email, $affiliate_id ) ) {

			if ( $this->debug ) {
				$this->log( 'Referral not created because affiliate\'s own account was used.' );
			}

			return false;
		}

		// get referral total
		$total          = $this->get_field_value( 'calculation', $form );
		$referral_total = $this->calculate_referral_amount( $total, $entry_id );

		// use form title as description
		$description = $form['name'];

		// insert a pending referral
		$referral_id = $this->insert_pending_referral( $referral_total, $entry_id, $description );

		// Mark referral complete (unpaid) if no total
		if ( empty( $referral_total ) ) {
			$this->mark_referral_complete( $entry_id );
		}

	}

	/**
	 * Mark referral as "unpaid" when the payment is successful in Stripe
	 *
	 * @access public
	 * @since 2.0
	 */
	public function complete_payment_stripe( $return_charge, $transdata, $config, $form ) {

		$submission_data = Caldera_Forms::get_instance()->get_submission_data( $form );
		$entry_id        = $submission_data['_entry_id'];

		$this->mark_referral_complete( $entry_id );
	}

	/**
	 * Mark referral as "unpaid" when the payment is successful in Braintree
	 *
	 * @access public
	 * @since 2.0
	 */
	public function complete_payment_braintree( $result, $order_id, $transaction, $config, $form, $proccesid ) {

		$submission_data = Caldera_Forms::get_instance()->get_submission_data( $form );
		$entry_id        = $submission_data['_entry_id'];

		$this->mark_referral_complete( $entry_id );
	}

	/**
	 * Sets a referral to unpaid when payment is completed
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function mark_referral_complete( $entry_id = 0 ) {
		$this->complete_referral( $entry_id );
	}

	/**
	 * Get calculation field total
	 *
	 * @since 2.0
	 */
	public function get_field_value( $type = '', $form ) {

		$fields          = $form['fields'];
		$submission_data = Caldera_Forms::get_instance()->get_submission_data( $form );

		foreach ( $fields as $field ) {
			if ( $field['type'] === $type ) {
				$field_id = $field['ID'];
			}
		}

		if ( isset( $field_id ) ) {
			return $submission_data[$field_id];
		}

		return false;

	}

	/**
	 * Register the form-specific settings
	 *
	 * @since  2.0
	 * @return void
	 */
	public function add_settings( $element ) {
		?>

		<div class="caldera-config-group">
			<fieldset>
				<legend>
					<?php esc_html_e( 'Allow Referrals', 'affiliate-wp' ); ?>
				</legend>
				<div class="caldera-config-field">
					<label for="affwp-allow-referrals">
						<input id="affwp-allow-referrals" type="checkbox" class="field-config" name="config[affwp_allow_referrals]" value="1" <?php if ( ! empty( $element[ 'affwp_allow_referrals' ] ) ){ ?>checked="checked"<?php } ?>>
						<?php esc_html_e( 'Enable affiliate referral creation for this form', 'affiliate-wp' ); ?>
					</label>
				</div>
			</fieldset>
		</div>

		<?php
	}

}
new Affiliate_WP_Caldera_Forms;
