<?php

/**
 * Contact Form 7 integration class.
 *
 * @since 2.0
 */
class Affiliate_WP_Contact_Form_7 extends Affiliate_WP_Base {

	/**
	 * @see Affiliate_WP_Base::init
	 * @access  public
	 * @since   2.0
	 */
	public function init() {

		$this->context = 'contactform7';

		// Misc AffWP CF7 functions
		add_action( 'wpcf7_admin_init', array( $this, 'include_cf7_functions' ) );

		// CF7 settings
		add_filter( 'wpcf7_editor_panels', array( $this, 'register_settings' ) );

		// CF7 settings content
		add_action('wpcf7_admin_after_additional_settings', array( $this, 'settings_tab_content' ) );

		// Save CF7 AffWP setting
		add_action('wpcf7_save_contact_form', array( $this, 'save_settings' ) );
		// add_action('save_post', array( $this, 'save_settings' ) );

		add_filter( 'wpcf7_before_send_mail', array( $this, 'add_pending_referral' ), 9, 2 );

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
		if( class_exists( 'WPCF7' ) ) {
			require_once ( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/contactform7-functions.php' );
		}
	}

	/**
	 * Adds a settings tab to Contact Form 7 form admin settings
	 *
	 * @since  2.0
	 * @param  array $panels CF7 form settings
	 * @return array
	 */
	public function register_settings( $panels ) {

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
	 * Show a notice if Flamingo is not installed and active.
	 *
	 * @since  2.0
	 *
	 * @return [type]  [description]
	 */
	public function flamingo_required_notice() {
		$notice  = '<div class="affwp-notices">';
		$notice .= sprintf( __( 'AffiliateWP: The AffiliateWP Contact Form 7 integration requires the <a href="%s">Flamingo Contact Form 7 add-on</a> to be installed and active. Please install it to continue.', 'affiliate-wp' ),
			esc_url( 'https://wordpress.org/plugins/flamingo' )
		);

		$notice .= '</div>';
	}

	/**
	 * Check for the CF7 Flamingo addon
	 *
	 * @since  2.0
	 *
	 * @return bool True if Flamingo is installed and active, otherwise false.
	 */
	public function has_flamingo() {

		/**
		 * Flaming stores CF7 entries in the `flamingo_inbound` cpt,
		 * via the `Flamingo_Inbound_Message` class.
		 */
		if ( post_type_exists( 'flamingo_inbound' ) && class_exists( 'Flamingo_Inbound_Message' ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Load settings tab content
	 *
	 * @since  2.0
	 *
	 * @param  [type]  $cf7 Contact Form 7 form instance
	 *
	 * @return [type]       [description]
	 */
	public function settings_tab_content( $cf7 ) {

		// Check for Flamingo (plugin slug: `flamingo`).
		if ( ! $this->has_flamingo() ) {
			echo $this->flamingo_required_notice();
			return;
		}

		if ( isset( $_GET['post'] ) ) {
			$post_id = $_GET['post'];
		}

		$post_id = intval( $_GET['post'] );


		$affwp_cf7_enabled = get_post_meta( $post_id, "_affwp_cf7_enabled", true );

		if ( $affwp_cf7_enabled ) {
		error_log( 'enabled it is!');
			$checked = "checked";
		} else {
			$checked = "";
		}

		error_log( 'enabled is: ' . $affwp_cf7_enabled);

		// Check for PayPal extension (plugin slug: `contact-form-7-paypal-add-on`).
		$name    = get_post_meta( $post_id, "_cf7pp_name",        true );
		$price   = get_post_meta( $post_id, "_cf7pp_price",       true );
		$id      = get_post_meta( $post_id, "_cf7pp_id",          true );
		$email   = get_post_meta( $post_id, "_cf7pp_email",       true );

		// Check for PayPal extension (plugin slug: `contact-form-7-paypal-extension`).
		// todo
		?>

		<form>
			<div id="additional_settings-sortables" class="meta-box-sortables ui-sortable"><div id="additionalsettingsdiv" class="postbox">
			<div class="handlediv" title="<?php _e( 'Click to toggle', 'affiliate-wp' ); ?>">
			</div>
			<h3 class="hndle ui-sortable-handle">
				<span><?php _e( 'AffiliateWP Settings', 'affiliate-wp' ); ?></span>
			</h3>
			<div class="inside">

				<div class="affwp-cf7-enabled">
					<input name="affwp_cf7_enabled_field" type="checkbox" <?php
					$current = 1;
					checked( get_post_meta( $post_id, "_affwp_cf7_enabled", true ), $current, 1 ); ?>
					<label><?php _e( 'Enable referrals for this form', 'affiliate-wp' ); ?></label>
				</div>

				<input type="hidden" name="email" value="2">
				<input type="hidden" name="post" value="<?php echo $post_id; ?>">
		</form>
	</div>
	</div>
	</div>
<?php }

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

		$post_id = sanitize_text_field( $_GET['post'] );

		if ( isset( $_POST['affwp_cf7_enabled_field'] ) ) {

			$affwp_cf7_enabled = $_POST['affwp_cf7_enabled_field'];

			// $post_id, $meta_key, $meta_value, $prev_value
			update_post_meta( $post_id, "_affwp_cf7_enabled", true );
		} else {
			update_post_meta( $post_id, "_affwp_cf7_enabled", false );
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
	public function add_pending_referral( $contact_form ) {

		// The Contact Form 7 form ID
		$form_id = $contact_form->id();

		return;

		// Flamingo post ID
		// $entry_id = $contact_form[id];


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
	 * Update referral status and add note to Contact Form 7 entry
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
	 * Update referral status and add note to Contact Form 7 entry
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
	 * Link to Contact Form 7 entry in the referral reference column
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

	/**
	 * Helper function to retrieve a value from an array
	 *
	 * @since 2.0
	 *
	 *
	 * @param array  $array
	 * @param string $key
	 *
	 * @return string
	 */
	public static function get_array_value( $array, $key ) {

		return isset( $array[ $key ] ) ? $array[ $key ] : '';

	}

	/**
	 * Helper function to retrieve a value from a multidimensional array
	 *
	 * @since 2.0
	 *
	 *
	 * @param array  $array
	 * @param string $key
	 *
	 * @return string
	 */
	public static function get_array_values( $array, $keys ) {

		$keys  = explode( '/', $keys );
		$value = $array;

		foreach ( $keys as $current_key ) {
			$value = self::get_array_value( $value, $current_key );
		}

		return $value;

	}

}
new Affiliate_WP_Contact_Form_7;
