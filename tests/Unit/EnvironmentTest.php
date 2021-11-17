<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests\Unit;

use Drgnff\WP\StageSwitcher\Environment;
use Drgnff\WP\StageSwitcher\Tests\Helpers;

class EnvironmentTest extends TestCase {

	private string $random_url;
	private string $random_title;
	private string $random_color;

	public function test_should_be_created_with_required_args(): void {
		$object = new Environment( $this->random_url, $this->random_title );

		$this->assertEquals( $this->random_url, $object->url );
		$this->assertEquals( $this->random_title, $object->title );
		$this->assertEquals( '', $object->color );
		$this->assertEquals( '', $object->background_color );
	}

	public function test_should_be_created_with_optional_args(): void {
		$object = new Environment( $this->random_url, $this->random_title, $this->random_color, $this->random_color );

		$this->assertEquals( $this->random_color, $object->color );
		$this->assertEquals( $this->random_color, $object->background_color );
	}

	protected function setUp(): void {
		parent::setUp();

		$this->random_url = Helpers::random_url();
		$this->random_title = Helpers::random_id();
		$this->random_color = Helpers::random_hex_color();
	}

}
