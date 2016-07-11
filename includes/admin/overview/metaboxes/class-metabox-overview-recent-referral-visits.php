<?php

class AffWP_Meta_Box_Overview_Recent_Referral_Visits extends AffWP_Meta_Box_Base {

	/**
	 * The id of the meta box. Must be unique.
	 *
	 * @access  public
	 * @since   1.9
	 */
	public $meta_box_id = 'overview-recent-referral-visits';

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
	public $context = 'tertiary';

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

		$this->meta_box_name = __( 'Recent Referral Visits', 'affiliate-wp' );
	}

	/**
	 * Displays the content of the metabox
	 *
	 * @return mixed content  The metabox content
	 * @since  1.9
	 */
	public function content() {

	$visits = affiliate_wp()->visits->get_visits(
		apply_filters( 'affwp_overview_recent_referral_visits',
			array( 'number' => 8 )
		)
	); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
					<th><?php _e( 'URL',       'affiliate-wp' ); ?></th>
					<th><?php _e( 'Converted', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
				<?php if( $visits ) : ?>
					<?php foreach( $visits as $visit ) : ?>
						<tr>
							<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $visit->affiliate_id ); ?></td>
							<td><a href="<?php echo esc_url( $visit->url ); ?>"><?php echo esc_html( $visit->url ); ?></a></td>
							<td>
								<?php $converted = ! empty( $visit->referral_id ) ? 'yes' : 'no'; ?>
								<span class="visit-converted <?php echo $converted; ?>"><i></i></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="3"><?php _e( 'No referral visits recorded yet', 'affiliate-wp' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>

		</table>
	<?php }
}

new AffWP_Meta_Box_Overview_Recent_Referral_Visits;
