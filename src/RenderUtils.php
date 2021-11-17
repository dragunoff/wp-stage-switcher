<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

final class RenderUtils {

	private function __construct() {
		// static class
	}

	public static function render_admin_notice( string $message ): void {
		$class = 'notice notice-info';

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	public static function render_sortable_handle(): void {
		?>
		<span
			class="c-sortable-handle js-drgnff-sortable-handle"
			title="<?php esc_attr_e( 'Drag to reorder', 'drgnff-wp-stage-switcher' ); ?>"
		>
			<span class="dashicons dashicons-menu"></span>
		</span>
		<?php
	}

	public static function render_table_headers(): void {
		?>
		<div class="c-env-row">
			<div class="c-env-cell c-env-cell--label"></div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					<?php esc_html_e( 'Home URL', 'drgnff-wp-stage-switcher' ); ?>
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					<?php esc_html_e( 'Title', 'drgnff-wp-stage-switcher' ); ?>
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					<?php esc_html_e( 'Color', 'drgnff-wp-stage-switcher' ); ?>
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					<?php esc_html_e( 'Background', 'drgnff-wp-stage-switcher' ); ?>
				</label>
			</div>
		</div>
		<?php
	}

	// phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint
	public static function render_input(
		string $type,
		string $id,
		string $name,
		?string $value = null,
		array $atts = []
	): void {
		$classes = isset( $atts['class'] )
			? "js-drgnff-input {$atts['class']}"
			: 'js-drgnff-input';
		unset( $atts['class'] );

		?>
		<input
			type="<?php echo esc_attr( $type ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="<?php echo esc_attr( $classes ); ?>"
			<?php
			foreach ( $atts as $key => $val ) {
				if ( is_bool( $val ) ) {
					echo $val ? esc_attr( $key ) : '';
				} else {
					printf( '%s="%s"', esc_attr( $key ), esc_attr( $val ) );
				}
			}
			?>
		/>
		<?php
	}

}
