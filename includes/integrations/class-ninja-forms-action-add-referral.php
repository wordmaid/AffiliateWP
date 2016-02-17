<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * Class Affiliate_WP_Ninja_Forms_ActionAddReferral
 */
final class Affiliate_WP_Ninja_Forms_ActionAddReferral extends NF_Abstracts_Action
{
    /**
     * @var string
     */
    protected $_name  = 'affiliatewp_add_referral';

    /**
     * @var array
     */
    protected $_tags = array( 'affiliate', 'affiliatewp', 'referral' );

    /**
     * @var string
     */
    protected $_timing = 'late';

    /**
     * @var int
     */
    protected $_priority = '10';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __( 'Add Referral', 'ninja-forms' );

        $this->_settings[ 'affiliatewp_total' ] = array(
            'name' => 'affiliatewp_total',
            'label' => __( 'Total Field' ),
            'type' => 'textbox',
            'width' => 'full',
            'value' => '',
            'group' => 'primary',
            'use_merge_tags' => array(
                'exclude' => array(
                    'post',
                    'user',
                    'system'
                )
            ),
        );

        $this->_settings[ 'affiliatewp_email' ] = array(
            'name' => 'affiliatewp_email',
            'label' => __( 'Customer Email' ),
            'type' => 'textbox',
            'width' => 'full',
            'value' => '',
            'group' => 'primary',
            'use_merge_tags' => array(
                'exclude' => array(
                    'user',
                )
            ),
        );

        $this->_settings[ 'affiliatewp_description' ] = array(
            'name' => 'affiliatewp_description',
            'label' => __( 'Description' ),
            'type' => 'textbox',
            'width' => 'full',
            'value' => '',
            'group' => 'advanced',
        );
    }

    /*
    * PUBLIC METHODS
    */

    public function save( $action_settings )
    {

    }

    public function process( $action_settings, $form_id, $data )
    {
        $referral_total = $this->get_total( $action_settings );
        $reference = $this->get_reference( $data );
        $description = $this->get_description( $action_settings, $data );
        $customer_email = $this->get_customer_email( $action_settings );

        $args = $data[ 'extra' ][ 'affiliatewp' ] = compact( 'referral_total', 'reference', 'description', 'customer_email' );

        do_action( 'nf_affiliatewp_add_referral', $args );

        return $data;
    }

    /*
    * PRIVATE METHODS
    */

    private function get_total( $action_settings )
    {
        $total = 0;
        if( isset( $action_settings[ 'affiliatewp_total' ] ) ){
            $total = $action_settings[ 'affiliatewp_total' ];
        }
        return $total;
    }

    private function get_reference( $data )
    {
        $reference = '';
        if( isset( $data[ 'actions' ][ 'save' ][ 'id' ] ) ){
            $reference = $data[ 'actions' ][ 'save' ][ 'id' ];
        }
        return $reference;
    }

    private function get_description( $action_settings, $data )
    {
        $description = '';
        if( isset( $action_settings[ 'affiliatewp_description' ] ) ){
            $description = $action_settings[ 'affiliatewp_description' ];
        } elseif( isset( $data[ 'settings' ][ 'title' ] ) ){
            $description = $data[ 'settings' ][ 'title' ];
        }
        return $description;
    }

    private function get_customer_email( $action_settings )
    {
        $email = 0;
        if( isset( $action_settings[ 'affiliatewp_email' ] ) ){
            $email = $action_settings[ 'affiliatewp_email' ];
        }
        return $email;
    }

}
