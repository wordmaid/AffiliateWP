<?php

class AffWP_Meta_Box_Overview_Totals extends AffWP_Meta_Box_Base {

	/**
	 * The id of the meta box. Must be unique.
	 *
	 * @access  public
	 * @since   1.9
	 */
	public $meta_box_id = 'overview-totals';

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
	public $context = 'primary';

	/**
	 * Initialize
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		add_action( 'add_meta_box',              array( $this, 'add_meta_box' ) );
		add_action( 'affwp_overview_meta_boxes', array( $this, 'add_meta_box' ) );
		$this->meta_box_name = __( 'Totals', 'affiliate-wp' );
	}

	/**
	 * Displays the content of the metabox
	 *
	 * @return mixed content The metabox content
	 * @since  1.9
	 */
	public function content() { ?>

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

new AffWP_Meta_Box_Overview_Totals;
