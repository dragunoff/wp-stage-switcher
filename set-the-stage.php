<?php

declare( strict_types = 1 );

/**
 * Plugin Name: Set the Stage
 * Plugin URI: https://github.com/dragunoff/wp-stage-switcher/
 * Description: Quickly switch between environments (live, test, dev) from the admin bar.
 * Author: Ivaylo Draganov
 * Author URI: https://dragunoff.github.io/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version: 1.0.1
 * Requires PHP: 7.4
 * Requires at least: 4.7
 * Tested up to: 6.0
 * Network: true
 * Text Domain: drgnff-wp-stage-switcher
 * Domain Path: /lang
 */

namespace Drgnff\WP\StageSwitcher;

require_once __DIR__ . '/src/autoload.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\init', 11 );

function init(): void {
	$constants = new Constants( __FILE__ );
	$env_manager = new EnvironmentsManager( $constants );

	$plugin = new Plugin(
		new I18n( __FILE__ ),
		$env_manager,
		new AdminBar( $env_manager ),
		new SettingsPage( $constants ),
		new SettingsDefaultSection( $constants ),
		new SettingsEnvironmentsSection( $constants, $env_manager )
	);
	$plugin->run();
}

register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall' );

function uninstall(): void {
	delete_option( 'drgnff_wp_stage_switcher__default_environment' );
	delete_option( 'drgnff_wp_stage_switcher__environments' );
}
