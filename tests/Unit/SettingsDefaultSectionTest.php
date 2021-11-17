<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\Constants;
use Drgnff\WP\StageSwitcher\SettingsDefaultSection;
use Drgnff\WP\StageSwitcher\Tests\Helpers;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsDefaultSectionTest extends TestCase {

	private Constants $constants;

	public function test_should_hook_to_admin_init(): void {
		$constants = $this->constants;
		$constants->shouldIgnoreMissing()->getMock();

		$object = new SettingsDefaultSection( new $constants() );
		$object->run();

		$this->assertNotFalse( has_action( 'admin_init', '\Drgnff\WP\StageSwitcher\SettingsDefaultSection->register_settings()' ) );
		$this->assertNotFalse( has_action( 'admin_init', '\Drgnff\WP\StageSwitcher\SettingsDefaultSection->add_settings_sections()' ) );
	}

	public function test_should_register_settings(): void {
		$constants = $this->constants;
		$constants->shouldIgnoreMissing()->getMock();

		$object = new SettingsDefaultSection( new $constants() );

		Functions\expect( 'register_setting' )
		->once()
		->with(
			'drgnff-wp-stage-switcher',
			'drgnff_wp_stage_switcher__default_environment',
			[
				'type' => 'array',
				'sanitize_callback' => [ $object, 'sanitize_default_environment' ],
			]
		);

		$object->run();
		$object->register_settings();
	}

	public function test_should_sanitize_default_environment_that_is_not_an_array(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
			->with( 'default_environment_original' )
			->andReturn( [ 'title' => 'Default Environment Original' ] )
		->shouldReceive( 'get' )
			->with( 'is_default_environment_overridden' )
		->getMock();

		$object = new SettingsDefaultSection( new $constants() );

		$object->run();
		$sanitized = $object->sanitize_default_environment( null );

		$this->assertEquals( $sanitized, [] );
	}

	public function test_should_sanitize_default_environment_that_has_a_value(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
			->with( 'default_environment_original' )
			->andReturn( [ 'title' => 'Default Environment Original' ] )
		->shouldReceive( 'get' )
			->with( 'is_default_environment_overridden' )
		->getMock();

		$env = [
			'title' => Helpers::random_id(),
			'url' => home_url(),
			Helpers::random_id() => Helpers::random_id(),
		];
		$env_sanitized = [
			'title' => $env['title'],
			'color' => '',
			'background_color' => '',
		];

		$object = new SettingsDefaultSection( new $constants() );

		$object->run();
		$sanitized = $object->sanitize_default_environment( $env );

		$this->assertEquals( $sanitized, $env_sanitized );
	}

	public function test_should_render_notice_text(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_default_environment_overridden' )
			->andReturn( false )
		->getMock();

		$object = new SettingsDefaultSection( new $constants() );
		$object->run();

		ob_start();
		$object->render_notice_overridden();
		$output = strip_tags( Helpers::strip_whitespace( ob_get_clean() ) );

		$this->assertStringContainsString( 'Default environment has been overridden with a filter.', $output );
	}

	public function test_should_sanitize_default_environment_if_it_matches_constant(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
			->with( 'default_environment_original' )
			->andReturn( [ 'title' => 'Default Environment Original' ] )
		->shouldReceive( 'get' )
			->with( 'is_default_environment_overridden' )
		->getMock();

		$object = new SettingsDefaultSection( new $constants() );

		$object->run();
		$sanitized = $object->sanitize_default_environment( [ 'title' => 'Default Environment Original' ] );

		$this->assertEquals( $sanitized, [] );
	}

	public function test_should_add_settings_section(): void {
		$constants = $this->constants;
		$constants->shouldIgnoreMissing()->getMock();

		$object = new SettingsDefaultSection( new $constants() );

		Functions\expect( 'add_settings_section' )
		->once()
		->with(
			'drgnff-wp-stage-switcher__default_env',
			__( 'Default Environment', 'drgnff-wp-stage-switcher' ),
			[ $object, 'render_default_environment_section' ],
			'drgnff-wp-stage-switcher'
		);

		$object->run();
		$object->add_settings_sections();
	}

	public function test_should_render_default_env_section(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
			->with( 'default_environment' )
			->andReturn( [ 'title' => 'Default Environment' ] )
		->shouldReceive( 'get' )
			->with( 'default_environment_original' )
			->andReturn( [ 'title' => 'Default Environment Original' ] )
		->shouldReceive( 'get' )
			->with( 'is_default_environment_overridden' )
		->getMock();

		$object = new SettingsDefaultSection( new $constants() );
		$object->run();

		ob_start();
		$object->render_default_environment_section();

		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertNotEmpty( $output );
	}

	public function test_should_render_notice_overridden(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_default_environment_overridden' )
			->andReturn( true )
		->getMock();

		( new SettingsDefaultSection( new $constants() ) )->run();

		$this->assertNotFalse( has_action( 'admin_notices', '\Drgnff\WP\StageSwitcher\SettingsDefaultSection->render_notice_overridden()' ) );
	}

	public function test_should_not_render_notice_overridden(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_default_environment_overridden' )
			->andReturn( false )
		->getMock();

		( new SettingsDefaultSection( new $constants() ) )->run();

		$this->assertFalse( has_action( 'admin_notices', '\Drgnff\WP\StageSwitcher\SettingsDefaultSection->render_notice_overridden()' ) );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->constants = m::mock( 'overload:Drgnff\WP\StageSwitcher\Constants' );

		Functions\when( 'get_option' )->justReturn();
	}

}
