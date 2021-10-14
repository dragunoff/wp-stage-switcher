<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

spl_autoload_register(
	static function ( string $class_name ): void {
		$namespace_mapping = [
			'Drgnff\\WP\\StageSwitcher' => './',
		];

		foreach ( $namespace_mapping as $namespace => $directory ) {
			$namespace = trim( $namespace, '\\' );
			$directory = realpath( __DIR__ . DIRECTORY_SEPARATOR . trim( $directory, DIRECTORY_SEPARATOR ) );

			if ( 0 !== strpos( $class_name, $namespace ) || ! $directory ) {
				continue;
			}

			$class_file = $directory . str_replace( [ $namespace, '\\' ], [ '', DIRECTORY_SEPARATOR ], $class_name ) . '.php';
			if ( file_exists( $class_file ) ) {
				require_once $class_file;
			}
		}
	}
);
