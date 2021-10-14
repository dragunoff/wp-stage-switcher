<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Drgnff\WP\StageSwitcher\Environment;
use Drgnff\WP\StageSwitcher\Tests\Helpers;

class EnvironmentTest extends TestCase {

	private string $random_title;
	private string $random_slug;
	private string $random_color;

	public function test_it_is_created_with_required_args(): void {
		$object = new Environment( $this->random_title, $this->random_slug );

		$this->assertEquals( $this->random_title, $object->title );
		$this->assertEquals( $this->random_slug, $object->slug );
		$this->assertEquals( '', $object->color );
		$this->assertEquals( '', $object->background_color );
	}

	public function test_it_is_created_with_optional_args(): void {
		$object = new Environment( $this->random_title, $this->random_slug, $this->random_color, $this->random_color );

		$this->assertEquals( $this->random_color, $object->color );
		$this->assertEquals( $this->random_color, $object->background_color );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->random_title = Helpers::random_id();
		$this->random_slug = Helpers::random_id();
		$this->random_color = Helpers::random_hex_color();
	}

}
