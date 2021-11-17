<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class SettingsDefaultSection {

	private Constants $constants;

	public function __construct( Constants $constants ) {
		$this->constants = $constants;
	}

	public function run(): void {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'add_settings_sections' ] );

		if ( $this->constants->get( 'is_default_environment_overridden' ) ) {
			add_action( 'admin_notices', [ $this, 'render_notice_overridden' ] );
		}
	}

	public function register_settings(): void {
		register_setting(
			'drgnff-wp-stage-switcher',
			'drgnff_wp_stage_switcher__default_environment',
			[
				'type' => 'array',
				'sanitize_callback' => [ $this, 'sanitize_default_environment' ],
			]
		);
	}

	// phpcs:ignore SlevomatCodingStandard.TypeHints
	public function sanitize_default_environment( $value ): array {
		$default_environment_original = $this->constants->get( 'default_environment_original' );

		if ( ! is_array( $value ) || $default_environment_original === $value ) {
			return [];
		}

		return [
			'title' => $value['title'],
			'color' => $value['color'] ?? '',
			'background_color' => $value['background_color'] ?? '',
		];
	}

	public function add_settings_sections(): void {
		add_settings_section(
			'drgnff-wp-stage-switcher__default_env',
			__( 'Default Environment', 'drgnff-wp-stage-switcher' ),
			[ $this, 'render_default_environment_section' ],
			'drgnff-wp-stage-switcher'
		);
	}

	public function render_notice_overridden(): void {
		RenderUtils::render_admin_notice(
			__( 'Default environment has been overridden with a filter.', 'drgnff-wp-stage-switcher' )
		);
	}

	public function render_default_environment_section(): void {
		$default_environment = $this->constants->get( 'default_environment' );

		$title = $default_environment['title'];
		$color = sanitize_hex_color( $default_environment['color'] ?? '' );
		$background_color = sanitize_hex_color( $default_environment['background_color'] ?? '' );
		$disabled = $this->constants->get( 'is_default_environment_overridden' );

		$default_environment_original = $this->constants->get( 'default_environment_original' );

		$title_original = $default_environment_original['title'];
		$color_original = sanitize_hex_color( $default_environment_original['color'] ?? '' );
		$background_color_original = sanitize_hex_color( $default_environment_original['background_color'] ?? '' );
		?>

		<p>
			<?php esc_html_e( 'This will be used if the current site is not part of the environments list.', 'drgnff-wp-stage-switcher' ); ?>
		</p>

		<div class="c-env-table">
			<?php RenderUtils::render_table_headers(); ?>

			<div class="c-env-row js-drgnff-row <?php echo $disabled ? ' c-env-row--disabled' : ''; ?>">
				<div class="c-env-cell"></div>
				<div class="c-env-cell">
					<?php
					RenderUtils::render_input(
						'url',
						'drgnff_wp_stage_switcher__default_environment[url]',
						'drgnff_wp_stage_switcher__default_environment[url]',
						home_url(),
						[
							'disabled' => true,
						]
					);
					?>
				</div>
				<div class="c-env-cell">
					<?php
					RenderUtils::render_input(
						'text',
						'drgnff_wp_stage_switcher__default_environment[title]',
						'drgnff_wp_stage_switcher__default_environment[title]',
						$title,
						[
							'disabled' => $disabled,
							'required' => true,
							'data-original-value' => $title_original,
						]
					);
					?>
				</div>

				<div class="c-env-cell">
					<?php
					RenderUtils::render_input(
						'text',
						'drgnff_wp_stage_switcher__default_environment[color]',
						'drgnff_wp_stage_switcher__default_environment[color]',
						sanitize_hex_color( $color ),
						[
							'disabled' => $disabled,
							'class' => 'js-drgnff-color-field',
							'data-original-value' => $color_original,
						]
					);
					?>
				</div>

				<div class="c-env-cell">
					<?php
					RenderUtils::render_input(
						'text',
						'drgnff_wp_stage_switcher__default_environment[background_color]',
						'drgnff_wp_stage_switcher__default_environment[background_color]',
						sanitize_hex_color( $background_color ),
						[
							'disabled' => $disabled,
							'class' => 'js-drgnff-color-field',
							'data-original-value' => $background_color_original,
						]
					);
					?>
				</div>

				<?php if ( ! $disabled ) : ?>
					<div class="c-env-cell">
						<button
							type="button"
							class="button button-secondary js-drgnff-default-reset"
							title="<?php esc_attr_e( 'Reset to plugin&#39;s original values', 'drgnff-wp-stage-switcher' ); ?>"
						>
							<?php esc_html_e( 'Reset', 'drgnff-wp-stage-switcher' ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

}
