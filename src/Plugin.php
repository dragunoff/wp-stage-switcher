<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class Plugin {

	private I18n $i18n;
	private EnvironmentsManager $env_manager;
	private AdminBar $admin_bar;

	public function __construct( I18n $i18n, EnvironmentsManager $env_manager, AdminBar $admin_bar ) {
		$this->i18n = $i18n;
		$this->env_manager = $env_manager;
		$this->admin_bar = $admin_bar;
	}

	public function run(): void {
		$this->i18n->run();
		$this->env_manager->run();
		$this->admin_bar->run();
	}

}
