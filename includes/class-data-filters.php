<?php
/**
 * AffiliateWP Data Filters Class
 *
 * This class serves as an object with minimal
 * requirements for children.
 *
 * The primary purpose is to call new instances
 * of two or more other classes within AffWP_Data_Filters,
 * so that their variable and method values are available,
 * if public, to one another.
 *
 * A base structure of example data is provided below:
 *
 * private $example_data;
 *
 * public function example_store( $example_value ) {
 *
 *     $this->$example_data = $example_value;
 * }
 *
 * public function example_fetch() {
 *
 *     return $this->$example_data;
 * }
 *
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Tools
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class AffWP_Data_Filters {

}
