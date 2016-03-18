<header id="main-site-header" class="banner" role="banner">
	<div>
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php bloginfo('template_directory'); ?>/assets/images/mla-commons-logo.png" alt="<?php bloginfo( 'name' ); ?>">
		</a>
		<nav role="navigation" class="primary">
			<?php
			if ( has_nav_menu( 'primary_navigation' ) ) :
				wp_nav_menu([
					'theme_location' => 'primary_navigation',
					'menu_class' => 'nav'
				]);
			endif;
			?>
		</nav>
		<?php // get_search_form( true ); ?>
	</div>
</header>
