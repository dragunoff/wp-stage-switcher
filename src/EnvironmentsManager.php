<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class EnvironmentsManager {

	private string $const_name;

	/** @var array<\Drgnff\WP\StageSwitcher\Environment> */
	private array $environments;
	private Environment $current_environment;

	/** @var array<string> */
	private array $default_environment;

	public function run(): void {
		add_action( 'init', [ $this, 'init_props' ] );
	}

	public function init_props(): void {
		$this->const_name = apply_filters( 'drgnff_wp_stage_switcher__const_name', 'DRGNFF_WP_STAGE_SWITCHER__ENVS' );
		$this->default_environment = apply_filters(
			'drgnff_wp_stage_switcher__default_environment', [
				'title' => __( 'UNKNOWN', 'drgnff-wp-stage-switcher' ),
				'slug' => 'unknown',
				'color' => '#FFFFFF',
				'background_color' => '#663399',
			]
		);

		$environments = defined( $this->const_name )
			? constant( $this->const_name )
			: [];
		$environments = apply_filters( 'drgnff_wp_stage_switcher__envs', $environments );

		array_walk(
			$environments, function( $env, $stage_url ): void {
				$this->environments[ self::parse_stage_url( $stage_url ) ] = new Environment(
					$env['title'],
					$env['slug'],
					$env['color'] ?? '',
					$env['background_color'] ?? ''
				);
			}
		);

		if ( ! isset( $this->environments[ home_url() ] ) ) {
			$this->environments[ home_url() ] = new Environment(
				$this->default_environment['title'],
				$this->default_environment['slug'],
				$this->default_environment['color'],
				$this->default_environment['background_color']
			);
		}

		$this->current_environment = $this->environments[ home_url() ];
		unset( $this->environments[ home_url() ] );
	}

	/** @return array<\Drgnff\WP\StageSwitcher\Environment> */
	public function get_environments(): array {
		return $this->environments;
	}

	public function get_current_environment(): Environment {
		return $this->current_environment;
	}

	public function needs_styles(): bool {
		return $this->current_environment->color && $this->current_environment->background_color;
	}

	private static function parse_stage_url( string $stage_url ): string {
		if ( is_multisite() ) {
			if ( is_subdomain_install() && ! is_main_site() ) {
				$stage_host = wp_parse_url( $stage_url, PHP_URL_HOST );
				$stage_host_parts = explode( '.', $stage_host );

				$current_host = wp_parse_url( get_home_url(), PHP_URL_HOST );
				$current_host_parts = explode( '.', $current_host );

				$subdomain = array_shift( $current_host_parts );
				array_unshift( $stage_host_parts, $subdomain );

				$new_stage_host = implode( '.', $stage_host_parts );

				$stage_url = str_replace( $stage_host, $new_stage_host, $stage_url );
			} else {
				$stage_url .= wp_parse_url( get_home_url(), PHP_URL_PATH );
			}
		}

		return untrailingslashit( $stage_url );
	}

}
