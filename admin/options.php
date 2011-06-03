<div class="wrap">

	<h2>Front-End Users</h2>
	
	<form method="post" action="options.php">
		
		<?php settings_fields('front-end-users'); ?>
		<?php do_settings_sections('front-end-users'); ?>
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		
	</form>
	
</div>