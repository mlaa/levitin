<?php //if ( is_user_logged_in() ) : ?>

	<?php // TODO pending spec ?>

<?php //else: ?>

	<?php do_action( 'bp_before_directory_activity' ); ?>
	<?php do_action( 'bp_before_directory_activity_content' ); ?>
	<?php do_action( 'template_notices' ); ?>
	<?php do_action( 'bp_before_directory_activity_list' ); ?>

	<div class="recent-commons-activity">
		<h4>Recent Commons Activity</h4>
		<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&max=20' ) ) : ?>
			<?php while ( bp_activities() ) : bp_the_activity() ?>
				<?php levitin_activity_action() ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>

	<?php do_action( 'bp_after_directory_activity_list' ); ?>
	<?php do_action( 'bp_directory_activity_content' ); ?>
	<?php do_action( 'bp_after_directory_activity_content' ); ?>
	<?php do_action( 'bp_after_directory_activity' ); ?>

	<div class="widgets">
		<div class="video">
			<ul class="dashboard-top">
				<?php dynamic_sidebar('mla-dashboard-top'); ?>
			</ul>
		</div>

		<div class="columns">
			<ul class="dashboard-left">
				<?php dynamic_sidebar('mla-dashboard-left'); ?>
			</ul>

			<ul class="dashboard-right">
				<?php dynamic_sidebar('mla-dashboard-right'); ?>
			</ul>
		</div>
	</div>

<?php //endif; ?>
