<?php
/**
 * Plugin Name: Great Marketrealm Companion
 * Description: The official companion plugin for the Great Marketrealm campaign setting.
 * Version: 0.2.0-alpha1
 * Author: Chris Mitchell (Design by Chris)
 * Text Domain: great-marketrealm-companion
 */

if (! defined('ABSPATH')) {
	exit;
}

define('GMRC_VERSION', '0.2.0-alpha1');
define('GMRC_PLUGIN_FILE', __FILE__);
define('GMRC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GMRC_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GMRC_PLUGIN_DIR . 'app/Core/Loader.php';

$loader = new \GreatMarketRealm\Core\Loader();
$loader->boot();
