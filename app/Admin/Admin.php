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

	public function dashboard(): void {

		echo '<div class="wrap">';
		echo '<h1>Great Marketrealm Companion</h1>';
		echo '<p>Version 0.2.0-alpha1</p>';
		echo '</div>';

	}

}
