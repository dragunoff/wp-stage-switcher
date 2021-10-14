<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

// phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.voidFound

class TestCase extends FrameworkTestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void {
		Monkey\setUp();

		parent::setUp();

		Functions\stubEscapeFunctions();
		Functions\stubTranslationFunctions();

		Functions\stubs( [ 'sanitize_html_class', 'plugin_basename' ] );

		Functions\when( 'home_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_home_url' )->justReturn( 'https://example.com' );

		Functions\when( 'wp_parse_args' )->alias( 'array_merge' );
		Functions\when( 'wp_parse_url' )->alias( 'parse_url' );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		Mockery::close();

		parent::tearDown();
	}

}
