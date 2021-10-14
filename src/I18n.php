<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class I18n {

	private string $plugin_dirname;

	public function __construct( string $plugin_filename ) {
		$this->plugin_dirname = dirname( plugin_basename( $plugin_filename ) );
	}

	public function run(): void {
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'drgnff-wp-stage-switcher', false, $this->plugin_dirname . '/lang/' );
	}

}
