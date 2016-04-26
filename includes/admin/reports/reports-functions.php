<?php
/**
 * Functions which return statistical data specific to various contexts
 *
 * @package     AffiliateWP
 * @subpackage  Functions/Reports
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
*/

/**
 * Displays referrals ordereed by the highest-performing reference
 * (either specified by the AffiliateWP integration in use, or by a custom-set reference)
 *
 * @since 1.8
 * @return string The referral context which has the greatest number of accepted referrals
 */
function affwp_get_referrals_by_reference() {

    $referrals = affiliate_wp()->referrals->get_referrals( apply_filters( 'affwp_overview_most_valuable_references', array( 'number' => '5', 'orderby' => 'reference', 'order' => 'DESC' ) ) );

        // An array used to determine the most frequently-occurring reference. (TODO)
        // $by_reference = array();

        ?>
        <table class="affwp_table">

            <thead>

                <tr>
                    <th><?php _e( 'Reference', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Amount', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Context', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Date', 'affiliate-wp' ); ?></th>
                </tr>

            </thead>

            <tbody>
            <?php if( $referrals ) : ?>
                <?php

                    foreach( $referrals as $referral  ) :

                    // Push to the $by_reference array
                    // so there's an available array to sort
                    // to determine the most frequently-occurring
                    // reference. (TODO)
                    // array_push( $by_reference, $referral->context ); ?>

                    <tr>
                        <td><?php echo $referral->reference; ?></td>
                        <td><?php echo $referral->amount; ?></td>
                        <td><?php echo $referral->context; ?></td>
                        <td><?php echo human_time_diff( strtotime( $referral->date ), time() ) . __( ' ago', 'affiliate-wp' ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php _e( 'No references have been defined.', 'affiliate-wp' ); ?></td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>
    <?php
}

/**
 * Get the creative which have generated the highest quantity of referrals,
 * within a specified date range.
 *
 * @since 1.8
 * @return array List of creatives which have generated the highest quantity
 * of referrals within a specified date range
 */
function affwp_get_referrals_by_creatives() {

	// A sub-method must first be created which optionally tracks creatives
	// via url parameter, for example:
	// example.com?ref=10&c=2 where the integer 2 in thise case refers to the ID of the
	// creative which was used.

	$by_creatives = '';
	return $by_creatives;
}
