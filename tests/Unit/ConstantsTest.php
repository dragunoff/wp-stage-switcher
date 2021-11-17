<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\Constants;
use Drgnff\WP\StageSwitcher\Tests\Helpers;

class ConstantsTest extends TestCase {

	private string $plugin_file;

	public function test_should_throw_on_non_existing_constant_name(): void {
		$name = Helpers::random_id();

		$this->expectExceptionMessage( "Constant '{$name}' does not exist." );

		$object = new Constants( $this->plugin_file );
		$object->get( $name );
	}

	public function test_should_get_assets_url(): void {
		Functions\when( 'plugins_url' )
		->alias(
			function( string $path, string $plugin_file ): string {
				if ( 'assets' !== $path || $this->plugin_file !== $plugin_file ) {
					$this->fail( 'Unexpected call to "plugins_url".' );
				}

				return $this->plugin_file . '/assets';
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertTrue( $object->get( 'assets_url' ) === $this->plugin_file . '/assets/' );
	}

	public function test_should_get_plugin_version(): void {
		Functions\when( 'get_file_data' )
		->alias(
			// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
			function( string $plugin_file, array $headers ): array {
				if ( [ 'Version' => 'Version' ] !== $headers || $this->plugin_file !== $plugin_file ) {
					$this->fail( 'Unexpected call to "get_file_data".' );
				}

				return [ 'Version' => '1.0.0' ];
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertTrue( '1.0.0' === $object->get( 'version' ) );
	}

	public function test_should_get_default_environment(): void {
		$object = new Constants( $this->plugin_file );

		$this->assertSame(
			[
				'title' => 'UNKNOWN',
				'color' => '#ffffff',
				'background_color' => '#663399',
			],
			$object->get( 'default_environment' )
		);
	}

	public function test_should_get_default_environment_original(): void {
		Functions\when( 'get_option' )
		->alias(
			// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
			static function( string $option_name ) {
				if ( 'drgnff_wp_stage_switcher__default_environment' !== $option_name ) {
					return;
				}

				return [];
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertSame(
			[
				'title' => 'UNKNOWN',
				'color' => '#ffffff',
				'background_color' => '#663399',
			],
			$object->get( 'default_environment_original' )
		);
	}

	public function test_should_apply_default_environment_filter(): void {
		$env = [
			'title' => Helpers::random_id(),
			'color' => Helpers::random_hex_color(),
			'background_color' => Helpers::random_hex_color(),
		];

		Filters\expectApplied( 'drgnff_wp_stage_switcher__default_environment' )
		->times( 2 )
		->andReturn( $env );

		$object = new Constants( $this->plugin_file );

		$this->assertSame( $env, $object->get( 'default_environment' ) );
	}

	public function test_should_get_default_environment_from_options(): void {
		$env = [
			'title' => 'FROM OPTIONS',
		];

		Functions\when( 'get_option' )
		->alias(
			// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
			static function( $option_name ) use ( $env ) {
				if ( 'drgnff_wp_stage_switcher__default_environment' !== $option_name ) {
					return;
				}

				return $env;
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertSame( $env, $object->get( 'default_environment' ) );
	}

	public function test_should_get_is_default_environment_overridden(): void {
		add_filter(
			'drgnff_wp_stage_switcher__default_environment', static function( $arg ) {
				return $arg;
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertTrue( $object->get( 'is_default_environment_overridden' ) );
	}

	public function test_should_get_is_default_environment_overridden_not(): void {
		$object = new Constants( $this->plugin_file );

		$this->assertFalse( $object->get( 'is_default_environment_overridden' ) );
	}

	public function test_should_get_is_environments_overridden(): void {
		add_filter(
			'drgnff_wp_stage_switcher__environments', static function( $arg ) {
				return $arg;
			}
		);

		$object = new Constants( $this->plugin_file );

		$this->assertTrue( $object->get( 'is_environments_overridden' ) );
	}

	public function test_should_get_is_environments_overridden_not(): void {
		$object = new Constants( $this->plugin_file );

		$this->assertFalse( $object->get( 'is_environments_overridden' ) );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->plugin_file = Helpers::random_id();

		Functions\when( 'get_file_data' )->justReturn( [ 'Version' => '1.0.0' ] );
		Functions\when( 'plugins_url' )->justReturn();
		Functions\when( 'get_option' )->justReturn( false );
	}

}
