<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\AdminBar;
use Drgnff\WP\StageSwitcher\Environment;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\Tests\Helpers;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AdminBarTest extends TestCase {

	private EnvironmentsManager $env_manager;

	public function test_should_hook_admin_menu_if_switcher_displayed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__should_display_switcher' )
		->with( true )
		->andReturn( true );

		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( true );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		( new AdminBar( new $env_manager() ) )->run();

		$this->assertNotFalse( has_action( 'admin_bar_menu', '\Drgnff\WP\StageSwitcher\AdminBar->add_nodes_to_admin_bar()' ) );
	}

	public function test_should_not_hook_admin_menu_if_switcher_not_displayed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__should_display_switcher' )
		->with( true )
		->andReturn( false );

		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( true );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		( new AdminBar( new $env_manager() ) )->run();

		$this->assertFalse( has_action( 'admin_bar_menu', '\Drgnff\WP\StageSwitcher\AdminBar->add_nodes_to_admin_bar()' ) );
	}

	public function test_should_hook_styles_if_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__should_display_switcher' )
		->with( true )
		->andReturn( true );

		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( true );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		( new AdminBar( new $env_manager() ) )->run();

		$this->assertNotFalse( has_action( 'wp_before_admin_bar_render', '\Drgnff\WP\StageSwitcher\AdminBar->admin_bar_css()' ) );
	}

	public function test_should_not_hook_styles_if_switcher_not_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__should_display_switcher' )
		->with( true )
		->andReturn( false );

		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( false );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		( new AdminBar( new $env_manager() ) )->run();

		$this->assertFalse( has_action( 'wp_before_admin_bar_render', '\Drgnff\WP\StageSwitcher\AdminBar->admin_bar_css()' ) );
	}

	public function test_should_print_css(): void {
		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( false );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		$object = new AdminBar( new $env_manager() );
		$object->run();

		ob_start();
		$object->admin_bar_css();

		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( '<style>', $output );
		$this->assertStringEndsWith( '</style>', $output );
	}

	public function test_should_add_nodes_to_admin_bar(): void {
		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( false );
		$env_manager->shouldReceive( 'get_environments' )->andReturn(
			[
				new Environment( Helpers::random_url(), 'TEST 2' ),
				new Environment( Helpers::random_url(), 'TEST 3' ),
				$env,
			]
		);
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		$wp_admin_bar = m::spy( 'WP_Admin_Bar' );

		$object = new AdminBar( new $env_manager() );
		$object->run();
		$object->add_nodes_to_admin_bar( $wp_admin_bar );

		$wp_admin_bar->shouldHaveReceived( 'add_node' )->times( 3 );
	}

	public function test_should_not_add_child_nodes_for_single_env(): void {
		$env = new Environment( home_url(), 'TEST' );
		$env_manager = $this->env_manager;
		$env_manager->shouldReceive( 'needs_styles' )->andReturn( false );
		$env_manager->shouldReceive( 'get_environments' )->andReturn( [ $env ] );
		$env_manager->shouldReceive( 'get_current_environment' )->andReturn( $env );

		$wp_admin_bar = m::spy( 'WP_Admin_Bar' );

		$object = new AdminBar( new $env_manager() );
		$object->run();
		$object->add_nodes_to_admin_bar( $wp_admin_bar );

		$wp_admin_bar->shouldHaveReceived( 'add_node' )->once();
	}

	protected function setUp(): void {
		parent::setUp();

		$this->env_manager = m::mock( 'overload:\Drgnff\WP\StageSwitcher\EnvironmentsManager' );

		Functions\when( 'is_user_logged_in' )->justReturn( true );
	}

}
