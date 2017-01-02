<?php

/**
 * Contact Form 7 integration class.
 *
 * This integration provides support for four total plugins:
 *
 * - `contact-form-7`
 * - `contact-form-7-paypal-add-on`
 * - `contact-form-7-paypal-extension`
 *
 * - Plugins `contact-form-7` are required for use of either paypal add-on.
 * - Plugins `contact-form-7` and one of the above-noted PayPal add-ons are required.
 *
 * For brevity, within this class:
 *
 * - `contact-form-7-paypal-add-on` plugin is referenced as `paypal1`.
 * - `contact-form-7-paypal-extension` plugin is referenced as `paypal2`.
 *
 * @since 2.0
 */
class Affiliate_WP_Contact_Form_7 extends Affiliate_WP_Base {

	/**
	 * The supported CF7 payment gateway integrations.
	 *
	 * @access  public
	 * @see     Affiliate_WP_Contact_Form_7::get_supported_gateways
	 * @since   2.0
	 * @var     array
	 */
	public $supported_gateways;

	/**
	 * The PayPal transaction success page url
	 * Specific to the paypal1 integration.
	 *
	 * @access public
	 * @since 2.0
	 */
	public $return_url;

	/**
	 * The PayPal transaction cancellation page url
	 * Specific to the paypal1 integration.
	 *
	 * @access public
	 * @since 2.0
	 */
	public $cancel_url;

	/**
	 * @access  public
	 * @see     Affiliate_WP_Base::init
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'contactform7';

		$this->supported_gateways = $this->get_supported_gateways();

		// Set the success and cancel url
		$paypal1_options = get_option( 'cf7pp_options' );
		$this->return_url = $paypal1_options['return'];
		$this->cancel_url = $paypal1_options['cancel'];


		// Misc AffWP CF7 functions
		$this->include_cf7_functions();

		// Register core settings
		add_filter( 'affwp_settings_tabs', array( $this, 'register_settings_tab' ) );
		add_filter( 'affwp_settings',      array( $this, 'register_settings'     ) );

		/**
		 * Per-form referral rate hooks
		 */

		// CF7 settings
		// add_filter( 'wpcf7_editor_panels', array( $this, 'register_cf7_settings' ) );

		// // CF7 settings content
		// add_action('wpcf7_admin_after_additional_settings', array( $this, 'settings_tab_content' ) );

		// // Save CF7 AffWP settings
		// add_action('wpcf7_save_contact_form', array( $this, 'save_settings' ) );


		/**
		 * paypal1
		 */

		// Add PayPal meta to the contact form submision object.
		add_action( 'wpcf7_submit', array( $this, 'add_paypal_meta' ), 1, 2 );

		// Add a pending referral.
		add_filter( 'wpcf7_submit', array( $this, 'add_pending_referral' ), 10, 2 );

		// Process paypal1 redirect after generating the initial referral.
		remove_action( 'wpcf7_mail_sent', 'cf7pp_after_send_mail' );
		add_action( 'wpcf7_submit', 'cf7pp_after_send_mail', 20, 2 );

		// Mark referral complete.
		add_action( 'wp_footer', array( $this, 'mark_referral_complete' ), 9999 );

		// Revoke referral.
		add_action( 'wp_footer', array( $this, 'revoke' ), 9999 );


		/**
		 * paypal2
		 */

		// Hook on the correct CF7 action
		remove_action( 'wpcf7_add_shortcode', 'contact_form_7_paypal_submit' );
		add_action( 'wpcf7_add_form_tag ',    'contact_form_7_paypal_submit', 10, 3 );


