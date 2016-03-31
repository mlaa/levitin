<?php
/*
 * This file is just Sage's base.php with a standard post template plugged in it,
 * which serves as a placeholder for the magic that BuddyPress Global Search does
 * to inject fancy search results into this page.
 *
 * TODO: Replace everything until and including `<main>` with some call to a
 * `get_template_part()` or equivalent. DRY it up!
 */
?>
<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
	<?php get_template_part( 'templates/head' ); ?>
	<body <?php body_class(); ?>>
		<!--[if lt IE 9]>
			<div class="alert">
				<?php _e( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'cpwpst' ); ?>
			</div>
		<![endif]-->
		<?php
			do_action( 'get_header' );
			//get_template_part( 'templates/header' ); // TODO figure out why menu items don't show up in primary_navigation
		?>
		<header id="main-site-header" class="banner" role="banner">
			<div>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/assets/images/mla-commons-logo.png" alt="<?php bloginfo( 'name' ); ?>">
				</a>
				<nav role="navigation" class="primary">
					<div class="menu-inside-header-navigation-container">
						<ul id="menu-inside-header-navigation" class="nav">
							<li id="menu-item-243" class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-6 current_page_item menu-item-243"><a href="/groups/">Groups</a></li>
							<li id="menu-item-245" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-245"><a href="/members/">Members</a></li>
							<li id="menu-item-241" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-241"><a href="/sites/">Sites</a></li>
							<li id="menu-item-7567" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-7567"><a href="/core/">CORE</a></li>
							<li id="menu-item-1437" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1437"><a href="/publications/">MLA Publications</a></li>
							<li class="search"><a href="/?s">Search</a></li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		<div class="wrap" role="document">
			<div class="content">
				<main class="main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'templates/page', 'header' ); ?>
					<?php get_template_part( 'templates/content', 'page' ); ?>
				<?php endwhile; ?>

				</main><!-- /.main -->
			</div><!-- /.content -->
		</div><!-- /.wrap -->
		<?php
			get_template_part( 'templates/footer' );
			wp_footer();
		?>
	</body>
</html>
