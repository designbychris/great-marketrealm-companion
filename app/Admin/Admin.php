<?php

namespace GreatMarketRealm\Admin;

class Admin {

	public function register(): void {

		add_menu_page(
			'Great Marketrealm',
			'Great Marketrealm',
			'manage_options',
			'gmrc-dashboard',
			[$this, 'dashboard'],
			'dashicons-store'
		);

	}

}
