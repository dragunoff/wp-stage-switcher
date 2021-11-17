<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class EnvironmentsManager {

	private Constants $constants;

	/** @var array<\Drgnff\WP\StageSwitcher\Environment> */
	private array $environments;
	private Environment $current_environment;
	private int $default_env_index = -1;
	private bool $is_current_environment_unknown = true;

	public function __construct( Constants $constants ) {
		$this->constants = $constants;
	}

	public function run(): void {
		// Setup all environments.
		$environments = get_option( 'drgnff_wp_stage_switcher__environments' );
		if ( ! is_array( $environments ) ) {
			$environments = [];
		}
		$environments = apply_filters( 'drgnff_wp_stage_switcher__environments', $environments );

		array_walk(
			$environments,
			function( $env, $i ): void {
				$url = self::parse_stage_url( $env['url'] );
				if ( $url === home_url() ) {
					$this->default_env_index = $i;
					$this->is_current_environment_unknown = false;
				}

				$this->environments[] = new Environment(
					$url,
					$env['title'],
					$env['color'] ?? '',
					$env['background_color'] ?? ''
				);
			}
		);

		// Setup default environment.
		$default_environment = $this->constants->get( 'default_environment' );

		if ( $this->default_env_index < 0 ) {
			$this->environments[] = new Environment(
				home_url(),
				$default_environment['title'],
				$default_environment['color'] ?? '',
				$default_environment['background_color'] ?? ''
			);
			$this->default_env_index = count( $this->environments ) - 1;
		}

		$this->current_environment = $this->environments[ $this->default_env_index ];
	}

	/** @return array<\Drgnff\WP\StageSwitcher\Environment> */
	public function get_environments(): array {
		return $this->environments;
	}

	public function get_current_environment(): Environment {
		return $this->current_environment;
	}

	public function needs_styles(): bool {
		return $this->current_environment->color || $this->current_environment->background_color;
	}

	public function is_current_environment_unknown(): bool {
		return $this->is_current_environment_unknown;
	}

	private static function parse_stage_url( string $stage_url ): string {
		if ( is_multisite() ) {
			$stage_url = untrailingslashit( $stage_url );

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
