<?php

/**
 * Contact Form 7 integration class.
 *
 * @since 2.0
 */
class Affiliate_WP_Contact_Form_7 extends Affiliate_WP_Base {

	/**
	 * The supported CF7 payment gateway integrations.
	 *
	 * @var array
	 */
	public $supported_gateways;

	/**
	 * @see Affiliate_WP_Base::init
	 * @access  public
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'contactform7';

		$this->supported_gateways = $this->get_supported_gateways();

		// Misc AffWP CF7 functions
		$this->include_cf7_functions();

		add_filter( 'affwp_settings_tabs', array( $this, 'register_settings_tab' ) );
		add_filter( 'affwp_settings', array( $this, 'register_settings' ) );

		// CF7 settings
		add_filter( 'wpcf7_editor_panels', array( $this, 'register_cf7_settings' ) );

		// CF7 settings content
		add_action('wpcf7_admin_after_additional_settings', array( $this, 'settings_tab_content' ) );

		// Save CF7 AffWP setting
		add_action('wpcf7_save_contact_form', array( $this, 'save_settings' ) );

		add_filter( 'publish_flamingo_inbound', array( $this, 'add_pending_referral' ), 10, 2 );

		// Mark referral complete
		// add_action( 'todo', array( $this, 'mark_referral_complete' ), 10, 2 );

		// Revoke referral.
		// add_action( 'todo', array( $this, 'revoke_referral_on_refund' ), 10, 2 );

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
	 *
	 * @return array $gateways Contact Form 7 payment gateways supported by AffiliateWP core.
	 */
	private function _supported_gateways() {
		$gateways = array(
			'contact-form-7-paypal-add-on',
			'contact-form-7-paypal-extension'
		);
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

		$tabs['contactform7'] = __( 'Contact Form 7', 'affiliatewp-zapier' );

		return $tabs;
	}

	/**
	 * Add our settings
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
				'name' => __( 'Enable referrals on all Contact Form 7 forms', 'affiliatewp-zapier' ),
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
				'name' => '<strong>' . __( 'Enable referrals for specific Contact Form 7 forms', 'affiliatewp-zapier' ) . '</strong>',
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
	 * - `enabled_forms`: Array defining which CF7 forms have AffiliateWP referral tracking enabled, specified by form ID.
	 * - `has_flamingo`: Whether the `flamingo` CF7 add-on is installed and active.
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
				'has_flamingo'  => false,
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
	 * @return $enabled  [description]
	 */
	public function get_enabled_forms() {
		$enabled = array();
		$enabled = affiliate_wp()->settings->get( 'affwp_cf7_enable_specific_forms' );

		return apply_filters( 'affwp_cf7_enabled_forms', $enabled );
	}

