<?php

// Running additional code after a user has updated their profile

add_action('feu_after_update_user', 'after_update_user', 1, 2);
function after_update_user($user, $post_data) {
	// Add code here to process any other fields, etc
}

// Adding additional views

/*
	Settings for a view:
		title	(string)	Title displayed in the menu and at the top of the page
		in_menu	(boolean)	Whether the view is shown in the menu (default: true)
		url		(string)	The URL path for the view (e.g. 'settings' will cause the view to be rendered at site_url().'/user/settings/'
		file	(string)	Name of the view's file (e.g. 'settings' will render [views_directory]/settings.php). If
								this is omitted, the view's key will be used here
		action	(string)	The action value used in the WP rewrite rule; no need to customize this, as it's mainly only relevant
								for the 'index' action
		items	(array)		A list of view keys; these views will be listed in a dropdown submenu below the view title
								in the menu.
*/
add_filter('feu_settings', 'change_feu_settings');
function change_feu_settings($settings) {
	$settings['views'] = array(
		'index' => array(
				'title' => 'Home'
			),
		'settings' => array(
				'title' => 'Settings'
			)
	);
	return $settings;
}

// Modify the HTML of the FEU menu (which lists FEU views that have 'in_menu' => true)

add_filter('feu_menu', 'change_feu_menu');
function change_feu_menu($html) {
	return '';
}

// Make the layout of wp-login.php similar to the rest of the site.  To do this, create files
// like the ones listed below, which can then be included both in header.php/footer.php and here.

global $theme_path;
$theme_path = get_theme_root().'/'.get_template().'/';

function feu_login_head_element() {
	global $theme_path;
	require $theme_path.'includes/header-title.php';
	require $theme_path.'includes/header-meta.php';
	require $theme_path.'includes/header-css.php';
	require $theme_path.'includes/header-js.php';
	wp_head();
}

function feu_login_header() {
	global $theme_path;
	require $theme_path.'includes/header-content.php';
}

function feu_login_footer() {
	global $theme_path;
	require $theme_path.'includes/footer-content.php';
	wp_footer();
	require $theme_path.'includes/footer-js.php';
}

?>