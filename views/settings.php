<?php

global $feu;

$user = wp_get_current_user();
$update_status = null;
$user_avatar_enabled = $feu->is_user_avatar_enabled();

if (empty($user->ID)) {
	echo '<p>Sorry, you need to be logged in to view these settings.</p>';
	return;
}

if (!empty($_POST)) {
	$update_status = $feu->update_user_settings($user, $_POST);
}

if ($user_avatar_enabled) {
	$feu->prepare_user_avatar();
}

feu_header();

?>

<?php feu_box_(); ?>

	<h1>Settings</h1>
	
	<?php feu_form_message($update_status); ?>
	
	<div class="feu-form">
		
		<?php
			if ($user_avatar_enabled) {
				user_avatar_form($user);
			}
		?>
	
		<form action="<?php echo feu_get_url('settings'); ?>" method="post">
		
			<fieldset>
				
				<legend>Name</legend>
			
				<div>
				
					<label>Username</label>
					
					<div>
						<?php echo $user->user_login; ?>
					</div>
				
				</div>
				
				<div>
				
					<label for="first_name">First Name</label>
					
					<div>
						<input type="text" name="first_name" value="<?php echo esc_attr($user->first_name); ?>" />
					</div>
				
				</div>
				
				<div>
				
					<label for="last_name">Last Name</label>
					
					<div>
						<input type="text" name="last_name" value="<?php echo esc_attr($user->last_name); ?>" />
					</div>
				
				</div>
				
				<div>
				
					<label for="nickname">Nickname <span>(required)</span></label>
					
					<div>
						<input type="text" name="nickname" value="<?php echo esc_attr($user->nickname); ?>" />
					</div>
				
				</div>
				
				<div>
				
					<label for="display_name">Display Name</label>
					
					<div>
						<select name="display_name">
							<?php echo feu_get_display_names_options_html($user); ?>
						</select>
					</div>
				
				</div>
			
			</fieldset>
		
			<fieldset>
				
				<legend>Contact Info</legend>
				
				<div>
				
					<label for="email">Email</label>
					
					<div>
						<input type="text" name="email" value="<?php echo esc_attr($user->user_email); ?>" />
					</div>
				
				</div>
			
			</fieldset>
		
			<fieldset>
				
				<legend>Password</legend>
				
				<div>
				
					<label for="pass1">New Password</label>
					
					<div>
						<input type="password" name="pass1" value="" autocomplete="off" /> <span class="description">If you would like to change the password type a new one. Otherwise leave this blank.</span><br />
						<input type="password" name="pass2" value="" autocomplete="off" /> <span class="description">Type your new password again.</span>
					</div>
				
				</div>
			
			</fieldset>
			
			<?php if ($feu->get_display_custom_profile_settings()): ?>
			
				<?php do_action( 'show_user_profile', $user ); ?>
			
			<?php endif; ?>
			
			<div class="submit">
			
				<input type="hidden" name="user_id" value="<?php echo esc_attr($user->ID); ?>" />
				<input type="submit" name="submit" value="Update" />
			
			</div>
	
		</form>
	
	</div>
	
<?php _feu_box(); ?>

<?php feu_footer(); ?>