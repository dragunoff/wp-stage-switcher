<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class Constants {

	// phpcs:ignore SlevomatCodingStandard.TypeHints
	private array $constants;

	public function __construct( string $plugin_file ) {
		// default_environment
		$default_env = [
			'title' => __( 'UNKNOWN', 'drgnff-wp-stage-switcher' ),
			'color' => '#ffffff',
			'background_color' => '#663399',
		];

		$this->constants['default_environment'] =
			has_filter( 'drgnff_wp_stage_switcher__default_environment' ) || ! get_option( 'drgnff_wp_stage_switcher__default_environment' )
			? apply_filters( 'drgnff_wp_stage_switcher__default_environment', $default_env )
			: get_option( 'drgnff_wp_stage_switcher__default_environment' );

		$this->constants['default_environment_original'] = apply_filters( 'drgnff_wp_stage_switcher__default_environment', $default_env );

		// is_default_environment_overridden
		$this->constants['is_default_environment_overridden'] = ! ! has_filter( 'drgnff_wp_stage_switcher__default_environment' );

		// is_environments_overridden
		$this->constants['is_environments_overridden'] = ! ! has_filter( 'drgnff_wp_stage_switcher__environments' );

		// version
		$plugin_headers = [
			'Version' => 'Version',
		];
		$this->constants['version'] = get_file_data( $plugin_file, $plugin_headers )['Version'];

		// assets_url
		$this->constants['assets_url'] = trailingslashit( plugins_url( 'assets', $plugin_file ) );
	}

	// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
	public function get( string $name ) {
		if ( ! isset( $this->constants[ $name ] ) ) {
			throw new \Exception( "Constant '{$name}' does not exist." );
		}

		return $this->constants[ $name ];
	}

}
