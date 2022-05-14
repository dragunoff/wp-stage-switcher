<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Drgnff\WP\StageSwitcher\RenderUtils;
use Drgnff\WP\StageSwitcher\Tests\Helpers;

class RenderUtilsTest extends TestCase {

	public function test_should_print_admin_notice(): void {
		$class = 'notice notice-info';
		$message = Helpers::random_id();
		$template = sprintf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_admin_notice( $message );
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_sortable_handle(): void {
		$template = <<<'HTML'
		<span
			class="c-sortable-handle js-drgnff-sortable-handle"
			title="Drag to reorder"
		>
			<span class="dashicons dashicons-menu"></span>
		</span>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_sortable_handle();
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_table_headers(): void {
		$template = <<<'HTML'
		<div class="c-env-row">
			<div class="c-env-cell c-env-cell--label"></div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					Home URL
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					Title
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					Color
				</label>
			</div>

			<div class="c-env-cell c-env-cell--label">
				<label>
					Background
				</label>
			</div>
		</div>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_table_headers();
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_a_type(): void {
		$type = Helpers::random_id();
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;

		$template = <<<HTML
		<input
			type="{$type}"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input( $type, $id, $name, $value );
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_without_a_value(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input( 'text', $id, $name, $value );
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_a_value(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input( 'text', $id, $name, $value );
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_a_class(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;
		$class = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input {$class}"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input(
			'text',
			$id,
			$name,
			$value,
			[ 'class' => $class ]
		);
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_an_attribute(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;
		$attr = Helpers::random_id();
		$attr_value = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
			{$attr}="{$attr_value}"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input(
			'text',
			$id,
			$name,
			$value,
			[ $attr => $attr_value ]
		);
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_a_truthy_attribute(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;
		$attr = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
			{$attr}
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input(
			'text',
			$id,
			$name,
			$value,
			[ $attr => true ]
		);
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_a_falsy_attribute(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;
		$attr = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input(
			'text',
			$id,
			$name,
			$value,
			[ $attr => false ]
		);
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

	public function test_should_print_input_field_with_an_attribute_and_a_truthy_attribute(): void {
		$id = Helpers::random_id();
		$name = Helpers::random_id();
		$value = null;
		$attr = Helpers::random_id();
		$attr_value = Helpers::random_id();
		$bool_attr = Helpers::random_id();

		$template = <<<HTML
		<input
			type="text"
			id="{$id}"
			name="{$name}"
			value="{$value}"
			class="js-drgnff-input"
			{$attr}="{$attr_value}"
			{$bool_attr}
		/>
		HTML;
		$template = Helpers::strip_whitespace( $template );

		ob_start();
		RenderUtils::render_input(
			'text',
			$id,
			$name,
			$value,
			[
				$attr => $attr_value,
				$bool_attr => true,
			]
		);
		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( $template, $output );
		$this->assertSame( strlen( $template ), strlen( $output ) );
	}

}

