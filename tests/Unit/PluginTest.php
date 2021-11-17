<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Drgnff\WP\StageSwitcher\AdminBar;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\I18n;
use Drgnff\WP\StageSwitcher\Plugin;
use Drgnff\WP\StageSwitcher\SettingsDefaultSection;
use Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection;
use Drgnff\WP\StageSwitcher\SettingsPage;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PluginTest extends TestCase {

	public function test_should_run_dependent_modules(): void {
		$i18n = m::mock( 'overload:Drgnff\WP\StageSwitcher\I18n' );
		$env_manager = m::mock( 'overload:Drgnff\WP\StageSwitcher\EnvironmentsManager' );
		$admin_bar = m::mock( 'overload:Drgnff\WP\StageSwitcher\AdminBar' );
		$settings_page = m::mock( 'overload:Drgnff\WP\StageSwitcher\SettingsPage' );
		$settings_default_section = m::mock( 'overload:Drgnff\WP\StageSwitcher\SettingsDefaultSection' );
		$settings_environments_section = m::mock( 'overload:Drgnff\WP\StageSwitcher\SettingsEnvironmentsSection' );

		$i18n->shouldReceive( 'run' )->once();
		$env_manager->shouldReceive( 'run' )->once();
		$admin_bar->shouldReceive( 'run' )->once();
		$settings_page->shouldReceive( 'run' )->once();
		$settings_default_section->shouldReceive( 'run' )->once();
		$settings_environments_section->shouldReceive( 'run' )->once();

		( new Plugin(
			new I18n(),
			new EnvironmentsManager(),
			new AdminBar(),
			new SettingsPage(),
			new SettingsDefaultSection(),
			new SettingsEnvironmentsSection()
		)
		)->run();
	}

}
