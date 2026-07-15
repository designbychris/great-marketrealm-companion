<?php

namespace GreatMarketRealm\Frontend;

class Shortcodes {

	public function register(): void {

		add_shortcode(
			'gmr_dashboard',
			[$this, 'dashboard']
		);

	}

	public function dashboard(): string {

		ob_start();

		?>

		<div class="gmrc-dashboard">

			<h2>Great Marketrealm</h2>

			<p>Character dashboard coming soon.</p>

		</div>

		<?php

		return ob_get_clean();

	}

}
