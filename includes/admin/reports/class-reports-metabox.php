<?php
/**
 * Reports Admin Metabox Base class
 * Provides a base structure for reports content metaboxes
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Affiliate_WP_Reports_Metabox_Base {

	/**
	 * Constructor
	 *
	 * @access  public
	 * @since   1.8
	 */
	public function __construct() {

		$this->init();

	}

	/**
	 * Gets things started
	 *
	 * @access  public
	 * @since   1.8
	 * @return  void
	 */
	public function init() {

	}



}
