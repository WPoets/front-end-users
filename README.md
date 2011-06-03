Front-End Users
==================================================
Hides the WordPress admin section from specified user roles, allows users to edit their settings from the front-end, and allows for customization of user-specific pages on the front-end.  

Description
-----------

Front-End Users is a WordPress plugin that prevents specified user roles from accessing the WordPress admin section (the pages in /wp-admin/), sets up a profile editing page on the front-end, and allows for customization of front-end user-specific pages.  This allows you to hide all of the WordPress back-end from specified roles (e.g. Subscribers) and instead present to them a profile editing page (and, optionally, other user-specific pages) that have the same layout as the rest of the site.  The front-end functionality is available to all roles, but you can choose which roles are able to access the WordPress admin section (by default, only Administrators have access to it).

Front-End Users also makes it easy to:

* Display user-specific links that depend on the login state (e.g. "Sign in | Register", "John | Sign out", "John | Dashboard | Sign out")
* Change the URL of the profile editing page and other user-specific pages
* Add other user-specific pages and display a menu of all of some of these pages
* Determine whether the current user has access to the admin section

If the [User Avatar plugin](http://wordpress.org/extend/plugins/user-avatar/) is also installed, the avatar-editing functionality it provides will be shown on the front-end user settings page.

If you'd like to grab development releases, see what new features are being added, or browse the source code please visit the [GitHub repo](http://github.com/tombenner/front-end-users).

Installation
------------

1. Upload `front-end-users` to the `wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

Frequently Asked Questions
--------------------------

####Where should I go for support questions or to ask for a new feature?

Please feel free to either add a topic in the WordPress forum or contact me through GitHub for any questions about Front-End Users:

* [WordPress Forum](http://wordpress.org/tags/front-end-users?forum_id=10)
* [GitHub](http://github.com/tombenner/)