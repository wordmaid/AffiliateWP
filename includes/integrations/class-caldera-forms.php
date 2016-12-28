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
		if ( ! $form['affwp_allow_referrals'] ) {
			return;
		}

		// get referral total
		$total          = $this->get_total( $form );
		$referral_total = $this->calculate_referral_amount( $total, $entry_id );

	}

	/**
	 * Get calculation field total
	 *
	 * @since 2.0
	 */
	public function get_total( $form ) {

		$fields = $form['fields'];

		foreach ( $fields as $field ) {
			if ( $field['type'] === 'calculation' ) {
				$field_id = $field['ID'];
				break;
			}
		}

		if ( isset( $field_id ) ) {
			return (int) $_POST[$field_id];
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
