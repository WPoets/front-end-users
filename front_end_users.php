<?php
/*
Plugin Name: Front End Users
Plugin URI: http://wordpress.org/extend/plugins/front-end-users/
Description: Hides the WordPress admin section from specified user roles, allows users to edit their settings from the front-end, and allows for customization of user-specific pages on the front-end.
Author: Tom Benner
Version: 1.0
Author URI: 
*/

require_once dirname(__FILE__).'/functions.php';
require_once dirname(__FILE__).'/lib/front_end_users.php';

global $feu;
$feu = new FrontEndUsers();

register_activation_hook(__FILE__, array('FrontEndUsers', 'activate_plugin'));

// Init
add_action('init', array($feu, 'init'));
add_action('admin_init', array($feu, 'admin_init'));

// Admin options page
add_action('admin_menu', array($feu, 'add_options_page'));

// Routing
add_filter('rewrite_rules_array', array($feu, 'add_rewrite_rules'));
add_filter('query_vars', array($feu, 'add_query_vars'));
add_filter('template_redirect', array($feu, 'template_redirect'));

// Restricting non-admin users
add_action('admin_init', array($feu, 'restrict_admin_access'), 1);
add_filter('admin_url', array($feu, 'rewrite_admin_url'), 1, 1);
add_filter('login_url', array($feu, 'rewrite_login_url'), 1, 1);

// Reskinning wp-login.php
add_action('login_head', array($feu, 'login_head'));
add_filter('login_message', array($feu, 'login_message'));
add_action('login_footer', array($feu, 'login_footer'));

?>