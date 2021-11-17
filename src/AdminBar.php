<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

use WP_Admin_Bar;

class AdminBar {

	private EnvironmentsManager $env_manager;

	/** @var array<\Drgnff\WP\StageSwitcher\Environment> */
	private array $environments;
	private Environment $current_environment;

	public function __construct( EnvironmentsManager $env_manager ) {
		$this->env_manager = $env_manager;
	}

	public function run(): void {
		$this->environments = $this->env_manager->get_environments();
		$this->current_environment = $this->env_manager->get_current_environment();

		if ( $this->should_display_switcher() ) {
			add_action( 'admin_bar_menu', [ $this, 'add_nodes_to_admin_bar' ] );
		}

		if ( $this->should_display_switcher() && $this->env_manager->needs_styles() ) {
			add_action( 'wp_before_admin_bar_render', [ $this, 'admin_bar_css' ] );
		}
	}

	public function add_nodes_to_admin_bar( WP_Admin_Bar $wp_admin_bar ): void {
		$args = [
			'id' => 'drgnff_wp_stage_switcher',
			'title' => '<span class="ab-icon dashicons dashicons-visibility"></span><span class="ab-label">' . esc_html( $this->current_environment->title ) . '</span>',
			'parent' => 'top-secondary',
		];
		$wp_admin_bar->add_node( $args );

		foreach ( $this->environments as $env ) {
			if ( $this->current_environment->url === $env->url ) {
				continue;
			}

			$wp_admin_bar->add_node(
				[
					'id' => 'drgnff_wp_stage_switcher__' . sanitize_html_class( strtolower( $env->url ) ),
					'parent' => 'drgnff_wp_stage_switcher',
					'title' => esc_html( $env->title ),
					'href' => esc_url( self::parse_stage_url( $env->url ) ),
				]
			);
		}
	}

	public function admin_bar_css(): void {
		?>
		<style>
			<?php if ( $this->current_environment->color ) { ?>
				#wp-admin-bar-drgnff_wp_stage_switcher > .ab-item > .ab-icon::before,
				#wp-admin-bar-drgnff_wp_stage_switcher > .ab-item > .ab-label,
				#wp-admin-bar-drgnff_wp_stage_switcher > .ab-item {
				color: <?php echo esc_html( sanitize_hex_color( $this->current_environment->color ) ); ?> !important;
			}
			<?php } ?>

			<?php if ( $this->current_environment->background_color ) { ?>
				#wp-admin-bar-drgnff_wp_stage_switcher > .ab-item {
					background-color: <?php echo esc_html( sanitize_hex_color( $this->current_environment->background_color ) ); ?> !important;
				}
			<?php } ?>
		</style>
		<?php
	}

	private function should_display_switcher(): bool {
		return apply_filters( 'drgnff_wp_stage_switcher__should_display_switcher', is_user_logged_in() );
	}

	private static function parse_stage_url( string $stage_url ): string {
		$path = wp_parse_url( $stage_url, PHP_URL_PATH );
		if ( $path ) {
			$stage_url = str_replace( $path, '', $stage_url );
		}

		return untrailingslashit( $stage_url ) . filter_input( INPUT_SERVER, 'REQUEST_URI' );
	}

}
