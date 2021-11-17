<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher\Tests;

class Helpers {

	public static function random_hex_color(): string {
		return '#' . dechex( rand( 0, 255 ) ) . dechex( rand( 0, 255 ) ) . dechex( rand( 0, 255 ) );
	}

	public static function random_id(): string {
		return 'test-' . rand();
	}

	public static function random_url(): string {
		return 'https://test-' . rand() . '.localhost';
	}

	public static function strip_whitespace( string $str ): string {
		$whitespace = [
			"\n",
			"\r",
			"\t",
		];

		return str_replace( $whitespace, '', $str );
	}

}
