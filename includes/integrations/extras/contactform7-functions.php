<?php
/**
 * Temporarily overrides Flaingo `flamingo_inbound` posttype creation.
 *
 * This function is used in development, and provides debugging data.
 *
 * Ultimately, a usable method (injecting additional flamingo_inbound cpt meta to log referral data) will be used in lieu of this.
 *
 * @since  2.0
 *
 * @todo remove
 */
function affwp_wpcf7_flamingo_submit( $contactform, $result ) {

    // error_log( 'Contact form data object: '. print_r( $contactform, true ) );

    if ( ! class_exists( 'Flamingo_Contact' )
    || ! class_exists( 'Flamingo_Inbound_Message' ) ) {
        return;
    }

    if ( $contactform->in_demo_mode()
    || $contactform->is_true( 'do_not_store' ) ) {
        return;
    }

    $cases = (array) apply_filters( 'wpcf7_flamingo_submit_if',
        array( 'spam', 'mail_sent', 'mail_failed' ) );

    if ( empty( $result['status'] )
    || ! in_array( $result['status'], $cases ) ) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();

    if ( ! $submission || ! $posted_data = $submission->get_posted_data() ) {
        return;
    }

    $fields_senseless = $contactform->scan_form_tags(
        array( 'type' => array( 'captchar', 'quiz', 'acceptance' ) ) );

    $exclude_names = array();

    foreach ( $fields_senseless as $tag ) {
        $exclude_names[] = $tag['name'];
    }

    $exclude_names[] = 'g-recaptcha-response';

    foreach ( $posted_data as $key => $value ) {
        if ( '_' == substr( $key, 0, 1 ) || in_array( $key, $exclude_names ) ) {
            unset( $posted_data[$key] );
        }
    }

    $email = wpcf7_flamingo_get_value( 'email', $contactform );
    $name = wpcf7_flamingo_get_value( 'name', $contactform );
    $subject = wpcf7_flamingo_get_value( 'subject', $contactform );

    /**
     *  AffiliateWP variables.
     */

    $form_id = $contactform->id();

    // paypal1
    $enabled     = get_post_meta( $form_id, 'affwp_paypal_enabled', true );
    $amount      = get_post_meta( $form_id, 'affwp_purchase_amount', true );
    $description = get_post_meta( $form_id, 'affwp_referral_description', true );
    $sku         = get_post_meta( $form_id, 'affwp_product_sku', true );

    $meta = array();

    $special_mail_tags = array( 'remote_ip', 'user_agent', 'url',
        'date', 'time', 'post_id', 'post_name', 'post_title', 'post_url',
        'post_author', 'post_author_email' );

    foreach ( $special_mail_tags as $smt ) {
        $meta[$smt] = apply_filters( 'wpcf7_special_mail_tags',
            '', '_' . $smt, false );
    }

    $akismet = isset( $submission->akismet ) ? (array) $submission->akismet : null;

    if ( 'mail_sent' == $result['status'] ) {
        Flamingo_Contact::add( array(
            'email' => $email,
            'name' => $name ) );
    }

    $channel_id = wpcf7_flamingo_add_channel(
        $contactform->name(), $contactform->title() );

    if ( $channel_id ) {
        $channel = get_term( $channel_id,
            Flamingo_Inbound_Message::channel_taxonomy );

        if ( ! $channel || is_wp_error( $channel ) ) {
            $channel = 'contact-form-7';
        } else {
            $channel = $channel->slug;
        }
    } else {
        $channel = 'contact-form-7';
    }


    $args = array(
        'channel' => $channel,
        'subject' => $subject,
        'from' => trim( sprintf( '%s <%s>', $name, $email ) ),
        'from_name' => $name,
        'from_email' => $email,
        'fields' => $posted_data,
        'meta' => $meta,
        'akismet' => $akismet,
        'spam' => ( 'spam' == $result['status'] ),
        'affwp_paypal_enabled'       => $enabled,
        'affwp_customer_email'       => $email,
        'affwp_purchase_amount'      => $amount,
        'affwp_referral_description' => $description,
        'affwp_product_sku'          => $sku
        );

    Flamingo_Inbound_Message::add( $args );
}
