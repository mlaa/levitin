<?php

use MLA\Levitin\Config;
use MLA\Levitin\Wrapper;

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
			get_template_part( 'templates/header' );
		?>
		<div class="wrap" role="document">
			<div class="content">
					<main class="main" role="main">

					<!-- required by Remodal plugin so that we can make modal dialogs
							 with this content -->
					<div class="remodal-bg">

						<?php do_action( 'cacap_before_content' ) ?>

						<div id="cacap-content">
							<?php if ( bp_is_user_profile_edit() ) : ?>

								<div class="remodal" data-remodal-id="modal">
									<h1>Welcome to Portfolios</h1>
									<p>The Portfolios profile system allows you to enter information about yourself and your career so that you might better connect with other users of the <em>Commons</em>. Read more about Portfolios on the <a href="http://howtouse.commons.mla.org/category/how-to-do-things-with-mla-commons/portfolios">help blog</a>.</p>
									<br>
									<!-- <a class="remodal-cancel" href="#">Cancel</a> -->
									<a class="remodal-confirm" href="#">Get Started</a>
								</div>

								<form action="" method="post" id="cacap-edit-form">
									<div id="cacap-header">
										<?php bp_get_template_part( 'cacap/header-edit' ) ?>
									</div>

									<div id="cacap-edit">
										<?php bp_get_template_part( 'cacap/body-edit' ) ?>
									</div>
								</form>
							<?php else : ?>
								<div id="cacap-header">
									<?php bp_get_template_part( 'cacap/header' ) ?>
								</div>

								<div id="cacap-body">
									<?php bp_get_template_part( 'cacap/body' ) ?>
								</div>
							<?php endif ?>
						</div>

					</div> <!-- end .remodal-bg -->
				</main><!-- /.main -->
			</div><!-- /.content -->
		</div><!-- /.wrap -->
		<?php
			get_template_part( 'templates/footer' );
			wp_footer();
		?>
	</body>
</html>
