=== Set the Stage ===
Contributors: dragunoff
Tags: stage, environment, switcher, menu
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.0.1
Requires PHP: 7.4
Requires at least: 4.7
Tested up to: 6.1

Quickly switch between environments (live, test, dev) from the admin bar.

== Description ==

Adds a menu to the admin bar to quickly identify different environments for a site (e.g. production and development) and easily switch to the same URL on other environments. Multi-site compatible for both sub-domain and sub-directory installations.

== Installation ==

1. Activate from the plugins menu.
1. Navigate to the plugin settings page or use a filter to set up your environments.

== Configuration ==

The plugin has a convenient settings page but can also be configured programatically via a filter hook.

= Setting environments via the WordPress admin interface =

Navigate to "Settings > Set the Stage" in the WordPress admin to review and edit the configuration.

= Setting environments via a WordPress filter hook =

Hook to `drgnff_wp_stage_switcher__environments` and return an array with environments. Here's an example filter function:

`
add_filter( 'drgnff_wp_stage_switcher__environments', function ($envs) {
	return [
		[
			'url' => 'https://example.com', // home url
			'title' => 'LIVE', // display name
			'color' => '#ffffff', // hex color (optional)
			'background_color' => '#ff0000', // hex color (optional)
		],
		[
			'url' => 'https://example.com',
			'title' => 'DEVELOPMENT',
			'color' => '#ffffff',
			'background_color' => '#228b22',
		],
	];
});
`

= Controlling visibility of the switcher =

By default the switcher menu is displayed to all logged in users. By hooking to `drgnff_wp_stage_switcher__should_display_switcher` and returning a boolean you can control whether the switcher should be displayed.

= Overriding the default environment =

The default environment is used for the current site if it's not in the list of environments. By hooking to `drgnff_wp_stage_switcher__default_environment` you can control the title and colors for the default environment.

= A few notes on configuration =

* Filters have a precedence over manual configuration made in the settings page. If filters are used then relevant sections on the settings page are rendered as read-only.
* For multi-site installations use the URLs of the main site.
* The plugin initiates its logic on the `plugins_loaded` hook with priority `11`. Thus filter hooks that affect the plugin must be added before that.

== Screenshots ==

1. The admin bar menu in action
1. The settings page
1. Adding the current environment to the list

== Changelog ==

= 1.0.1 =
**Fixed**
- Fix default environment reset button

= 1.0.0 =
* Initial release.
