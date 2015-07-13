<?php

// Functions for the dashboard

// Make a new sidebar for tabbed widgets. This will hold
// "From the MLA," -> "News," "Sites," and "Resources."
function mla_dashboard_sidebars(){
	register_sidebar( array(
		'id'          => 'mla-dashboard-tabbed-sidebar',
		'name'        => 'Dashboard Tabbed Sidebar',
		'description' => 'This is the "From the MLA" sidebar, containing MLA news, sites, and resources.',
	));
}
add_action( 'widgets_init', 'mla_dashboard_sidebars', 9 );

/* A widget for displaying the logged-in user's avatar, name, affiliation,
 * and a few useful links to that user's pages.
 */
class MLA_BP_Profile_Area extends WP_Widget {
	function __construct() {
		parent::WP_Widget( false, $name = __( 'MLA Profile Area', 'tuileries' ) );
	}

	function widget($args, $instance) {
		global $bp;

		extract( $args );

		$link_title = ! empty( $instance['link_title'] );

		echo $before_widget;
		echo $before_title;

		if ( $link_title ) {
			$dir_link = trailingslashit( bp_get_root_domain() ) . trailingslashit( bp_get_blogs_root_slug() );
			$title = '<a href="' . $dir_link . '">' . $instance['title'] . '</a>';
		} else {
			$title = $instance['title'];
		}
		echo $title;
		echo $after_title;

		if ( empty( $instance['max_posts'] ) || !$instance['max_posts'] )
			$instance['max_posts'] = 10;

		// Load more items that we need, because many will be filtered out by privacy
		$real_max = $instance['max_posts'] * 10;
		$counter = 0;

		$query_string_a = empty( $instance['include_groupblog'] ) ? 'action=new_blog_post' : 'action=new_blog_post,new_groupblog_post';
		$query_string_b = '&max=' . $real_max . '&per_page=' . $real_max;

		if ( bp_has_activities( $query_string_a . $query_string_b ) ) : ?>

			<ul id="blog-post-list" class="activity-list item-list">

				<?php while ( bp_activities() ) : bp_the_activity(); ?>

					<?php if ( $counter >= $instance['max_posts'] ) break ?>

					<li>
						<div class="activity-content" style="margin: 0">
							<div class="activity-avatar">
								<?php bp_activity_avatar() ?>
							</div>

							<div class="activity-header">
								<?php bp_activity_action() ?>
							</div>

							<?php if ( bp_get_activity_content_body() ) : ?>

									<?php bp_activity_content_body() ?>

							<?php endif; ?>

						</div>
					</li>

					<?php $counter++ ?>

				<?php endwhile; ?>

			</ul>

		<p class="cac-more-link"><a href="<?php bp_blogs_directory_permalink(); ?>">More Blogs</a></p>

		<?php else : ?>
			<div id="message" class="info">
				<p><?php _e( 'Sorry, there were no blog posts found. Why not write one?', 'buddypress' ) ?></p>
			</div>
		<?php endif; ?>

		<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['max_posts']  = strip_tags( $new_instance['max_posts'] );
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['link_title'] = empty( $new_instance['link_title'] ) ? '0' : '1';
		$instance['include_groupblog'] = empty( $new_instance['include_groupblog'] ) ? '0' : '1';

		return $instance;
	}

	function form( $instance ) {
		$instance   = wp_parse_args( (array) $instance, array(
			'max_posts'  => 10,
			'title'      => __( 'Recent Blog Posts', 'cbox-theme' ),
			'link_title' => true,
		) );
		$max_posts  = strip_tags( $instance['max_posts'] );
		$title      = strip_tags( $instance['title'] );
		$link_title = (bool) $instance['link_title'];
		$include_groupblog = (bool) $instance['include_groupblog'];

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e('Title: ', 'cbox-theme'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 90%" /></label></p>

		<p><label for="<?php echo $this->get_field_name( 'link_title' ) ?>"><input type="checkbox" name="<?php echo $this->get_field_name( 'link_title' ) ?>" value="1" <?php checked( $link_title ) ?> /> <?php _e( 'Link widget title to Blogs directory', 'cbox-theme' ) ?></label></p>

		<p><label for="<?php echo $this->get_field_name( 'include_groupblog' ) ?>"><input type="checkbox" name="<?php echo $this->get_field_name( 'include_groupblog' ) ?>" value="1" <?php checked( $include_groupblog ) ?> /> <?php _e( 'Include groupblog posts', 'cbox-theme' ) ?></label></p>

		<p><label for="<?php echo $this->get_field_id( 'max_posts' ) ?>"><?php _e('Max posts to show:', 'buddypress'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_posts' ); ?>" name="<?php echo $this->get_field_name( 'max_posts' ); ?>" type="text" value="<?php echo esc_attr( $max_posts ); ?>" style="width: 30%" /></label></p>

	<?php
	}
}

/**
 * Register the above widget
 */
function mla_register_profile_widget()
{
	return register_widget( "MLA_BP_Profile_Area" );
}
add_action( 'widgets_init', 'mla_register_profile_widget' );
