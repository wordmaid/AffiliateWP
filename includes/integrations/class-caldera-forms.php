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

		add_action( 'caldera_forms_entry_saved', array( $this, 'entry_saved' ), 10, 3 );
		add_action( 'caldera_forms_general_settings_panel', array( $this, 'add_settings' ) );

		add_action( 'caldera_forms_pre_load_processors', array( $this, 'pre_load_processors' ), 10, 6 );
		add_action( 'caldera_forms_includes_complete', array( $this, 'load_processor' ), 10, 6 );

		add_action( 'cf_stripe_post_successful_charge', array( $this, 'complete_payment_stripe' ), 10, 4 );
		add_action( 'cf_braintree_success', array( $this, 'complete_payment_braintree' ), 10, 6 );

	}

	/**
	 * Load the processor
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function load_processor() {

		if ( class_exists( 'Caldera_Forms_Processor_Processor' ) ) {
			require_once ( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/class-caldera-forms-processor.php' );
		}

	}

	/**
	 * Register processor when in Caldera Forms admin, or when rendering form.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function pre_load_processors() {

	    $config = array(
	        'name'        => 'AffiliateWP',
	        'author'      => 'AffiliateWP',
	        'description' => 'Create a referral in AffiliateWP',
	        'author_url'  => 'https://affiliatewp.com',
			'icon'        => AFFILIATEWP_PLUGIN_URL . 'assets/images/logo-affwp.svg',
	        'template'    => AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/caldera-forms-config.php'
	    );

	    new AffiliateWP_Caldera_Forms_Processor( $config, $this->fields(), 'affwp' );

	}

	/**
	 * Fields shown for the affwp processor
	 *
	 * @access public
	 * @since  2.0
	 */
	public function fields() {

		return array(
			array(
				'id'       => 'total',
				'label'    => __( 'Total', 'affiliate-wp' ),
				'type'     => 'text',
				'required' => true,
				'magic'    => true,
			)
		);

	}

	/**
	 * Records a 0.00 referral when the entry is saved
	 *
	 * @access  public
	 * @since   2.0
	*/
	public function entry_saved( $entry_id, $new_entry, $form ) {

		/**
		 * Forms without the affwp processor will generate a $0.00 referral
		 * Forms with the affwp processor are handled via the AffiliateWP_Caldera_Forms_Processor class and will not go any further than this
		 */
		if ( $form['processors'] ) {
			foreach ( $form['processors'] as $processor ) {
				if ( $processor['type'] === 'affwp' ) {
					// return if form has affwp processor
					return false;
				}
			}
		}

		$args = array(
			'entry_id'               => $entry_id,
			'referral_total'         => 0.00,
			'mark_referral_complete' => true
		);

		// Add pending referral
		$this->add_pending_referral( $args, $form );

	}

	/**
	 * Records a pending referral
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function add_pending_referral( $args = array(), $form ) {

		$affiliate_id = $this->affiliate_id;
		$entry_id     = $args['entry_id'];

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

		// Referral total
		$referral_total = $args['referral_total'];

		// Use form title as description
		$description = $form['name'];

		// Insert a pending referral
		$referral_id = $this->insert_pending_referral( $referral_total, $entry_id, $description );

		// Mark referral complete (set to "unpaid" status)
		if ( isset( $args['mark_referral_complete'] ) ) {
			$this->mark_referral_complete( $entry_id );
		}

	}

	/**
	 * Mark referral as "unpaid" when the payment is successful in Stripe
	 *
	 * @access public
	 * @since  2.0
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
	 * @since  2.0
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
	 * Get a field's value
	 *
	 * @access public
	 * @since  2.0
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
