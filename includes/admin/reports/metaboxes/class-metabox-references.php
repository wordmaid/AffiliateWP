<?php


class AffWP_Metabox_Reports_Top_Performers extends AffWP_Metabox_Base {


    /**
    * The id of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.9
    */
    public $meta_box_id = 'reports-top-performers';

    /**
    * The name of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.9
    */
    public $meta_box_name;

    /**
     * The position in which the meta box will be loaded
     * Either 'normal' side'.
     *
     * @access  public
     * @var     $context
     * @since   1.9
     */
    public $context = 'tertiary';

    /**
     * Initialize
     *
     * @access  public
     * @return  void
     * @since   1.9
     */
    public function init() {
        add_action( 'add_meta_box',     array( $this, 'add_meta_box' ) );
        add_action( 'affwp_reports_meta_boxes', array( $this, 'add_meta_box' ) );
        $this->meta_box_name = __( 'Top Performing References', 'affiliate-wp' );
    }

    /**
     * Displays an overview of earnings in 3 different tables
     *
     * @return mixed content An overview of referrals and earnings
     * @since  1.9
     */
    public function content() {
?>

        <h3>
            <?php echo __( 'These are the most recently-used references on the site.', 'affiliate-wp' ); ?>
        </h3>

        <?php echo affwp_get_referrals_by_reference(); ?>

    <?php }
}

new AffWP_Metabox_Reports_Top_Performers;
