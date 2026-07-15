<?php

namespace GreatMarketRealm\Core;

use GreatMarketRealm\Admin\Admin;
use GreatMarketRealm\Frontend\Shortcodes;

class Plugin {

	public function init(): void {

		add_action('admin_menu', [$this, 'register_admin']);

		add_action('wp_enqueue_scripts', [$this, 'frontend_assets']);

		add_action('admin_enqueue_scripts', [$this, 'admin_assets']);

		add_action('init', [$this, 'register_shortcodes']);

	}

	public function register_admin(): void {

		$admin = new Admin();
		$admin->register();

	}

	public function register_shortcodes(): void {

		$shortcodes = new Shortcodes();
		$shortcodes->register();

	}

	public function frontend_assets(): void {

		wp_enqueue_style(
			'gmrc-frontend',
			GMRC_PLUGIN_URL . 'assets/css/frontend.css',
			[],
			GMRC_VERSION
		);

	}

	public function admin_assets(): void {

		wp_enqueue_style(
			'gmrc-admin',
			GMRC_PLUGIN_URL . 'assets/css/admin.css',
			[],
			GMRC_VERSION
		);

	}

}
