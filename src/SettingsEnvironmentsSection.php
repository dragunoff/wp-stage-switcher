<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class SettingsEnvironmentsSection {

	private Constants $constants;
	private EnvironmentsManager $env_manager;
	private Environment $current_environment;

	/** @var array<\Drgnff\WP\StageSwitcher\Environment> */
	private array $environments;

	public function __construct( Constants $constants, EnvironmentsManager $env_manager ) {
		$this->constants = $constants;
		$this->env_manager = $env_manager;
	}

	public function run(): void {
		$this->environments = $this->env_manager->get_environments();
		$this->current_environment = $this->env_manager->get_current_environment();

		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'add_settings_sections' ] );

		if ( $this->constants->get( 'is_environments_overridden' ) ) {
			add_action( 'admin_notices', [ $this, 'render_notice_overridden' ] );
		}
	}

	public function register_settings(): void {
		register_setting(
			'drgnff-wp-stage-switcher',
			'drgnff_wp_stage_switcher__environments',
			[
				'type' => 'array',
				'sanitize_callback' => [ $this, 'sanitize_environments' ],
			]
		);
	}

	// phpcs:ignore SlevomatCodingStandard.TypeHints
	public function sanitize_environments( $value ): array {
		if ( ! is_array( $value ) ) {
			return [];
		}

		$value = array_filter(
			$value, static function( $env ) {
				$is_invalid =
				( ! isset( $env['title'] ) || ! $env['title'] ) &&
				( ! isset( $env['url'] ) || ! $env['url'] );

				return ! $is_invalid;
			}
		);

		$value = array_map(
			static function( $env ) {
				return [
					'title' => $env['title'],
					'url' => $env['url'],
					'color' => $env['color'] ?? '',
					'background_color' => $env['background_color'] ?? '',
				];
			}, $value
		);

		return $value;
	}

	public function add_settings_sections(): void {
		add_settings_section(
			'drgnff-wp-stage-switcher__envs',
			__( 'Environments', 'drgnff-wp-stage-switcher' ),
			[ $this, 'render_environments_section' ],
			'drgnff-wp-stage-switcher'
		);
	}

	public function render_notice_overridden(): void {
		RenderUtils::render_admin_notice(
			__( 'Environments have been overridden with a filter.', 'drgnff-wp-stage-switcher' )
		);
	}

	public function render_environments_section(): void {
		$disabled = $this->constants->get( 'is_environments_overridden' );
		?>

		<p>
			<?php esc_html_e( 'These will be printed in order in the admin bar menu.', 'drgnff-wp-stage-switcher' ); ?>
		</p>

		<div class="c-env-table js-drgnff-sortable js-drgnff-env-table">
			<?php RenderUtils::render_table_headers(); ?>

			<?php
			foreach ( $this->environments as $i => $env ) :
				if ( $this->env_manager->is_current_environment_unknown() && $env === $this->current_environment ) {
					continue;
				}

				$url = $env->url;
				$title = $env->title;
				$color = sanitize_hex_color( $env->color ?? '' );
				$background_color = sanitize_hex_color( $env->background_color ?? '' );
				?>
				<div class="c-env-row js-drgnff-row<?php echo $disabled ? ' c-env-row--disabled' : ''; ?>">
					<div class="c-env-cell">
						<?php if ( ! $disabled ) : ?>
							<?php RenderUtils::render_sortable_handle(); ?>
						<?php endif; ?>
					</div>

					<div class="c-env-cell">
						<?php
						RenderUtils::render_input(
							'url',
							'drgnff_wp_stage_switcher__environments[' . $url . '][url]',
							'drgnff_wp_stage_switcher__environments[' . $i . '][url]',
							$url,
							[
								'disabled' => $disabled,
								'required' => true,
							]
						);
						?>
					</div>

					<div class="c-env-cell">
						<?php
						RenderUtils::render_input(
							'text',
							'drgnff_wp_stage_switcher__environments[' . $title . '][title]',
							'drgnff_wp_stage_switcher__environments[' . $i . '][title]',
							$title,
							[
								'disabled' => $disabled,
								'required' => true,
							]
						);
						?>
					</div>

					<div class="c-env-cell">
						<?php
						RenderUtils::render_input(
							'text',
							'drgnff_wp_stage_switcher__environments[' . $title . '][color]',
							'drgnff_wp_stage_switcher__environments[' . $i . '][color]',
							sanitize_hex_color( $color ),
							[
								'disabled' => $disabled,
								'class' => 'js-drgnff-color-field',
							]
						);
						?>
					</div>

					<div class="c-env-cell">
						<?php
						RenderUtils::render_input(
							'text',
							'drgnff_wp_stage_switcher__environments[' . $title . '][background_color]',
							'drgnff_wp_stage_switcher__environments[' . $i . '][background_color]',
							sanitize_hex_color( $background_color ),
							[
								'disabled' => $disabled,
								'class' => 'js-drgnff-color-field',
							]
						);
						?>
					</div>

					<?php if ( ! $disabled ) : ?>
						<div class="c-env-cell">
							<button
								type="button"
								class="button button-secondary js-drgnff-row-remove"
							>
								<?php esc_html_e( 'Remove', 'drgnff-wp-stage-switcher' ); ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php
		if ( ! $disabled ) :
			?>
			<div>
				<div class="c-env-cell">
					<button type="button" class="button button-secondary js-drgnff-add-button">
						<?php esc_html_e( 'Add New Environment', 'drgnff-wp-stage-switcher' ); ?>
					</button>
				</div>

				<script id="tmpl-drgnff-row" type="text/html">
					<div class="c-env-row js-drgnff-row">
						<div class="c-env-cell">
							<?php RenderUtils::render_sortable_handle(); ?>
						</div>

						<div class="c-env-cell">
							<?php
							RenderUtils::render_input(
								'url',
								'',
								'drgnff_wp_stage_switcher__environments[{{data.index}}][url]',
								'',
								[ 'required' => true ]
							);
							?>
						</div>

						<div class="c-env-cell">
							<?php
							RenderUtils::render_input(
								'text',
								'',
								'drgnff_wp_stage_switcher__environments[{{data.index}}][title]',
								'',
								[ 'required' => true ]
							);
							?>
						</div>

						<div class="c-env-cell">
							<?php
							RenderUtils::render_input(
								'text',
								'',
								'drgnff_wp_stage_switcher__environments[{{data.index}}][color]',
								'',
								[ 'class' => 'js-drgnff-color-field' ]
							);
							?>
						</div>

						<div class="c-env-cell">
							<?php
							RenderUtils::render_input(
								'text',
								'',
								'drgnff_wp_stage_switcher__environments[{{data.index}}][background_color]',
								'',
								[ 'class' => 'js-drgnff-color-field' ]
							);
							?>
						</div>

						<div class="c-env-cell">
							<button
								type="button"
								class="button button-secondary js-drgnff-row-remove"
							>
								<?php esc_html_e( 'Remove', 'drgnff-wp-stage-switcher' ); ?>
							</button>
						</div>
					</div>
				</script>
			</div>
		<?php endif; ?>
		<?php
	}

}
