<?php

// Renders the FEU header view (views/header.php by default)
function feu_header() {
	global $feu;
	$feu->render_header();
}

// Renders the FEU footer view (views/footer.php by default)
function feu_footer() {
	global $feu;
	$feu->render_footer();
}

// Renders the beginning of a standardized FEU UI box
function feu_box_() {
	$html = '<div class="feu-box">';
	$html = apply_filters('feu_box_', $html);
	echo $html;
}

// Renders the end of a standardized FEU UI box
function _feu_box() {
	$html = '</div>';
	$html = apply_filters('_feu_box', $html);
	echo $html;
}

// Renders a menu of FEU views
function feu_menu() {
	global $feu;
	echo $feu->get_user_menu();
}

// Renders an FEU view
function feu_render_view($view) {
	global $feu;
	$feu->render_view($view);
}

// Returns a boolean of whether a user is currently logged in
function feu_is_logged_in() {
	global $feu;
	return $feu->is_logged_in();
}

// Returns a boolean of whether the current user has access to wp-admin
function feu_has_admin_access() {
	global $feu;
	return $feu->has_admin_access();
}

// Returns the URL of a FEU view
function feu_get_url($view_name=null) {
	global $feu;
	return $feu->get_view_url($view_name);
}

// Returns an <ul> of useful user links that depend on the current user state.  If a user's first name is "John", these
// could look like one of the following:
// * Sign in | Register
// * John | Sign out
// * John | Dashboard | Sign out
function feu_user_header_links() {
	global $feu;
	return $feu->user_header_links();
}

// Returns an array of useful user links that depend on the current user state.  You'll want to iterate through these
// and format each entry's data in a way that's appropriate for your layout.  Each entry represents a link and is an
// array of three elements.
//
// Here are some examples of entries that can be returned in this array:
// array('sign_in', 'Sign in', 'http://mysite.com/wp-login.php')
// array('register', 'Register', 'http://mysite.com/wp-login.php?action=register')
// array('profile', 'John', 'http://mysite.com/profile/')
// array('dashboard', 'Dashboard', 'http://mysite.com/wp-admin/')
//
// Here's an example of how to use it to create links:
// $user_links = feu_user_header_links_array();
// foreach($user_links as $link){
// 	echo '<a href="'.$link[2].'" class="user_action_link_'.$link[0].'">'.$link[1].'</a>';
// }
function feu_user_header_links_array() {
	global $feu;
	return $feu->user_header_links_array();
}

// Accepts an array with two elements, the first being a boolean where true == message and false == error, and
// the second being either the text to display or a WP_Error object whose messages will all be displayed.
function feu_form_message($message=null) {
	if (is_array($message) && count($message) == 2) {
		$class = $message[0] ? 'message' : 'error';
		if (is_wp_error($message[1])) {
			$error_strings = array();
			$errors = $message[1]->get_error_messages();
			foreach($errors as $error) {
				$error_strings[] = '<div>'.$error.'</div>';
			}
			$message = implode('', $error_strings);
		} else {
			$message = $message[1];
		}
		echo '<div class="'.$class.'">'.$message.'</div>';
	} else {
		if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
			echo '<div class="message">Successfully updated!</div>';
		}
	}
}

// Returns the HTML for the <option>s of possible display names that the user can choose in the profile update form. 
function feu_get_display_names_options_html($user) {
	global $feu;
	return $feu->get_display_names_options_html($user);
}

?>