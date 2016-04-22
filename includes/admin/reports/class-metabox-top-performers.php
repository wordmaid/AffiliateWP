<?php


class AffWP_Metabox_Top_Performers extends AffWP_Metabox_Base {


    /**
    * The id of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.8
    */
    public $meta_box_id = 'overview-top-performers';

    /**
    * The name of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.8
    */
    public $meta_box_name = 'Top Performers';

    /**
     * Displays the an overview of earnings in 3 different tables
     *
     * @return mixed content An overview of referrals and earnings
     * @since  1.8
     */
    public function content() {
        ?>

        <h3>
            <?php echo __( 'References', 'affiliate-wp' ); ?>
        </h3>

        <?php echo affwp_get_referrals_by_reference(); ?>

    <?php }
}

new AffWP_Metabox_Top_Performers;
