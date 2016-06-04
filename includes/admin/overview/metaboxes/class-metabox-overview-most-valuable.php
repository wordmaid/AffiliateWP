<?php

class AffWP_Metabox_Overview_Most_Valuable extends AffWP_Metabox_Base {

	/**
	* The id of the meta box. Must be unique.
	*
	* @access  public
	* @since   1.9
	*/
	public $meta_box_id = 'overview-most-valuable';

	/**
	* The name of the meta box. Must be unique.
	*
	* @access  public
	* @since   1.9
	*/
	public $meta_box_name;

	/**
	 * The position in which the meta box will be loaded
	 * Either 'normal', 'side', or 'advanced'.
	 *
	 * @access  public
	 * @var     $context
	 * @since   1.9
	 */
	public $context = 'side';

	/**
	 * Initialize
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		add_action( 'add_meta_box',         array( $this, 'add_meta_box' ) );
		add_action( 'affwp_overview_center', array( $this, 'add_meta_box' ) );

		$this->meta_box_name = __( 'Most Valuable Affiliates', 'affiliate-wp' );
	}

	/**
	 * Displays the content of the metabox
	 *
	 * @return mixed content  The metabox content
	 * @since  1.9
	 */
	public function content() {

		$affiliates = affiliate_wp()->affiliates->get_affiliates(
			apply_filters( 'affwp_overview_most_valuable_affiliates',
				array(
					'number'  => 5,
					'orderby' => 'earnings',
					'order'   => 'DESC'
				)
			)
		); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Earnings', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Referrals', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
			<?php if( $affiliates ) : ?>
				<?php foreach( $affiliates as $affiliate  ) : ?>
					<tr>
						<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
						<td><?php echo affwp_currency_filter( $affiliate->earnings ); ?></td>
						<td><?php echo absint( $affiliate->referrals ); ?></td>
						<td><?php echo absint( $affiliate->visits ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php _e( 'No registered affiliates', 'affiliate-wp' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>

		</table>
	<?php }
}

new AffWP_Metabox_Overview_Most_Valuable;
