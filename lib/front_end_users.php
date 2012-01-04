<?php

class FrontEndUsers {

	public $plugin_file_path = '';
	public $plugin_url = '';
	public $settings = array();
	private $views = array();
	private $action_key = 'feu_action';
	private $administrator_role_key = 'administrator';
	private $user_avatar_enabled = false;
	private $initialized = false;
	private $debug = false;
	
	static function activate_plugin() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		add_option('front_end_users_url_path', 'profile');
		add_option('front_end_users_display_custom_profile_settings', 0);
	}
	
	public function init() {

		$this->plugin_file_path = preg_replace('/lib\/?$/', '', dirname(__FILE__));
		$this->plugin_url = site_url('/').str_replace(ABSPATH, '', $this->plugin_file_path);
		
		/*
			Settings for a view:
				title	(string)	Title displayed in the menu and at the top of the page
				in_menu	(bool)		Whether the view is shown in the menu
				url		(string)	The URL path for the view (e.g. 'settings' will cause the view to be rendered at site_url().'/user/settings/'
				file	(string)	Name of the view's file (e.g. 'settings' will render [views_directory]/settings.php). If
										this is omitted, the view's key will be used here
				action	(string)	The action value used in the WP rewrite rule; no need to customize this, as it's mainly only relevant
										for the 'index' action
				items	(array)		A list of view keys; these views will be listed in a dropdown submenu below the view title
										in the menu.
		*/
		
		$default_views = array(
			'index' => array(
					'title' => 'Settings',
					'file' => 'settings'
				),
			'not-logged-in' => array(
					'title' => 'Please sign in',
					'in_menu' => false
				)
		);
		
		$options = get_option('front_end_users_options');
		$defaults = array(
			'url_path' => 'profile',
			'roles_with_admin_access' => array($this->administrator_role_key),
			'display_custom_profile_settings' => 0
		);
		
		if (empty($options)) {
			$options = $defaults;
			update_option('front_end_users_options', $options);
		} else {
			$options = array_merge($defaults, $options);
		}
		
		$default_settings = array(
			'404_include_path' => get_theme_root().'/'.get_template().'/404.php',
			'display_custom_profile_settings' => $options['display_custom_profile_settings'],
			'roles_with_admin_access' => $options['roles_with_admin_access'],
			'url_path' => $options['url_path'],
			'views' => $default_views,
			'views_directory' => $this->plugin_file_path.'views/'
		);

		$this->settings = apply_filters('feu_settings', $default_settings);
		$this->init_views();
		$this->register_css();
		$this->set_user_avatar_enabled();
		if (!$this->has_admin_access()) {
			$this->disable_wp_admin_bar();
		}
		$this->initialized = true;
		
	}
	
	public function admin_init() {
		register_setting('front-end-users', 'front_end_users_options', array($this, 'validate_options'));
		add_settings_section('front-end-users-main', 'Settings', array($this, 'settings_text'), 'front-end-users');
		add_settings_field('roles_with_admin_access', 'Roles with Admin Access', array($this, 'settings_input_roles_with_admin_access'), 'front-end-users', 'front-end-users-main');
		add_settings_field('url_path', 'URL Base', array($this, 'settings_input_url_path'), 'front-end-users', 'front-end-users-main');
		add_settings_field('display_custom_profile_settings', 'Display Custom Profile Settings', array($this, 'settings_input_display_custom_profile_settings'), 'front-end-users', 'front-end-users-main');
	}
	
	private function init_views() {
	
		$this->views = $this->settings['views'];
		
		// A view with key 'not-logged-in' needs to be available to display to the user if they aren't
		// logged in and try to access a feu page
		if (empty($this->views['not-logged-in'])) {
			$this->views['not-logged-in'] = array(
				'title' => 'Please sign in',
				'in_menu' => false
			);
		}
		
		foreach($this->views as $key => $view) {
			$view['key'] = $key;
			if (empty($view['action'])) {
				$view['action'] = $key;
			}
			if (empty($view['file'])) {
				$view['file'] = $key;
			}
			if (empty($view['url'])) {
				// The index view should have an empty url path by default
				$view['url'] = $key == 'index' ? '' : $key;
			}
			$this->views[$key] = $view;
		}

	}
	
	public function flush_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
	private function register_css() {
		$css_url = $this->plugin_url.'css/feu.css';
		$css_url = apply_filters('feu_css_url', $css_url);
		wp_deregister_style('feu');
		wp_register_style('feu', $css_url);
	}
	
	public function is_user_avatar_enabled() {
		return $this->user_avatar_enabled;
	}
	
	public function set_user_avatar_enabled($boolean=null) {
		if ($boolean === null) {
			$active_plugins = get_option('active_plugins', array());
			$plugin_path = 'user-avatar/user-avatar.php';
			if (in_array($plugin_path, (array)$active_plugins )) {
				$this->user_avatar_enabled = true;
			}
		} else {
			$this->user_avatar_enabled = $boolean;
		}
	}
	
	private function disable_wp_admin_bar() {
		remove_action('init','wp_admin_bar_init');
		remove_filter('init','wp_admin_bar_init');
		remove_action('wp_head','wp_admin_bar_render',1000);
		remove_filter('wp_head','wp_admin_bar_render',1000);
		remove_action('wp_footer','wp_admin_bar_render',1000);
		remove_filter('wp_footer','wp_admin_bar_render',1000);
		remove_action('admin_head','wp_admin_bar_render',1000);
		remove_filter('admin_head','wp_admin_bar_render',1000);
		remove_action('admin_footer','wp_admin_bar_render',1000);
		remove_filter('admin_footer','wp_admin_bar_render',1000);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_me_separator',10);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_my_account_menu',20);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_my_blogs_menu',30);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_blog_separator',40);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_bloginfo_menu',50);
		remove_action('wp_before_admin_bar_render','wp_admin_bar_edit_menu',100);
		remove_action('wp_head','wp_admin_bar_css');
		remove_action('wp_head','wp_admin_bar_dev_css');
		remove_action('wp_head','wp_admin_bar_rtl_css');
		remove_action('wp_head','wp_admin_bar_rtl_dev_css');
		remove_action('admin_head','wp_admin_bar_css');
		remove_action('admin_head','wp_admin_bar_dev_css');
		remove_action('admin_head','wp_admin_bar_rtl_css');
		remove_action('admin_head','wp_admin_bar_rtl_dev_css');
		remove_action('wp_footer','wp_admin_bar_js');
		remove_action('wp_footer','wp_admin_bar_dev_js');
		remove_action('admin_footer','wp_admin_bar_js');
		remove_action('admin_footer','wp_admin_bar_dev_js');
		remove_action('wp_ajax_adminbar_render','wp_admin_bar_ajax_render');
		remove_action('personal_options',' _admin_bar_preferences');
		remove_filter('personal_options',' _admin_bar_preferences');
		remove_action('personal_options',' _get_admin_bar_preferences');
		remove_filter('personal_options',' _get_admin_bar_preferences');
		remove_filter('locale','wp_admin_bar_lang');
		add_filter('show_admin_bar','__return_false');
		wp_deregister_script('admin-bar');
		wp_deregister_style('admin-bar');
	}

	// Routing functions

	public function add_rewrite_rules($rules) {
		global $wp_rewrite;
		$wp_rewrite->add_rewrite_tag('%'.$this->action_key.'%', '(.+)', $this->action_key.'=');
		$action_structure = $wp_rewrite->root.$this->settings['url_path'].'/%'.$this->action_key.'%/';
		$new_rules = $wp_rewrite->generate_rewrite_rules($action_structure);
		$new_rules[$this->settings['url_path'].'$'] = 'index.php?'.$this->action_key.'=index';
		return array_merge($new_rules, $rules);
	}
	
	public function add_query_vars($vars) {
		$vars[] = $this->action_key;
		return $vars;
	}
	
	public function get_action() {
		global $wp_query;
		return $wp_query->get($this->action_key);
	}
	
	public function template_redirect() {
		global $wp_query;
		$action = $this->get_action();
		if ($action) {
			$wp_query->is_home = false;
			if (!$this->is_logged_in()) {
				$this->render_page('not-logged-in');
			} else {
				$view_key = null;
				foreach($this->views as $view) {
					if ($view['action'] == $action) {
						$view_key = $view['key'];
					}
				}
				$this->render_page($view_key);
			}
		}
	}
	
	// View-related functions
	
	private function render_page($view_key) {
		global $feu_current_view;
		$view = $this->get_view($view_key);
		if (!empty($view)) {
			$feu_current_view = $view;
			wp_enqueue_style('feu');
			do_action('feu_before_view', $view);
			$this->render_view($view);
			do_action('feu_after_view', $view);
		} else {
			$this->render_404();
		}
		die();
	}
	
	public function render_view($view_or_view_key) {
		$view = $this->get_view($view_or_view_key);
		if (!empty($view)) {
			$view_file_path = $this->get_file_path($view['file']);
			if (file_exists($view_file_path)) {
				include $view_file_path;
			} else {
				$this->debug('View file ("'.$view_path_path.'") not found');
			}
		} else {
			$this->debug('View not found: <br />'.print_r($view_or_view_key, true));
		}
	}
	
	private function get_file_path($file_name) {
		return $this->settings['views_directory'].$file_name.'.php';
	}
	
	private function get_view($view_or_view_key) {
		$view = null;
		if (is_string($view_or_view_key)) {
			if (!empty($this->views[$view_or_view_key])) {
				$view = $this->views[$view_or_view_key];
			}
		} else if (is_array($view_or_view_key)) {
			$view = $view_or_view_key;
		}
		return $view;
	}
	
	public function get_view_url($view_or_view_key=null) {
		if ($view_or_view_key === null) {
			$view_or_view_key = 'index';
		}
		$view = $this->get_view($view_or_view_key);
		if (empty($view)) {
			return false;
		}
		return site_url().'/'.$this->settings['url_path'].'/'.$view['url'];
	}
	
	// User status-related functions
	
	public function is_logged_in() {
		return is_user_logged_in();
	}
	
	public function has_admin_access() {
		global $current_user;
		if (!function_exists('get_currentuserinfo')) {
			require_once ABSPATH.WPINC.'/pluggable.php';
		}
		get_currentuserinfo();
		if ($current_user->ID == 0 || empty($current_user->roles)) {
			return false;
		}
		if (in_array($this->administrator_role_key, $current_user->roles)) {
			return true;
		}
		if (count(array_intersect($current_user->roles, $this->settings['roles_with_admin_access'])) == 0) {
			return false;
		}
		return true;
	}
	
	public function restrict_admin_access() {
		if (is_admin()) {
			$valid_admin_ajax_actions = array('user_avatar_add_photo');
			if ($_SERVER['SCRIPT_NAME'] == '/wp-admin/admin-ajax.php' &&
				isset($_GET['action']) && in_array($_GET['action'], $valid_admin_ajax_actions)) {
					return true;
			}
			if (!$this->is_logged_in()) {
				$this->render_page('not-logged-in');
			} else if (!$this->has_admin_access()) {
				$this->render_404();
			}
		}
	}

	public function rewrite_admin_url($url) {
		// This may be called before the init action occurs, so we need to explicitly call init() in that case
		if (!$this->initialized) {
			$this->init();
		}
		// Allow the following functions to access the true admin URL:
		// - check_admin_referer() - Whitelisting this prevents nonce verification failures during logouts
		// - wp_notify_postauthor() - Let notification emails to post authors use the admin URL
		// - wp_notify_moderator() - Let notification emails to moderators use the admin URL
		$backtrace = debug_backtrace();
		if (!empty($backtrace) && is_array($backtrace)) {
			$allowed_functions = array('check_admin_referer', 'wp_notify_postauthor', 'wp_notify_moderator');
			foreach($backtrace as $call) {
				if (!empty($call['function']) && in_array($call['function'], $allowed_functions)) {
					return $url;
				}
			}
		}
		global $current_user;
		get_currentuserinfo();
		$script_name = $_SERVER['SCRIPT_NAME'];
		if ($current_user->ID == 0) {
			return site_url().'/';
		}
		if (!$this->has_admin_access()) {
			// Allow admin AJAX to be used
			if ($url == get_bloginfo('wpurl').'/wp-admin/admin-ajax.php') {
				return $url;
			// Allow links to moderate comments
			} else if (preg_match('/\/wp-admin\/comment\.php\?action/', $url)) {
				return $url;
			} else if (preg_match('/\/wp-admin\/edit-comments\.php\?comment_status/', $url)) {
				return $url;
			}
			return $this->get_view_url();
		}
		if ($this->has_admin_access()) {
			if (preg_match('/\/wp-admin\/profile\.php/', $script_name)) {
				return $url;
			}
		}
		if (preg_match('/\/wp-admin\/profile\.php$/', $url)) {
			return feu_get_url();
		}
		return $url;
	}
	
	// If a user doesn't have admin access, prevent wp_redirect() from redirecting to the admin section
	public function rewrite_wp_redirect($url) {
		// This may be called before the init action occurs, so we need to explicitly call init() in that case
		if (!$this->initialized) {
			$this->init();
		}
		if (!$this->has_admin_access()) {
			if (preg_match('/\/wp-admin\//', $url)) {
				if ($this->is_logged_in()) {
					return feu_get_url();
				} else {
					return site_url();
				}
			}
		}
		return $url;
	}

	public function rewrite_login_url($url) {
		if (strstr($url, '%2Fwp-admin%2F')) {
			if (!$this->is_logged_in()) {
				if (!preg_match('/\/wp-login.*?redirect_to=/', $url)) {
					$url = wp_login_url($url);
				}
				wp_redirect($url);
				die();
			} else if (!$this->has_admin_access()) {
				$this->render_404();
			}
		}
		return $url;
	}
	
	public function user_header_links() {
		$links = $this->user_header_links_array();
		$html = '<ul>';
		foreach($links as $link) {
			$html .= '<li><a href="'.$link[2].'" title="'.esc_attr($link[1]).'" class="user-header-link-'.$links[0].'">'.$link[1].'</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}
	
	public function user_header_links_array() {
		
		$user = wp_get_current_user();
		$user_id = empty($user->ID) ? null : $user->ID;
		
		$links = array();
		
		if (empty($user_id)) {
		
			$links[] = array('sign_in', 'Sign in', wp_login_url());
			$links[] = array('register', 'Register', get_bloginfo('wpurl').'/wp-login.php?action=register', );
		
		} else {
		
			$user = wp_get_current_user();
			$profile_link_title = empty($user->first_name) ? $user->display_name : $user->first_name;
			$profile_link_title = apply_filters('feu_profile_link_title', $profile_link_title, $user);
			$dashboard_link_title = apply_filters('feu_dashboard_link_title', 'Dashboard', $user);
			
			$links[] = array('profile', $profile_link_title, feu_get_url());
			if (feu_has_admin_access()) {
				$links[] = array('dashboard', $dashboard_link_title, get_bloginfo('wpurl').'/wp-admin/');
			}
			$links[] = array('sign_out', 'Sign out', wp_logout_url());
	
		}
		
		return $links;
	
	}
	
	private function call_function_if_exists($function_name) {
		if (function_exists($function_name)) {
			call_user_func($function_name);
		}
	}
	
	// Reskinning wp-login.php
	
	public function login_head() {
		// If the user is already logged in, redirect to the homepage instead of showing the login screen
		if (is_user_logged_in()) {
			wp_redirect(site_url(), 302);
		}
		$this->call_function_if_exists('feu_login_head_element');
	}	
		
	public function login_message($message) {
		$this->call_function_if_exists('feu_login_header');
		return $message;
	}
	
	
	public function login_footer() {
		$this->call_function_if_exists('feu_login_footer');
	}
	
	// View rendering-related functions
	
	private function render_404() {
		include $this->settings['404_include_path'];
		die();
	}
	
	public function render_header() {
		include $this->get_file_path('header');
	}
	
	public function render_footer() {
		include $this->get_file_path('footer');
	}
	
	public function get_user_menu() {
		global $feu_current_view;
		
		$html = '<ul class="feu-menu">';
		
		$views = $this->views;
		$subview_keys = array();
		
		foreach($views as $view_key => $view) {
		
			if (isset($view_key) && in_array($view_key, $subview_keys)) {
				continue;
			}
			
			if (!isset($view['in_menu']) || $view['in_menu']) {
			
				if (!empty($view['items'])) {
				
					$html .= '<li class="submenu">';
					$html .= '<span class="submenu-name">'.$view['title'].'</span>';
					$html .= '<ul>';
					
					foreach($view['items'] as $subview_key) {
					
						$subview_keys[] = $subview_key;
						$subview = $this->views[$subview_key];
						$active = $feu_current_view['key'] == $view_key;
						
						$html .= '<li'.($active ? ' class="active"' : '').'>';
						if ($active) {
							$html .= $subview['title'];
						} else {
							$html .= '<a href="'.$this->get_view_url($subview['key']).'">'.$subview['title'].'</a>';
						}
						$html .= '</li>';
						
						unset($views[$subview_key]);
						
					}
					
					$html .= '</ul></li>';
					
				} else {
				
					$active = $feu_current_view['key'] == $view_key;
					
					$html .= '<li'.($active ? ' class="active"' : '').'>';
					if ($active) {
						$html .= $view['title'];
					} else {
						$html .= '<a href="'.$this->get_view_url($view['key']).'">'.$view['title'].'</a>';
					}
					$html .= '</li>';
					
				}
				
			}
		}
		$html .= '</ul>';
		
		$html = apply_filters('feu_menu', $html, $this->views);
		
		return $html;
	}
	
	// Profile settings-related functions
	
	public function update_user_settings(&$user, $post) {
	
		if (empty($post)) {
			return null;
		}

		$user_id = $user->ID;
		
		$update_status = null;
		
		$required_fields = array(
			'first_name',
			'last_name',
			'nickname',
			'display_name',
			'email',
			'pass1',
			'pass2',
			'user_id'
		);
		
		// Verify that all of the required fields are present
		$has_required_fields = true;
		
		foreach($required_fields as $field) {
			if (!isset($post[$field])) {
				$has_required_fields = false;
				break;
			}
		}
		
		// If everything looks valid, use WP's edit_user() (which handles POST data behind the scenes) to update the user's data.
		if ($has_required_fields && $user_id == $post['user_id']) {
		
			require_once ABSPATH.'wp-admin/includes/admin.php';
			
			do_action('feu_before_update_user', $post);
			
			$errors = edit_user($user_id);
			
			// Update the user object for use in the form or in the feu_after_update_user action
			$user = wp_set_current_user($user_id);
			
			if (!is_wp_error($errors)) {
			
				do_action('feu_after_update_user', $user, $post);
				if ($this->get_display_custom_profile_settings()) {
					do_action('personal_options_update', $user_id);
				}
				$redirect_url = feu_get_url('settings');
				$redirect_url = apply_filters('feu_settings_update_url', $redirect_url);
				$update_url_params = array('updated' => 'true');
				$update_url_params = apply_filters('feu_settings_update_url_params', $update_url_params, $user);
				$update_url_params = http_build_query($update_url_params);
				if ($update_url_params) {
					$update_url_params = '?'.$update_url_params;
				}
				wp_redirect($redirect_url.$update_url_params);
				die();
				
			} else {
			
				$update_status = array(0, $errors);
				
			}
		
		}
		
		return $update_status;
	
	}
	
	public function get_display_names_options_html($user) {
	
		$display_names = array();
		$display_names['display_username'] = $user->user_login;
		$display_names['display_nickname'] = $user->nickname;
		if (!empty($user->first_name)) {
			$display_names['display_firstname'] = $user->first_name;
		}
		if (!empty($user->last_name)) {
			$display_names['display_lastname'] = $user->last_name;
		}
		if (!empty($user->first_name) && !empty($user->last_name)) {
			$display_names['display_firstlast'] = $user->first_name.' '.$user->last_name;
			$display_names['display_lastfirst'] = $user->last_name.' '.$user->first_name;
		}
		// Only add this display name value if it isn't duplicated elsewhere
		if (!in_array($user->display_name, $display_names)) {
			$display_names['display_displayname'] = $user->display_name;
		}
		
		$display_names = array_map('trim', $display_names);
		$display_names = array_unique($display_names);
		
		$display_name_options_html = '';
		foreach ($display_names as $id => $item) {
			$display_name_options_html .= '<option id="'.$id.'" value="'.esc_attr($item).'"'.($user->display_name == $item ? ' selected="selected"' : '').'>'.$item.'</option>';
		}
		return $display_name_options_html;
		
	}
	
	public function get_display_custom_profile_settings() {
		return $this->settings['display_custom_profile_settings'];
	}
	
	public function prepare_user_avatar() {
		$this->enqueue_user_avatar_resources();
		// This function only deletes the avatar if the user has chosen to do so on the previous page
		if (function_exists('user_avatar_delete')) {
			user_avatar_delete();
		}
	}
	
	public function enqueue_user_avatar_resources() {
		// These are needed for user_avatar_form()
		wp_enqueue_script('thickbox');
		wp_enqueue_script('imgareaselect');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('imgareaselect');
	}
	
	public function debug($string) {
		if ($this->debug) {
			echo "\n<br />\n".$string;
		}
	}
	
	// Options-related methods
	
	public function add_options_page() {
		add_submenu_page(
			'options-general.php',
			'Front-End Users',
			'Front-End Users',
			'manage_options',
			'front-end-users.php',
			array($this, 'render_options_page')
		);
	}
	
	public function add_plugin_action_links($links, $file) {
		if ($file == 'front-end-users/front_end_users.php') {
			$settings_link = '<a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=front-end-users.php" title="Settings">Settings</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}
 	
	public function render_options_page() {
		require_once $this->plugin_file_path.'admin/options.php';
	}
	
	public function validate_options($options) {
		$options['url_path'] = trim($options['url_path'], '/ ');
		if (empty($options['url_path'])) {
			$options['url_path'] = 'profile';
		}
		if (!isset($options['display_custom_profile_settings'])) {
			$options['display_custom_profile_settings'] = 0;
		}
		$this->flush_rules();
		return $options;
	}
	
	public function settings_text() {
		echo '';
	}
	
	public function settings_input_url_path() {
		$options = get_option('front_end_users_options');
		echo '<input type="text" id="url_path" name="front_end_users_options[url_path]" value="'.esc_attr($options['url_path']).'" />';
		echo '<br /><em>(A value of "profile" will mean the user landing page is at "http://mysite.com/profile/".)</em>';
	}
	
	public function settings_input_display_custom_profile_settings() {
		$options = get_option('front_end_users_options');
		$checked = isset($options['display_custom_profile_settings']) ?
			($options['display_custom_profile_settings'] ? ' checked="checked"' : '') : '';
		echo '<input type="checkbox" id="display_custom_profile_settings" name="front_end_users_options[display_custom_profile_settings]" value="1"'.$checked.' />';
		echo '<br /><em>(This determines whether profile fields added by plugins or themes will show up in the default public settings view. Please note that some plugins\' profile fields may not function correctly outside of the admin section.)</em>';
	}
	
	public function settings_input_roles_with_admin_access() {
		$options = get_option('front_end_users_options');
		global $wp_roles;
		$roles_with_admin_access = $options['roles_with_admin_access'];
		if (empty($roles_with_admin_access)) {
			$roles_with_admin_access = array();
		}
		$roles_with_admin_access = array_unique(array_merge($roles_with_admin_access, array($this->administrator_role_key)));
		foreach($wp_roles->roles as $role_key => $role) {
			$id = 'roles_with_admin_access_'.$role_key;
			$checked = in_array($role_key, $roles_with_admin_access) ? ' checked="checked"' : '';
			$disabled = $role_key == $this->administrator_role_key ? ' disabled="disabled"' : '';
			echo '<div>';
			echo '<input type="checkbox" id="'.$id.'" name="front_end_users_options[roles_with_admin_access][]" value="'.$role_key.'"'.$checked.$disabled.' />';
			echo ' <label for="'.$id.'">'.$role['name'].'</label>';
			echo '</div>';
		}
	}

}

?>