<?php


class AffWP_Metabox_Reports_Referrals extends AffWP_Metabox_Base {

    /**
    * The id of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.9
    */
    public $meta_box_id = 'reports-referrals-earnings';

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
        $this->meta_box_name = __( 'Referrals and Earnings', 'affiliate-wp' );
    }

    /**
     * Displays an overview of earnings in 3 different tables
     *
     * @return mixed content An overview of referrals and earnings
     * @since  1.9
     */
    public function content() {

        $earnings_today = affiliate_wp()->referrals->paid_earnings( 'today' );
    ?>
        <h2>
            <?php echo $earnings_today . __(' earned today.', 'affiliate-wp' ); ?>
        </h2>
        <table class="affwp_table">
            <thead>
                <tr>
                    <th><?php _e( 'Paid Earnings', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Paid Earnings This Month', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Paid Earnings Today', 'affiliate-wp' ); ?></th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
                    <td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
                    <td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
                </tr>
            </tbody>
        </table>

        <table class="affwp_table">
            <thead>
                <tr>
                    <th><?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Unpaid Referrals This Month', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Unpaid Referrals Today', 'affiliate-wp' ); ?></th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
                    <td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
                    <td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
                </tr>
            </tbody>
        </table>

        <table class="affwp_table">
            <thead>
                <tr>
                    <th><?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Unpaid Earnings Today', 'affiliate-wp' ); ?></th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
                    <td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
                    <td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
                </tr>
            </tbody>
        </table>

    <?php }
}

new AffWP_Metabox_Reports_Referrals;
