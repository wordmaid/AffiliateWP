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

		add_action( 'caldera_forms_submit_complete', array( $this, 'add_pending_referral' ), 10, 3 );
		add_action( 'caldera_forms_general_settings_panel', array( $this, 'add_settings' ) );
	}

	/**
	 * Records a pending referral
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function add_pending_referral( $form, $referrer, $process_id ) {

		$affiliate_id = $this->affiliate_id;

		// Return if the customer was not referred or the affiliate ID is empty
		if ( ! $this->was_referred() && empty( $affiliate_id ) ) {
			return;
		}

	}

	/**
	 * Register the form-specific settings
	 *
	 * @since  2.0
	 * @return void
	 */
	public function add_settings( $element ) {

	}


}
new Affiliate_WP_Caldera_Forms;
