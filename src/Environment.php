<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class Environment {

	public string $title;
	public string $slug;
	public string $color;
	public string $background_color;

	public function __construct( string $title, string $slug, string $color = '', string $background_color = '' ) {
		$this->title = $title;
		$this->slug = $slug;
		$this->color = $color;
		$this->background_color = $background_color;
	}

}
