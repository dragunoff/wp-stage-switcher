<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\I18n;

class I18nTest extends TestCase {

	public function test_it_hooks_to_init_when_run(): void {
		( new I18n( __FILE__ ) )->run();
		$this->assertNotFalse( has_action( 'init', '\Drgnff\WP\StageSwitcher\I18n->load_textdomain()' ) );
	}

	public function test_it_loads_plugin_textdomain(): void {
		Functions\expect( 'load_plugin_textdomain' )
		->once()
		->with( 'drgnff-wp-stage-switcher', false, __DIR__ . '/lang/' );

		$i18n = new I18n( __FILE__ );
		$i18n->run();
		$i18n->load_textdomain();
	}

}
