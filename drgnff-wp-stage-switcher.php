<?php

declare( strict_types = 1 );

/**
 * Plugin Name: Stage Switcher
 * Plugin URI: https://github.com/dragunoff/wp-stage-switcher/
 * Description: Switch between server environments from the WordPress admin bar.
 * Author: Ivaylo Draganov
 * Author URI: https://dragunoff.github.io/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 4.7
 * Requires PHP: 7.4
 * Tested up to: 5.8
 * Network: true
 * Text Domain: drgnff-wp-stage-switcher
 * Domain Path: /lang
 */

namespace Drgnff\WP\StageSwitcher;

require_once __DIR__ . '/src/autoload.php';

add_action(
	'plugins_loaded',
	static function (): void {
		$env_manager = new EnvironmentsManager();

		$plugin = new Plugin(
			new I18n( __FILE__ ),
			$env_manager,
			new AdminBar( $env_manager )
		);
		$plugin->run();
	}
);
