<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Drgnff\WP\StageSwitcher\AdminBar;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\I18n;
use Drgnff\WP\StageSwitcher\Plugin;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PluginTest extends TestCase {

	public function test_it_runs_dependent_modules(): void {
		$i18n = m::mock( 'overload:Drgnff\WP\StageSwitcher\I18n' );
		$env_manager = m::mock( 'overload:Drgnff\WP\StageSwitcher\EnvironmentsManager' );
		$admin_bar = m::mock( 'overload:Drgnff\WP\StageSwitcher\AdminBar' );

		$i18n->shouldReceive( 'run' )->once();
		$env_manager->shouldReceive( 'run' )->once();
		$admin_bar->shouldReceive( 'run' )->once();

		( new Plugin(
			new I18n(),
			new EnvironmentsManager(),
			new AdminBar()
		)
		)->run();
	}

}
