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

	// if ( affiliate_wp()->referrals->get_by( 'reference', $reference, $this->context ) ) {

	$references = 'This method will return referrals by the best-performing reference.';
	return $references;
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
