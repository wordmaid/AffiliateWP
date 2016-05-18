<?php $active_tab = affwp_get_active_affiliate_area_tab(); ?>

<div id="affwp-affiliate-dashboard">

	<?php if ( 'pending' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is pending approval', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'inactive' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is not active', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'rejected' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account request has been rejected', 'affiliate-wp' ); ?></p>

	<?php endif; ?>

	<?php if ( 'active' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<?php do_action( 'affwp_affiliate_dashboard_top', affwp_get_affiliate_id() ); ?>

		<?php if ( ! empty( $_GET['affwp_notice'] ) && 'profile-updated' == $_GET['affwp_notice'] ) : ?>

			<p class="affwp-notice"><?php _e( 'Your affiliate profile has been updated', 'affiliate-wp' ); ?></p>

		<?php endif; ?>

		<?php do_action( 'affwp_affiliate_dashboard_notices', affwp_get_affiliate_id() ); ?>

		<ul id="affwp-affiliate-dashboard-tabs">
			<?php if ( affwp_affiliate_area_show_tab( 'urls' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'urls' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'urls' ) ); ?>"><?php _e( 'Affiliate URLs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'stats' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'stats' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'stats' ) ); ?>"><?php _e( 'Statistics', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'graphs' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'graphs' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'graphs' ) ); ?>"><?php _e( 'Graphs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'referrals' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'referrals' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'referrals' ) ); ?>"><?php _e( 'Referrals', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'visits' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'visits' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'visits' ) ); ?>"><?php _e( 'Visits', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'creatives' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'creatives' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'creatives' ) ); ?>"><?php _e( 'Creatives', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'settings' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'settings' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'settings' ) ); ?>"><?php _e( 'Settings', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>
			<?php do_action( 'affwp_affiliate_dashboard_tabs', affwp_get_affiliate_id(), $active_tab ); ?>
		</ul>

		<?php
		if ( ! empty( $active_tab ) && affwp_affiliate_area_show_tab( $active_tab ) ) :
			affiliate_wp()->templates->get_template_part( 'dashboard-tab', $active_tab );
		endif;
		?>

		<?php do_action( 'affwp_affiliate_dashboard_bottom', affwp_get_affiliate_id() ); ?>

	<?php endif; ?>

</div>
