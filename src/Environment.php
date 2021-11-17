<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class Environment {

	public string $url;
	public string $title;
	public string $color;
	public string $background_color;

	public function __construct( string $url, string $title, string $color = '', string $background_color = '' ) {
		$this->url = $url;
		$this->title = $title;
		$this->color = $color;
		$this->background_color = $background_color;
	}

}