	/**
	 * Show a notice if Flamingo is not installed and active.
	 *
	 * @since  2.0
	 *
	 * @return mixed $notice The notice.
	 */
	public function flamingo_required_notice() {
		$notice  = '<div class="affwp-notices">';
		$notice .= sprintf( __( 'AffiliateWP: The AffiliateWP Contact Form 7 integration requires the <a href="%s">Flamingo Contact Form 7 add-on</a> to be installed and active. Please install it to continue.', 'affiliate-wp' ),
			esc_url( 'https://wordpress.org/plugins/flamingo' )
		);
		$notice .= '</div>';

		return $notice;
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
	 * Check for the CF7 Flamingo add-on
	 *
	 * @since  2.0
	 *
	 * @return bool True if the plugin `flamingo` is installed and active, otherwise false.
	 */
	public function has_flamingo() {

		/**
		 * `flamingo` stores CF7 entries in the `flamingo_inbound` cpt,
		 * via the `Flamingo_Inbound_Message` class.
		 */
		if ( post_type_exists( 'flamingo_inbound' ) && class_exists( 'Flamingo_Inbound_Message' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks for a custom form referral rate, and returns that if defined.
	 *
	 * @since  [since
	 *
	 * @param  mixed  $form_id  The CF7 form ID.
	 * @return mixed  $form_id  If a custom rate is found, it is returned. Otherwise, a boolean false is returned.
	 */
	public function get_custom_rate( $form_id ) {
		if ( ! $form_id ) {
			return false;
		}
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

		// Second paypal add-on - TODO
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

		// Check for Flamingo
		if ( ! $this->has_flamingo() ) {
			echo $this->flamingo_required_notice();
			return;
		}

		$post_id = sanitize_text_field( $_GET['post'] );

		$referral_rate = get_post_meta( $post_id, 'referral_rate', true );

		// Check for active CF7 payment gateway for this form
		// (either `contact-form-7-paypal-add-on` or `contact-form-7-paypal-extension`).
		$gateway = $this->get_form_active_gateway( $post_id );


		// Form label message
		$label_message = __( 'Specify a custom referral rate for this form (optional)', 'affiliate-wp' );

		// $output  = "<form method='post' action='" . esc_url( admin_url( 'admin.php?page=wpcf7&tab=4' ) ) . "'>";
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
		$output .= "<input name='referral_rate' value='" . get_post_meta( $post_id, 'referral_rate', true ) . "' type='text' />";
		$output .= "<label>" . $label_message . "</label>";
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

		$post_id = sanitize_text_field($_POST['post'] );

		// wpcf7_form_hidden_fields filter

		error_log(print_r( $_POST, true) );

		if ( $_POST['referral_rate'] ) {
			$referral_rate = sanitize_text_field( $_POST['referral_rate'] );
			update_post_meta( $post_id, "referral_rate", $referral_rate );
		} else {
			update_post_meta( $post_id, "referral_rate", 0 );
		}
	}

	/**
	 * Add referral when form is submitted
	 *
	 * @since 2.0
	 *
	 * @param int $cf7 Contact Form 7 form submission
	 * @param int $form_id
	 * @param int $form_id
	 */
	public function add_pending_referral( $cf7 ) {

		// TODO - re-do to use Flamingo cpt hook

		global $postid;

		// The Contact Form 7 form ID
		$postid = $cf7->id();

		if ( $this->was_referred() ) {

			$form            = $frm_form->getOne( $form_id );
			$description     = $frm_entry_meta->get_entry_meta_by_field( $entry_id, $form->options['affiliatewp']['referral_description_field'] );
			$purchase_amount = floatval( $frm_entry_meta->get_entry_meta_by_field( $entry_id, $form->options['affiliatewp']['purchase_amount_field'] ) );

			$referral_total = $this->calculate_referral_amount( $purchase_amount, $entry_id );

			$this->insert_pending_referral( $referral_total, $entry_id, $description );

			if ( empty( $referral_total ) ) {
				$this->mark_referral_complete( $entry_id, $form_id );
			}
		}

	}

	/**
	 * Update referral status and add note to Contact Form 7 Flamingo entry
	 *
	 * @since 2.0
	 *
	 *
	 * @param int $entry_id
	 * @param int $form_id
	 */
	public function mark_referral_complete( $entry_id, $form_id ) {

		$entry_id =

		$this->complete_referral( $entry_id );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $entry_id, $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'AffiliateWP: Referral #%d for %s recorded for %s', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		$frm_entry_meta->add_entry_meta( $entry_id, 0, '', array( 'comment' => $note, 'user_id' => 0 ) );

	}

	/**
	 * Update referral status and add note to Contact Form 7 Flamingo entry
	 *
	 * @since 2.0
	 *
	 *
	 * @param int $entry_id
	 * @param int $form_id
	 */
	public function revoke_referral_on_refund( $entry_id, $form_id ) {

		global $frm_entry_meta;

		$this->reject_referral( $entry_id );

		$referral = affiliate_wp()->referrals->get_by( 'reference', $entry_id, $this->context );
		$amount   = affwp_currency_filter( affwp_format_amount( $referral->amount ) );
		$name     = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
		$note     = sprintf( __( 'AffiliateWP: Referral #%d for %s for %s rejected', 'affiliate-wp' ), $referral->referral_id, $amount, $name );

		$frm_entry_meta->add_entry_meta( $entry_id, 0, '', array( 'comment' => $note, 'user_id' => 0 ) );

	}

	/**
	 * Link to Contact Form 7 Flamingo entry in the referral reference column
	 *
	 * @since 2.0
	 *
	 * @param int    $reference
	 * @param object $referral
	 *
	 * @return string
	 *
	 */
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'formidablepro' != $referral->context ) {

			return $reference;

		}

		$url = admin_url( 'admin.php?page=formidable-entries&frm_action=show&id=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

}
new Affiliate_WP_Contact_Form_7;
