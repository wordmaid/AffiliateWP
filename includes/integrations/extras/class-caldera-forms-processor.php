<?php

class AffiliateWP_Caldera_Forms_Processor extends Caldera_Forms_Processor_Processor {

	/**
	 * Pre Processor
	 *
	 * @access public
	 * @since 2.0
	 */
	public function pre_processor( array $config, array $form, $process_id ) {
		// This method must exist to avoid an error
		// Using it would be bad as payments will not have been attempted yet.
	}

	/**
	 * Processor
	 * Payments will have been processed by now, if they generated an error, this method will not run.
	 *
	 * @access public
	 * @since 2.0
	 */
	public function processor( array $config, array $form, $process_id ) {

		$integration  = new Affiliate_WP_Caldera_Forms;

		// Get entry ID
		$submission_data = Caldera_Forms::get_submission_data( $form );
		$entry_id        = $submission_data['_entry_id'];

		// Get values of all settings for this processor
		$this->set_data_object_initial( $config, $form );

		// Get total conversion value set in processor whether it is hardcoded or field value
		$total = $this->data_object->get_value( 'total' );

		// Get referral total
		$referral_total = $integration->calculate_referral_amount( $total, $entry_id );

		$args = array(
			'entry_id'               => $entry_id,
			'referral_total'         => $referral_total,
			'mark_referral_complete' => false
		);

		// Add pending referral
		$integration->add_pending_referral( $args, $form );

    }

}
