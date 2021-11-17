<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Drgnff\WP\StageSwitcher\EnvironmentsManager;
use Drgnff\WP\StageSwitcher\Tests\Helpers;
use Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class EnvironmentsManagerTest extends TestCase {

	/** @var array<\ArrayObject> */
	private array $random_envs;

	public function test_should_return_current_environment(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 3 );
		$this->assertSame( $object->get_current_environment()->url, untrailingslashit( $this->random_envs[2]['url'] ) );
		$this->assertSame( $object->get_current_environment()->title, $this->random_envs[2]['title'] );
		$this->assertSame( $object->get_current_environment()->color, $this->random_envs[2]['color'] );
		$this->assertSame( $object->get_current_environment()->background_color, $this->random_envs[2]['background_color'] );
	}

	public function test_should_get_default_environment_from_constants(): void {
		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 1 );
		$this->assertSame( $object->get_current_environment()->url, untrailingslashit( $this->random_envs[2]['url'] ) );
		$this->assertSame( $object->get_current_environment()->title, $this->random_envs[2]['title'] );
		$this->assertSame( $object->get_current_environment()->color, $this->random_envs[2]['color'] );
		$this->assertSame( $object->get_current_environment()->background_color, $this->random_envs[2]['background_color'] );
	}

	public function test_should_check_if_current_environment_is_known(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => home_url(),
					'title' => 'LOCAL ENV',
				],
			]
		);
		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 1 );
		$this->assertFalse( $object->is_current_environment_unknown() );
	}

	public function test_should_check_if_current_environment_is_unknown(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => Helpers::random_url(),
					'title' => 'REMOTE ENV',
				],
			]
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 2 );
		$this->assertTrue( $object->is_current_environment_unknown() );
	}

	public function test_should_check_when_styles_are_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => home_url(),
					'title' => 'STYLES',
					'color' => '#ffffff',
					'background_color' => '#ff0000',
				],
			]
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertTrue( $object->needs_styles() );
	}

	public function test_should_check_when_color_styles_are_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => home_url(),
					'title' => 'STYLES',
					'color' => '#ffffff',
				],
			]
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertTrue( $object->needs_styles() );
	}

	public function test_should_check_when_background_color_styles_are_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => home_url(),
					'title' => 'STYLES',
					'background_color' => '#ff0000',
				],
			]
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertTrue( $object->needs_styles() );
	}

	public function test_should_check_when_styles_are_not_needed(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->andReturn(
			[
				[
					'url' => home_url(),
					'title' => 'NO STYLES',
				],
			]
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertFalse( $object->needs_styles() );
	}

	public function test_should_parse_multisite_subdir_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://example.com/subdir' );
		Functions\when( 'get_home_url' )->justReturn( 'http://example.com/subdir' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( $object->get_environments()[0]->url, untrailingslashit( $this->random_envs[0]['url'] ) . '/subdir' );
	}

	public function test_should_parse_multisite_subdomain_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'is_subdomain_install' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://subdomain.example.com' );
		Functions\when( 'get_home_url' )->justReturn( 'http://subdomain.example.com' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$hostname = wp_parse_url( $object->get_environments()[0]->url, PHP_URL_HOST );
		$this->assertSame( 0, strpos( $hostname, 'subdomain.' ) );
	}

	public function test_should_parse_multisite_subdomain_mainsite_url(): void {
		Functions\when( 'is_multisite' )->justReturn( true );
		Functions\when( 'is_subdomain_install' )->justReturn( true );
		Functions\when( 'is_main_site' )->justReturn( true );
		Functions\when( 'home_url' )->justReturn( 'http://example.com' );
		Functions\when( 'get_home_url' )->justReturn( 'http://example.com' );

		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( $object->get_environments()[0]->url, untrailingslashit( $this->random_envs[0]['url'] ) );
	}

	public function test_should_get_envs_from_options(): void {
		$envs = $this->random_envs;
		Functions\when( 'get_option' )
		->alias(
			// phpcs:ignore NeutronStandard.Functions.TypeHint
			// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
			static function( $option_name ) use ( $envs ) {
				if ( 'drgnff_wp_stage_switcher__environments' !== $option_name ) {
					return;
				}

				return $envs;
			}
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 3 );
		$this->assertSame( $object->get_environments()[0]->url, untrailingslashit( $this->random_envs[0]['url'] ) );
		$this->assertSame( $object->get_environments()[0]->title, $this->random_envs[0]['title'] );
		$this->assertSame( $object->get_environments()[1]->url, untrailingslashit( $this->random_envs[1]['url'] ) );
		$this->assertSame( $object->get_environments()[1]->title, $this->random_envs[1]['title'] );
	}

	public function test_should_not_get_envs_from_empty_options(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( [ $this->random_envs[1] ] );

		Functions\when( 'get_option' )
		->alias(
			// phpcs:ignore NeutronStandard.Functions.TypeHint
			// phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
			static function( $option_name ) {
				if ( 'drgnff_wp_stage_switcher__environments' !== $option_name ) {
					return;
				}

				return '';
			}
		);

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 2 );
		$this->assertSame( $object->get_environments()[0]->url, untrailingslashit( $this->random_envs[1]['url'] ) );
		$this->assertSame( $object->get_environments()[0]->title, $this->random_envs[1]['title'] );
	}

	public function test_should_apply_envs_filter(): void {
		Filters\expectApplied( 'drgnff_wp_stage_switcher__environments' )
		->once()
		->andReturn( $this->random_envs );

		$object = new EnvironmentsManager( new $this->constants() );
		$object->run();

		$this->assertSame( count( $object->get_environments() ), 3 );
		$this->assertSame( $object->get_environments()[0]->url, untrailingslashit( $this->random_envs[0]['url'] ) );
		$this->assertSame( $object->get_environments()[0]->title, $this->random_envs[0]['title'] );
		$this->assertSame( $object->get_environments()[1]->url, untrailingslashit( $this->random_envs[1]['url'] ) );
		$this->assertSame( $object->get_environments()[1]->title, $this->random_envs[1]['title'] );
	}

	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'is_multisite' )->justReturn( false );
		Functions\when( 'is_subdomain_install' )->justReturn( false );
		Functions\when( 'is_main_site' )->justReturn();

		$this->random_envs = [
			[
				'url' => 'https://localhost/',
				'title' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
			[
				'url' => 'https://example.net',
				'title' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
			[
				'url' => home_url(),
				'title' => Helpers::random_id(),
				'color' => Helpers::random_hex_color(),
				'background_color' => Helpers::random_hex_color(),
			],
		];

		$this->constants = m::mock( 'overload:Drgnff\WP\StageSwitcher\Constants' )
		->shouldReceive( 'get' )
			->with( 'default_environment' )
			->andReturn( $this->random_envs[2] )
		->getMock();

		Functions\when( 'get_option' )->justReturn();
	}

}
