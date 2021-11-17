<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\Constants;
use Drgnff\WP\StageSwitcher\Environment;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection;
use Drgnff\WP\StageSwitcher\Tests\Helpers;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsEnvironmentsSectionTest extends TestCase {

	private Constants $constants;
	private EnvironmentsManager $env_manager;

	public function test_should_hook_to_admin_init(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		( new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() ) )->run();

		$this->assertNotFalse( has_action( 'admin_init', '\Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection->register_settings()' ) );
		$this->assertNotFalse( has_action( 'admin_init', '\Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection->add_settings_sections()' ) );
	}

	public function test_should_register_settings(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );

		Functions\expect( 'register_setting' )
		->once()
		->with(
			'drgnff-wp-stage-switcher',
			'drgnff_wp_stage_switcher__environments',
			[
				'type' => 'array',
				'sanitize_callback' => [ $object, 'sanitize_environments' ],
			]
		);

		$object->run();
		$object->register_settings();
	}

	public function test_should_sanitize_environments_that_is_not_an_array(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );

		$object->run();
		$sanitized = $object->sanitize_environments( null );

		$this->assertEquals( $sanitized, [] );
	}

	public function test_should_sanitize_environments_that_has_a_value(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$envs = [
			[
				'title' => Helpers::random_id(),
				'url' => Helpers::random_url(),
				Helpers::random_id() => Helpers::random_id(),
			],
			[
				'title' => Helpers::random_id(),
				'url' => Helpers::random_url(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
		];
		$envs_sanitized = [
			[
				'title' => $envs[0]['title'],
				'url' => $envs[0]['url'],
				'color' => '',
				'background_color' => '',
			],
			[
				'title' => $envs[1]['title'],
				'url' => $envs[1]['url'],
				'color' => $envs[1]['color'],
				'background_color' => $envs[1]['background_color'],
			],
		];

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );

		$object->run();
		$sanitized = $object->sanitize_environments( $envs );

		$this->assertEquals( $sanitized, $envs_sanitized );
	}

	public function test_should_sanitize_environments_that_has_an_empty_row(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$envs = [
			[
				'title' => Helpers::random_id(),
				'url' => Helpers::random_url(),
				'color' => '',
				'background_color' => '',
			],
			[
				'title' => '',
				'url' => '',
				'color' => '',
				'background_color' => '',
			],
		];
		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );

		$object->run();
		$sanitized = $object->sanitize_environments( $envs );

		$this->assertEquals( $sanitized, [ $envs[0] ] );
	}

	public function test_should_add_settings_section(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );

		Functions\expect( 'add_settings_section' )
		->once()
		->with(
			'drgnff-wp-stage-switcher__envs',
			__( 'Environments', 'drgnff-wp-stage-switcher' ),
			[ $object, 'render_environments_section' ],
			'drgnff-wp-stage-switcher'
		);

		$object->run();
		$object->add_settings_sections();
	}

	public function test_should_render_envs_section(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )->with( 'is_environments_overridden' )->getMock();

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );
		$object->run();

		ob_start();
		$object->render_environments_section();

		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertNotEmpty( $output );
	}

	public function test_should_render_notice_overridden(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_environments_overridden' )
			->andReturn( true )
		->getMock();

		( new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() ) )->run();

		$this->assertNotFalse( has_action( 'admin_notices', '\Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection->render_notice_overridden()' ) );
	}

	public function test_should_not_render_notice_overridden(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_environments_overridden' )
			->andReturn( false )
		->getMock();

		( new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() ) )->run();

		$this->assertFalse( has_action( 'admin_notices', '\Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection->render_notice_overridden()' ) );
	}

	public function test_should_render_notice_text(): void {
		$constants = $this->constants;
		$constants->shouldReceive( 'get' )
		->with( 'is_environments_overridden' )
			->andReturn( false )
		->getMock();

		$object = new SettingsEnvironmentsSection( new $constants(), new $this->env_manager() );
		$object->run();

		ob_start();
		$object->render_notice_overridden();
		$output = strip_tags( Helpers::strip_whitespace( ob_get_clean() ) );

		$this->assertStringContainsString( 'Environments have been overridden with a filter.', $output );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->env_manager = m::mock( 'overload:\Drgnff\WP\StageSwitcher\EnvironmentsManager' )
		->shouldReceive( 'get_environments' )
			->andReturn( [] )
		->shouldReceive( 'get_current_environment' )
			->andReturn( new Environment( home_url(), 'HOME' ) )
		->getMock();

		$this->constants = m::mock( 'overload:Drgnff\WP\StageSwitcher\Constants' );

		Functions\when( 'get_option' )->justReturn();
	}

}
