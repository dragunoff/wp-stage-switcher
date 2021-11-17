<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\Constants;
use Drgnff\WP\StageSwitcher\SettingsPage;
use Drgnff\WP\StageSwitcher\Tests\Helpers;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SettingsPageTest extends TestCase {

	private Constants $constants;

	public function test_should_hook_to_admin_menu(): void {
		( new SettingsPage( new $this->constants() ) )->run();

		$this->assertNotFalse( has_action( 'admin_menu', '\Drgnff\WP\StageSwitcher\SettingsPage->register_settings_page()' ) );
	}

	public function test_should_register_submenu_page(): void {
		$object = new SettingsPage( new $this->constants() );

		Functions\expect( 'add_options_page' )
		->once()
		->with(
			__( 'Set the Stage', 'drgnff-wp-stage-switcher' ),
			__( 'Set the Stage', 'drgnff-wp-stage-switcher' ),
			'manage_options',
			'drgnff-wp-stage-switcher',
			[ $object, 'render_settings_page' ]
		);

		$object->run();
		$object->register_settings_page();
	}

	public function test_should_render_settings_page(): void {
		$object = new SettingsPage( new $this->constants() );
		$random_title = Helpers::random_id();

		Functions\expect( 'get_admin_page_title' )
		->once()
		->andReturn( $random_title );

		Functions\expect( 'settings_fields' )
		->once()
		->with( 'drgnff-wp-stage-switcher' );

		Functions\expect( 'do_settings_sections' )
		->once()
		->with( 'drgnff-wp-stage-switcher' );

		Functions\expect( 'submit_button' )
		->once()
		->withNoArgs();

		$object->run();
		ob_start();
		$object->render_settings_page();

		$output = Helpers::strip_whitespace( ob_get_clean() );

		$this->assertStringStartsWith( '<div class="wrap"><h1>' . $random_title . '</h1><form action="options.php" method="post">', $output );
		$this->assertStringEndsWith( '</form></div>', $output );
	}

	public function test_should_hook_to_admin_enqueue_scripts(): void {
		( new SettingsPage( new $this->constants() ) )->run();

		$this->assertNotFalse( has_action( 'admin_enqueue_scripts', '\Drgnff\WP\StageSwitcher\SettingsPage->enqueue_assets()' ) );
	}

	public function test_should_enqueue_admin_scripts(): void {
		$object = new SettingsPage( new $this->constants() );

		Functions\expect( 'wp_enqueue_style' )
		->atLeast()
		->once()
		->with( 'wp-color-picker' );

		Functions\expect( 'wp_enqueue_script' )
		->once()
		->with(
			'drgnff-wp-stage-switcher__admin',
			'assets_url/admin.js',
			[ 'wp-color-picker', 'jquery', 'wp-util', 'jquery-ui-sortable' ],
			'0.0',
			true
		);

		$object->run();
		$object->enqueue_assets( 'settings_page_drgnff-wp-stage-switcher' );
	}

	public function test_should_enqueue_admin_styles(): void {
		$object = new SettingsPage( new $this->constants() );

		Functions\when( 'wp_enqueue_script' )->justReturn();

		Functions\expect( 'wp_enqueue_style' )
		->atLeast()
		->once()
		->with(
			'drgnff-wp-stage-switcher__admin',
			'assets_url/admin.css',
			[],
			'0.0'
		);

		$object->run();
		$object->enqueue_assets( 'settings_page_drgnff-wp-stage-switcher' );
	}

	public function test_should_not_enqueue_assets_on_other_admin_pages(): void {
		$object = new SettingsPage( new $this->constants() );

		Functions\expect( 'wp_enqueue_style' )->never();
		Functions\expect( 'wp_enqueue_script' )->never();

		$object->run();
		$object->enqueue_assets( 'some_other_page' );
	}

	public function test_should_hook_to_plugins_action_links(): void {
		$object = new SettingsPage( new $this->constants() );

		$object->run();

		self::assertNotFalse( has_filter( 'plugin_action_links_set-the-stage/set-the-stage.php', '\Drgnff\WP\StageSwitcher\SettingsPage->add_settings_link()' ) );
	}

	public function test_should_add_settings_link(): void {
		Functions\when( 'admin_url' )->returnArg();

		$links = [];
		$links_after = [
			'<a href="options-general.php?page=drgnff-wp-stage-switcher">Settings</a>',
		];

		$object = new SettingsPage( new $this->constants() );

		$object->run();
		$filtered_links = $object->add_settings_link( $links );

		self::assertSame( $filtered_links, $links_after );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->constants = m::mock( 'overload:Drgnff\WP\StageSwitcher\Constants' )
		->shouldReceive( 'get' )
			->with( 'assets_url' )
			->andReturn( 'assets_url/' )
		->shouldReceive( 'get' )
			->with( 'version' )
			->andReturn( '0.0' )
		->getMock();

		Functions\when( 'get_option' )->justReturn();
	}

}
