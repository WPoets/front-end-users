=== Front-End Users ===
Contributors: tombenner
Tags: front end, public, users, roles, admin, block, hide, prevent, profile, plugin
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1.2

Hides the WordPress admin section from specified user roles, allows users to edit their settings from the front-end, and more. 

== Description ==

Front-End Users is a WordPress plugin that prevents specified user roles from accessing the WordPress admin section (the pages in /wp-admin/), sets up a profile editing page on the front-end, and allows for customization of front-end user-specific pages.  This allows you to hide all of the WordPress back-end from specified roles (e.g. Subscribers) and instead present to them a profile editing page (and, optionally, other user-specific pages) that have the same layout as the rest of the site.  The front-end functionality is available to all roles, but you can choose which roles are able to access the WordPress admin section (by default, only Administrators have access to it).

Front-End Users also makes it easy to:

* Display user-specific links that depend on the login state (e.g. "Sign in | Register", "John | Sign out", "John | Dashboard | Sign out")
* Change the URL of the profile editing page and other user-specific pages
* Add other user-specific pages and display a menu of all of some of these pages
* Determine whether the current user has access to the admin section

The documentation for the hooks is in [example_hooks.php](http://github.com/tombenner/front-end-users/blob/master/example_hooks.php), and the documentation for the functions is in [functions.php](http://github.com/tombenner/front-end-users/blob/master/functions.php).

If the [User Avatar plugin](http://wordpress.org/extend/plugins/user-avatar/) is also installed, the avatar-editing functionality it provides will be shown on the front-end user settings page.

If you'd like to grab development releases, see what new features are being added, or browse the source code please visit the [GitHub repo](http://github.com/tombenner/front-end-users).

== Installation ==

1. Upload `front-end-users` to the `wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Make sure that [Pretty Permalinks](http://codex.wordpress.org/Introduction_to_Blogging#Pretty_Permalinks) are enabled and working

== Frequently Asked Questions ==

= Where should I go for support questions or to ask for a new feature? =

Please feel free to either add a topic in the WordPress forum or contact me through GitHub for any questions about Front-End Users:

* [WordPress Forum](http://wordpress.org/tags/front-end-users?forum_id=10)
* [GitHub](http://github.com/tombenner/)

== Screenshots ==

1. The settings for Front-End Users.
1. The default profile settings page that's displayed on the front-end to users. This can easily be modified or re-styled if any changes are desired.
1. An example of how user-specific pages and a menu listing them can be added.

== Changelog ==

= 1.1 =
* Added a switch that allows custom profile fields added by plugins and themes to show up in the default public settings view
* Added an exception to the admin URL rewrite to correct the admin profile form's action