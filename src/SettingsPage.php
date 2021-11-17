<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class SettingsPage {

	private Constants $constants;

	public function __construct( Constants $constants ) {
		$this->constants = $constants;
	}

	public function run(): void {
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_filter( 'plugin_action_links_set-the-stage/set-the-stage.php', [ $this, 'add_settings_link' ] );
	}

	public function register_settings_page(): void {
		add_options_page(
			__( 'Set the Stage', 'drgnff-wp-stage-switcher' ),
			__( 'Set the Stage', 'drgnff-wp-stage-switcher' ),
			'manage_options',
			'drgnff-wp-stage-switcher',
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo get_admin_page_title(); ?></h1>
			<form action="options.php" method="post">
				<?php settings_fields( 'drgnff-wp-stage-switcher' ); ?>
				<?php do_settings_sections( 'drgnff-wp-stage-switcher' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function enqueue_assets( string $hook_suffix ): void {
		if ( 'settings_page_drgnff-wp-stage-switcher' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script(
			'drgnff-wp-stage-switcher__admin',
			$this->constants->get( 'assets_url' ) . 'admin.js',
			[ 'wp-color-picker', 'jquery', 'wp-util', 'jquery-ui-sortable' ],
			$this->constants->get( 'version' ),
			true
		);

		wp_enqueue_style(
			'drgnff-wp-stage-switcher__admin',
			$this->constants->get( 'assets_url' ) . 'admin.css',
			[],
			$this->constants->get( 'version' )
		);
	}

	/**
	 * @param array<string> $links
	 * @return array<string>
	 */
	public function add_settings_link( array $links ): array {
		$added = [
			sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'options-general.php?page=drgnff-wp-stage-switcher' ),
				__( 'Settings', 'drgnff-wp-stage-switcher' )
			),
		];

		$links = array_merge( $links, $added );

		return $links;
	}

}
