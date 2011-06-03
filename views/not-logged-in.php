<?php feu_header(); ?>

<?php feu_box_(); ?>

	<h1>Not signed in</h1>

	<p>
		To see this page, please <a href="<?php echo wp_login_url(site_url($_SERVER['REQUEST_URI'])); ?>">sign in</a>.
	</p>
	
<?php _feu_box(); ?>

<?php feu_footer(); ?>