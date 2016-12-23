<?php

class Affiliate_WP_WPForms extends Affiliate_WP_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function init() {

		$this->context = 'wpforms';

	}


}
new Affiliate_WP_WPForms;
