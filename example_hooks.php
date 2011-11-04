<?php

// Modifying views or making custom views
// The views that are used by default are in wp-content/plugins/front-end-users/views/, but if you want to modify the views,
// it's recommended that you copy them into wp-content/themes/my-theme/views/ and modify them there. This will allow you to
// update this plugin without overwriting the views.

add_filter('feu_settings', 'set_custom_feu_views_directory');
function set_custom_feu_views_directory($settings) {
	$settings['views_directory'] = get_theme_root().'/'.get_template().'/views/';
	return $settings;
}


// Setting up custom views
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

add_filter('feu_settings', 'set_custom_feu_views');
function set_custom_feu_views($settings) {
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


// Setting up custom views and having some of the views ('my-first-settings-page' and 'my-second-settings-page') show up in the
// FEU menu as subitems for the another view ('settings'):

add_filter('feu_settings', 'set_custom_feu_views');
function set_custom_feu_views($settings) {
	$settings['views'] = array(
		'index' => array(
				'title' => 'Home'
			),
		'settings' => array(
				'title' => 'Settings',
				'items' => array('my-first-settings-page', 'my-second-settings-page')
			),
		// These will appear as subitems for the 'settings' menu item because they're listed in as the 'items' of 'settings'.
		// You'd need to create my-first-settings-page.php and my-second-settings-page.php in the views directory.
		'my-first-settings-page' => array(
				'title' => 'My First Settings Page'
			),
		'my-second-settings-page' => array(
				'title' => 'My Second Settings Page'
			)
	);
	return $settings;
}


// To display the FEU menu (which isn't displayed by default), modify views/header.php to include feu_menu(), which displays
// the menu:

if (feu_is_logged_in()) {
	feu_menu();
}


// Modify the HTML of the FEU menu (which lists FEU views that have 'in_menu' => true)

add_filter('feu_menu', 'change_feu_menu');
function change_feu_menu($html) {
	return '';
}


// Running additional code after a user has updated their profile

add_action('feu_after_update_user', 'after_update_user', 1, 2);
function after_update_user($user, $post_data) {
	// Add code here to process any other fields, etc
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