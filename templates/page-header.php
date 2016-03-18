<?php use MLA\Levitin\Titles; ?>

<?php if ( Titles\title() ): ?>
	<div class="page-header">
		<h1>
			<?php echo Titles\title(); ?>
		</h1>
	</div>
	<?php get_search_form( true ); ?>
<?php endif; ?>
