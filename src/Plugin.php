<?php

declare( strict_types = 1 );

namespace Drgnff\WP\StageSwitcher;

class Plugin {

	private I18n $i18n;
	private EnvironmentsManager $env_manager;
	private AdminBar $admin_bar;
	private SettingsPage $settings_page;
	private SettingsDefaultSection $settings_default_section;
	private SettingsEnvironmentsSection $settings_environments_section;

	public function __construct(
		I18n $i18n,
		EnvironmentsManager $env_manager,
		AdminBar $admin_bar,
		SettingsPage $settings_page,
		SettingsDefaultSection $settings_default_section,
		SettingsEnvironmentsSection $settings_environments_section
	) {
		$this->i18n = $i18n;
		$this->env_manager = $env_manager;
		$this->admin_bar = $admin_bar;
		$this->settings_page = $settings_page;
		$this->settings_default_section = $settings_default_section;
		$this->settings_environments_section = $settings_environments_section;
	}

	public function run(): void {
		$this->i18n->run();
		$this->env_manager->run();
		$this->admin_bar->run();
		$this->settings_page->run();
		$this->settings_default_section->run();
		$this->settings_environments_section->run();
	}

}