		// Set reference
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}

	/**
	 * Include Contact Form 7 functions
	 * @access  public
	 * @since   2.0
	 */
	public function include_cf7_functions() {
		require_once ( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/contactform7-functions.php' );
	}

	/**
	 * Defines supported core payment gateways for the Contact Form 7 integration.
	 *
	 * This method cannot be directly overidden.
	 * To add support for others, use the filter in the `get_supported_gateways` method.
	 *
	 * @since  2.0
	 * @access private
	 * @return array $gateways Contact Form 7 payment gateways supported by AffiliateWP core.
	 */
	private function _supported_gateways() {
		$gateways = array(
			'contact-form-7-paypal-add-on',
			'contact-form-7-paypal-extension'
		);

		return $gateways;
	}

	/**
	 * Defines supported CF7 payment gateways, with an option to add additional ones
	 *
	 * @since  2.0
	 *
	 * @return array $gateways Contact Form 7 payment gateways.
	 */
	public function get_supported_gateways() {

		$gateways = $this->_supported_gateways();

		/**
		 * Defines the supported payment gateways for the Contact Form 7 integration.
		 *
		 * @param array $gateways List of payment gateways supported.
		 *
		 * @since 2.0
		 */
		return apply_filters( 'affwp_cf7_payment_gateways', $gateways );
	}

	/**
	 * Register the Contact Form 7 integration settings tab
	 *
	 * @access public
	 * @since  2.0
	 * @return array The new tab name
	 */
	public function register_settings_tab( $tabs = array() ) {

		$tabs['contactform7'] = __( 'Contact Form 7', 'affiliate-wp' );

		return $tabs;
	}

	/**
	 * Adds AffiliateWP integration settings
	 *
	 * @access public
	 * @since  2.0
	 * @param  array $settings The existing settings
	 * @return array $settings The updated settings
	 */
	public function register_settings( $settings = array() ) {

		// TODO - add updated docs url for CF7 integration
		$doc_url = 'http://docs.affiliatewp.com/article/TODO';

		$settings[ 'contactform7' ] = array(
			'affwp_cf7_enable_all_forms' => array(
				'name' => __( 'Enable referrals on all Contact Form 7 forms', 'affiliate-wp' ),
				'desc' => sprintf( __( 'Check this box to enable referrals on all Contact Form 7 forms.<ul><li>%3$s Once enabled, referrals will be generated for all valid Contact Form 7 forms.</li><li>%2$s <a href="%1$s" target="_blank">Documentation for this integration</a></li></ul>', 'affiliate-wp' ),
					/**
					 * The Contact Form 7 Help Scout docs url displayed within plugin settings.
					 *
					 * @param  $doc_url Help Scout docs url to provide within plugin settings.
					 *
					 * @since  1.0
					 */
					esc_url( apply_filters( 'afwp_cf7_admin_docs_url', $doc_url ) ),
					'<span class="dashicons dashicons-external"></span>',
					'<span class="dashicons dashicons-info"></span>'
				),
				'type' => 'checkbox'
			),
			'affwp_cf7_enable_specific_forms' => array(
				'name' => '<strong>' . __( 'Enable referrals for specific Contact Form 7 forms', 'affiliate-wp' ) . '</strong>',
				'type' => 'multicheck',
				'options' => $this->all_forms_multicheck_render()
			)
		);

		return $settings;
	}

	/**
	 * Define default Contact Form 7 AffiliateWP settings.
	 *
	 * These options store data for the folowing:
	 *
	 * - `enable_all`: Whether all CF7 forms have referral tracking enabled.
	 * - `enabled_forms`: An array which defines the CF7 forms that have AffiliateWP referral tracking enabled, specified by form ID.
	 * - `has_paypal_1`: Whether the first PayPal add-on, `contact-form7-paypal-add-on`, is installed and active.
	 * - `has_paypal_2`: Whether the second PayPal add-on, `contact-form7-paypal-extension`, is installed and active.
	 *
	 * @since  2.0
	 *
	 * @return void
	 */
	public function options() {
		// Set defaults if option doesn't exist.
		if( ! get_option( 'affwp_cf7_options' ) ) {

			$enabled = $this->get_enabled_forms();

			$options = array(
				'enable_all'    => false,
				'enabled_forms' => $enabled,
				'has_paypal_1'  => false,
				'has_paypal_2'  => false
			);

			add_option( 'affwp_cf7_options', $options );
		}
	}

	/**
	 * Get forms which have AffiliateWP enabled.
	 * Directly checks the `wpcf7_contact_form` post type.
	 *
	 * @since  2.0
	 *
	 * @return array $enabled_forms All enabled CF7 forms
	 */
	public function get_all_forms() {

		$all_forms = array();

		$args = array(
			'post_type'   => array( 'wpcf7_contact_form' ),
			'post_status' => array( 'publish' )
		);

		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id               = get_the_ID();

				$all_forms[ $post_id ] = get_the_title();
			}
		}

		wp_reset_postdata();

		return $all_forms;
	}

	public function all_forms_multicheck_render() {

		$cf7_forms = array();

		$all_forms = $this->get_all_forms();

		foreach ( $all_forms as $id => $title ) {
			array_push( $cf7_forms, '<strong>' . $title . '</strong> <em>(' . __( 'Form ID: ', 'affiliate-wp' ) . $id . ' )</em>' );
		}

		return $cf7_forms;
	}

	/**
	 * Get forms which have AffiliateWP enabled.
	 *
	 * @since  2.0
	 *
	 * @return $enabled  The enabled forms
	 */
	public function get_enabled_forms() {
		$enabled = array();
		$enabled = affiliate_wp()->settings->get( 'affwp_cf7_enable_specific_forms' );

		return apply_filters( 'affwp_cf7_enabled_forms', $enabled );
	}

	/**
	 * Payment gateway notice, shown if a valid CF7 payment gateway add-on is not installed and active.
	 *
	 * @since  2.0
	 *
	 * @return mixed $notice The notice.
	 */
	public function gateway_required_notice() {
		$notice  = '<div class="affwp-notices">';
		$notice .= sprintf( __( 'AffiliateWP: The AffiliateWP Contact Form 7 integration requires at least one payment gateway for Contact Form 7. Please install and configure one to continue. <a href="%s">Read the set-up guide and documentation for this integration</a>.', 'affiliate-wp' ),
			// TODO: define actual CF7 doc url
			esc_url( 'https://docs.affiliatewp.com/contact-form-7' )
		);
		$notice .= '</div>';

		return $notice;
	}

	/**
	 * Checks for a custom form referral rate, and returns that if defined.
	 *
	 * @since  2.0
	 *
	 * @param  mixed  $form_id  The CF7 form ID.
	 * @return mixed  $form_id  If a custom rate is found, it is returned. Otherwise, a boolean false is returned.
	 */
	public function get_custom_rate( $form_id ) {
		if ( ! $form_id ) {
			return false;
		}

		// TODO
		// Get custom rate meta
	}

	/**
	 * Checks if any valid payment gateway is enabled for the given CF7 form ID
	 *
	 * @since  2.0
	 *
	 * @return mixed If a valid gateway is found, the plugin ID string is returned
	 *               (eg, `contact-form-7-paypal-add-on`), otherwise a boolean false is returned.
	 */
	public function get_form_active_gateway( $form_id ) {

		if ( ! $form_id ) {
			return false;
		}

		// First paypal add-on
		$paypal_1 = get_post_meta( $form_id, '_cf7_pp_enable', true );

		// TODO
		// Second paypal add-on
		$paypal_2 = get_post_meta( $form_id, 'paypal_2', true );

		$supported = $this->get_supported_gateways();



		if ( $paypal_1 ) {

			return 'paypal_1';

		} elseif ( $paypal_2 ) {

			return 'paypal_2';

		} else {

			// Bail, since neither of the PayPal payment
			// gateways are configured for this form ID.

			// TODO: Define generic payment gateway support functionality
			return false;
		}

		return false;
	}

	/**
	 * Adds a settings tab to Contact Form 7 form admin settings
	 *
	 * @since  2.0
	 * @param  array $panels CF7 form settings
	 * @return array
	 */
	public function register_cf7_settings( $panels ) {

		$affwp_panel = array(
			'AffiliateWP' => array(
				'title' => __( 'AffiliateWP', 'affiliate-wp' ),
				'callback' => array ( $this, 'settings_tab_content' )
			)
		);

		$panels = array_merge( $panels, $affwp_panel );

		return $panels;
	}

	/**
	 * Load settings tab content
	 *
	 * @since  2.0
	 *
	 * @param  array  $cf7 Contact Form 7 form instance
	 *
	 * @return void
	 */
	public function settings_tab_content( $cf7 ) {

		$post_id = sanitize_text_field( $_GET['post'] );

		$referral_rate = get_post_meta( $post_id, 'referral_rate', true );

		// Check for active CF7 payment gateway for this form
		// (either `contact-form-7-paypal-add-on` or `contact-form-7-paypal-extension`).
		$gateway = $this->get_form_active_gateway( $post_id );


		// Form label messages
		$label_message__rate        = __( 'Specify a custom referral rate for this form (optional)', 'affiliate-wp' );
		$label_message__rate_type   = __( 'Specify a referral rate type. If checked, a flat amount will be used, otherwise a percentage will be used', 'affiliate-wp' );

		$output = "";
		$output .= "<form>";
		$output = "<div id='additional_settings-sortables' class='meta-box-sortables ui-sortable'>";

		$output .= "<div id='additionalsettingsdiv' class='postbox'>";

		$output .= "<div class='handlediv' title='" . __( 'Click to toggle', 'affiliate-wp', 'Title for an element which toggles the Contact Form 7 integration settings page tab.' ) . "'>";

		$output .= "</div>";

		$output .= "<h3 class='hndle ui-sortable-handle'>";
		$output .= "<span>";
		$output .= __( 'AffiliateWP Settings', 'affiliate-wp', 'Contact Form 7 settings tab label.' );
		$output .= "</span>";
		$output .= "</h3>";

		$output .= "<div class='inside'>";

		$output .= "<div class='affwp-referral-rate'>";

		$output .= "<label for='referral_rate'>" . $label_message__rate . "</label>";
		$output .= "<input name='referral_rate' value='" . get_post_meta( $post_id, 'referral_rate', true ) . "' type='text' />";

		$output .= "<label for='referral_rate_type'>" . $label_message__rate_type . "</label>";
		$output .= "<input name='referral_rate_type' value='1' type='checkbox' $checked>";

		// $output .= "<br>";

		$output .= "</div>";


		$output .= "</td></tr></table>";
		$output .= "</form>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div><!--/affwp settings-->";

		echo $output;
	}

	/**
	 * Save contact form settings
	 *
	 * @since  2.0
	 *
	 * @param  array  $contact_form  CF7 form data.
	 *
	 * @return void
	 */
	public function save_settings( $contact_form ) {

		$post_id = sanitize_text_field( $_POST['post'] );

		if ( $_POST['referral_rate'] ) {
			$referral_rate = sanitize_text_field( $_POST['referral_rate'] );
			$referral_rate = affwp_format_amount( absint( $referral_rate ) );
			update_post_meta( $post_id, "referral_rate", $referral_rate );
		} else {
			update_post_meta( $post_id, "referral_rate", 0 );
		}

		if ( $_POST['referral_rate_type'] ) {
			$referral_rate_type = sanitize_text_field( $_POST['referral_rate_type'] );
			$referral_rate_type = affwp_format_amount( absint( $referral_rate_type ) );
			update_post_meta( $post_id, "referral_rate_type", $referral_rate_type );
		} else {
			update_post_meta( $post_id, "referral_rate_type", 0 );
		}

	}


	/**
	 * Adds PayPal add-on meta to the form object.
	 *
	 * @param stdClass  $contactform The contact form data.
	 * @return stdClass $contactform The modified contact form data.
	 * @since 2.0
	 *
	 */
	public function add_paypal_meta( $contactform ) {

		$form_id = $contactform->id();

		update_post_meta( $form_id, 'affwp_cf7_form_id', $form_id );

		$enabled     = get_post_meta( $form_id, '_cf7pp_enable', true );
		$email       = get_post_meta( $form_id, '_cf7pp_email',  true );
		$amount      = get_post_meta( $form_id, '_cf7pp_price',  true );
		$description = get_post_meta( $form_id, '_cf7pp_name',   true );
		$sku         = get_post_meta( $form_id, '_cf7pp_id',     true );

		// Temporarily cast object and add referral data.
		$contactform = (object) array_merge(
			(array) $contactform, array(
				'affwp_paypal_enabled'       => $enabled,
				'affwp_customer_email'       => $email,
				'affwp_base_amount'          => $amount,
				'affwp_referral_description' => $description,
				'affwp_product_sku'          => $sku
			)
		);

		return $contactform;
	}

	/**
	 * Adds a referral when a form is submitted.
	 *
	 * @since 2.0
	 *
	 * @param object $contact_form CF7 form submission object.
	 * @param object $result      Submitted CF7 form submission data.
	 */
	public function add_pending_referral( $contactform, $result ) {

		$form_id = absint( $contactform->id() );

		if ( ! $form_id ) {
			return false;
		}

		$form    = get_post( $form_id );
		$paypal1 = get_post_meta( $form_id, '_cf7pp_enable', true );
		$paypal2 = false;

		// Bail if neither PayPal add-on is active and enabled.
		if ( false === $paypal1 && false === $paypal2 ) {
			return false;
		}

		if ( $this->was_referred() ) {

			if ( 1 === $paypal1 || 1 == $paypal1 || '1' == $paypal1 ) {

				$email        = get_post_meta( $form_id, '_cf7pp_email',  true );
				$sku          = get_post_meta( $form_id, '_cf7pp_id',     true );
				$reference    = $form_id . '-' . date_i18n( 'Y-m-d' );
				$description  = get_post_meta( $form_id, '_cf7pp_name',   true );
				$base_amount  = floatval( get_post_meta( $form_id, '_cf7pp_price',  true ) );
				$affiliate_id = $this->get_affiliate_id( $reference );

				$referral_total  = $this->calculate_referral_amount( $base_amount, $reference, $sku, $affiliate_id );

			} elseif ( 1 === $paypal2 ) {

				$email        = get_post_meta( $form_id, '_cf7pp_email',  true );
				$sku          = get_post_meta( $form_id, '_cf7pp_id',     true );
				$reference    = $form_id . '-' . $contactform->timestamp;
				$description  = get_post_meta( $form_id, '_cf7pp_name',   true );
				$base_amount  = floatval( get_post_meta( $form_id, '_cf7pp_price',  true ) );
				$affiliate_id = $this->get_affiliate_id( $reference );

				$referral_total  = $this->calculate_referral_amount( $base_amount, $reference, $sku, $affiliate_id );
			} else {
				return false;
			}

			$this->insert_pending_referral( $referral_total, $reference, $description, $sku );

			if ( empty( $referral_total ) ) {
				$this->mark_referral_complete( affwp_get_referral( $reference ) );
			}
		}
	}

	/**
	 * Updates the referral status when a PayPal refund or transaction completion occurs,
	 * via the success or cancel pages provided in the PayPal add-ons.
	 *
	 * @param mixed $reference The referral reference.
	 * @since 2.0
	 */
	public function mark_referral_complete( $reference ) {

		$this->complete_referral( $reference );

		$referral    = affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context );

		$amount      = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name        = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$description = sprintf( __( 'AffiliateWP: Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name );
	}

	/**
	 * Update referral status. Fires when the cancel page url is hit from a PayPal transaction.
	 *
	 * @param  string $reference        The reference.
	 * @param  int    $current_page_id  The current page ID.
	 * @return mixed void|bool          Returns nothing if successful, returns boolean false if the
	 * @since 2.0                       current page ID does not match the PayPal cancel page ID.
	 *
	 */
	public function revoke( $current_page_id, $reference ) {

		$current_page_id = get_the_ID();

		$cancel_url     = $this->cancel_url;
		$cancel_page    = get_page_by_path( basename( untrailingslashit( $cancel_url ) ) );
		$cancel_page_id = absint( $cancel_page->ID );

		// Bail if not on the cancel page
		if ( $cancel_page_id !== $current_page_id ) {
			return false;
		}

		$this->reject_referral( $reference );

		$referral        = affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context );
		$amount          = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name            = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$description     = sprintf( __( 'AffiliateWP: Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );
	}

	/**
	 * Link to Contact Form 7 form in the referral reference column.
	 *
	 * @param  int    $reference
	 * @param  object $referral
	 * @return string
	 * @since  2.0
	 *
	 */
	public function reference_link( $reference, $referral ) {

		// To provide a working link to the CF7 form,
		// the date is stripped from the reference string.
		$reference = strstr( $reference, '-', true );

		$url = admin_url( 'admin.php?page=wpcf7&action=edit&post=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

}
new Affiliate_WP_Contact_Form_7;
