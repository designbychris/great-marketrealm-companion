<?php

namespace GreatMarketRealm\Core;

if (! defined('ABSPATH')) {
	exit;
}

class Loader {

	public function boot(): void {

		spl_autoload_register([$this, 'autoload']);

		$plugin = new Plugin();
		$plugin->init();

	}

	public function autoload(string $class): void {

		$prefix = 'GreatMarketRealm\\';

		if (strpos($class, $prefix) !== 0) {
			return;
		}

		$class = str_replace($prefix, '', $class);
		$class = str_replace('\\', '/', $class);

		$file = GMRC_PLUGIN_DIR . 'app/' . $class . '.php';

		if (file_exists($file)) {
			require_once $file;
		}

	}
}
