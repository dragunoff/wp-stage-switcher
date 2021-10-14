<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\Environment;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\Tests\Helpers;

class EnvironmentsManagerTest extends TestCase {

	/** @var array<\ArrayObject> */
	private array $random_envs;

	public function test_it_should_apply_envs_filter(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertTrue( isset( $object->get_environments()[ array_keys( $this->random_envs )[0] ] ) );
		$this->assertTrue( isset( $object->get_environments()[ array_keys( $this->random_envs )[1] ] ) );
		$this->assertTrue( $object->get_environments()[ array_keys( $this->random_envs )[0] ] instanceof Environment );
		$this->assertTrue( $object->get_environments()[ array_keys( $this->random_envs )[1] ] instanceof Environment );
	}

	public function test_it_should_apply_default_environment_filter(): void {
		$envs = $this->random_envs;
		$env = array_pop( $envs );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__default_environment' )
		->once()
		->andReturn( $env );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertSame( $env['title'], $object->get_current_environment()->title );
		$this->assertSame( $env['slug'], $object->get_current_environment()->slug );
		$this->assertSame( $env['color'], $object->get_current_environment()->color );
		$this->assertSame( $env['background_color'], $object->get_current_environment()->background_color );
	}

	public function test_it_should_return_current_environment(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertSame( $this->random_envs[ home_url() ]['title'], $object->get_current_environment()->title );
		$this->assertSame( $this->random_envs[ home_url() ]['slug'], $object->get_current_environment()->slug );
		$this->assertSame( $this->random_envs[ home_url() ]['color'], $object->get_current_environment()->color );
		$this->assertSame( $this->random_envs[ home_url() ]['background_color'], $object->get_current_environment()->background_color );
	}

	public function test_it_should_check_when_styles_are_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->andReturn(
			[
				home_url() => [
					'title' => 'STYLES',
					'slug' => 'styles',
					'color' => '#FFFFFF',
					'background_color' => '#FF0000',
				],
			]
		);

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertTrue( $object->needs_styles() );
	}

	public function test_it_should_check_when_styles_are_not_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->andReturn(
			[
				home_url() => [
					'title' => 'NO STYLES',
					'slug' => 'no-styles',
				],
			]
		);

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertFalse( $object->needs_styles() );
	}

	public function test_it_should_parse_multisite_subdir_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://example.com/subdir' );
		Functions\when( 'get_home_url' )->justReturn( 'http://example.com/subdir' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertTrue( isset( $object->get_environments()[ array_key_first( $this->random_envs ) . '/subdir' ] ) );
	}

	public function test_it_should_parse_multisite_subdomain_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'is_subdomain_install' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://subdomain.example.com' );
		Functions\when( 'get_home_url' )->justReturn( 'http://subdomain.example.com' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$hostname = wp_parse_url( array_key_first( $object->get_environments() ), PHP_URL_HOST );
		$this->assertSame( 0, strpos( $hostname, 'subdomain.' ) );
	}

	public function test_it_should_parse_multisite_subdomain_mainsite_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'is_subdomain_install' )->justReturn( true );
		Functions\when( 'is_main_site' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://example.com' );
		Functions\when( 'get_home_url' )->justReturn( 'http://example.com' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__envs' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertTrue( isset( $object->get_environments()[ array_key_first( $this->random_envs ) ] ) );
	}

	public function test_it_should_apply_const_name_filter(): void {
		$random_const_name = strtoupper( Helpers::random_id() );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__const_name' )
		->once()
		->with( 'DRGNFF_WP_STAGE_SWITCHER__ENVS' )
		->andReturn( 'DRGNFF_WP_STAGE_SWITCHER__' . $random_const_name );

        // phpcs:disable NeutronStandard.Constants.DisallowDefine.Define
		define( 'DRGNFF_WP_STAGE_SWITCHER__' . $random_const_name, $this->random_envs );

		$object = new EnvironmentsManager();
		$object->init_props();

		$this->assertTrue( isset( $object->get_environments()[ array_keys( $this->random_envs )[0] ] ) );
		$this->assertTrue( isset( $object->get_environments()[ array_keys( $this->random_envs )[1] ] ) );
		$this->assertTrue( $object->get_environments()[ array_keys( $this->random_envs )[0] ] instanceof Environment );
		$this->assertTrue( $object->get_environments()[ array_keys( $this->random_envs )[1] ] instanceof Environment );
	}

	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'is_multisite' )->justReturn( false );
		Functions\when( 'is_subdomain_install' )->justReturn( false );
		Functions\when( 'is_main_site' )->justReturn();

		$this->random_envs = [
			Helpers::random_url() => [
				'title' => Helpers::random_id(),
				'slug' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
			Helpers::random_url() => [
				'title' => Helpers::random_id(),
				'slug' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
			home_url() => [
				'title' => Helpers::random_id(),
				'slug' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
		];
	}

}
