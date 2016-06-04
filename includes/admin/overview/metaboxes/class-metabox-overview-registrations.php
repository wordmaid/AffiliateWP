<?php

class AffWP_Metabox_Overview_Registrations extends AffWP_Metabox_Base {

    /**
    * The id of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.9
    */
    public $meta_box_id = 'overview-registrations';

    /**
     * Initialize
     *
     * @access  public
     * @return  void
     * @since   1.9
     */
    public function init() {
        add_action( 'add_meta_box',     array( $this, 'add_meta_box' ) );
        add_action( 'affwp_overview_left', array( $this, 'add_meta_box' ) );
        $this->meta_box_name = __( 'Latest Affiliate Registrations', 'affiliate-wp' );
    }

    /**
     * Displays the content of the metabox
     *
     * @return mixed content  The metabox content
     * @since  1.9
     */
    public function content() { ?>

        <?php $affiliates = affiliate_wp()->affiliates->get_affiliates( apply_filters( 'affwp_overview_latest_affiliate_registrations', array( 'number' => 5 ) ) ); ?>
        <table class="affwp_table">

            <thead>

                <tr>
                    <th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Status', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Actions', 'affiliate-wp' ); ?></th>
                </tr>

            </thead>

            <tbody>
                <?php if( $affiliates ) : ?>
                    <?php foreach( $affiliates as $affiliate  ) : ?>
                        <tr>
                            <td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
                            <td><?php echo affwp_get_affiliate_status_label( $affiliate ); ?></td>
                            <td>
                                <?php
                                if( 'pending' == $affiliate->status ) {
                                    $review_url = admin_url( 'admin.php?page=affiliate-wp-affiliates&action=review_affiliate&affiliate_id=' . $affiliate->affiliate_id );
                                    echo '<a href="' . esc_url( $review_url ) . '">' . __( 'Review', 'affiliate-wp' ) . '</a>';
                                } else {
                                    $affiliate_report_url = admin_url( 'admin.php?page=affiliate-wp-affiliates&action=view_affiliate&affiliate_id=' . $affiliate->affiliate_id );
                                    echo '<a href="' . esc_url( $affiliate_report_url ) . '">' . __( 'View Report', 'affiliate-wp' ) . '</a>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3"><?php _e( 'No affiliate registrations yet', 'affiliate-wp' ); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
<?php }
}

new AffWP_Metabox_Overview_Registrations;
