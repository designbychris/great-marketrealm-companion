<?php
/**
 * Plugin Name: Great MarketRealm Companion
 * Description: A modular D&D RPG companion platform for the Great MarketRealm setting.
 * Version: 0.2.0-alpha3.2
 * Author: Marketrealm Studios
 * Text Domain: great-marketrealm-companion
 */

if (! defined('ABSPATH')) {
	exit;
}

define('GMRC_VERSION', '0.2.0-alpha2');
define('GMRC_DB_VERSION', 1);
define('GMRC_PLUGIN_FILE', __FILE__);
define('GMRC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GMRC_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GMRC_PLUGIN_DIR . 'app/Core/Loader.php';

$loader = new \GreatMarketRealm\Core\Loader();
$loader->boot();
