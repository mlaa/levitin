<?php

/**
 * Professional Interests  About
 * Recent Commons Activity Education
 * Commons Groups          Publications
 * Commons Sites           Projects
 *                         Work Shared in CORE
 *                         Upcoming Talks and Conferences
 *                         Memberships
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_before_profile_loop_content' ); ?>

<div class="left">
	<div class="academic-interests">
		<h4>Academic Interests</h4>
		<?php bp_member_profile_data( 'field=Academic Interests' ) ?>
	</div>
	<div class="recent-commons-activity">
		<h4>Recent Commons Activity</h4>
		<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>
			<?php while ( bp_activities() ) : bp_the_activity(); ?>
				<?php bp_activity_action(); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
		<?php endif; ?>
	</div>
	<div class="commons-groups">
		<h4>Commons Groups</h4>
		<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>
			<ul>
			<?php while ( bp_groups() ) : bp_the_group(); ?>
				<li>
					<a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a>
				</li>
			<?php endwhile; ?>
			</ul>
		<?php else: ?>
			<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
		<?php endif; ?>
	</div>
	<div class="commons-sites">
		<h4>Commons Sites</h4>
		<?php if ( bp_has_blogs( bp_ajax_querystring( 'blogs' ) ) ) : ?>
			<ul>
			<?php while ( bp_blogs() ) : bp_the_blog(); ?>
				<li>
					<a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_name(); ?></a>
				</li>
			<?php endwhile; ?>
			</ul>
		<?php else: ?>
			<p><?php _e( 'Sorry, there were no sites found.', 'buddypress' ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="right">
	<div class="about">
		<h4>About</h4>
		<?php bp_member_profile_data( 'field=About' ) ?>
	</div>
	<div class="education">
		<h4>Education</h4>
		<?php bp_member_profile_data( 'field=Education' ) ?>
	</div>
	<div class="publications">
		<h4>Publications</h4>
		<?php bp_member_profile_data( 'field=Publications' ) ?>
	</div>
	<div class="projects">
		<h4>Projects</h4>
		<?php bp_member_profile_data( 'field=Projects' ) ?>
	</div>
	<div class="work-shared-in-core">
		<h4>Work Shared in CORE</h4>
		<p>TODO</p>
	</div>
	<div class="upcoming-talks-and-conferences">
		<h4>Upcoming Talks and Conferences</h4>
		<?php bp_member_profile_data( 'field=Upcoming Talks and Conferences' ) ?>
	</div>
	<div class="memberships">
		<h4>Memberships</h4>
		<?php bp_member_profile_data( 'field=Memberships' ) ?>
	</div>
</div>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_after_profile_loop_content' ); ?>
