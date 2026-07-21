<?php
/**
 * Plugin Name: Great MarketRealm Companion
 * Description: A modular D&D RPG companion platform for the Great MarketRealm setting.
 * Version: 0.3.0-alpha
 * Author: Marketrealm Studios
 */

defined('ABSPATH') || exit;

define('GMRC_VERSION', '0.3.0-alpha');
define('GMRC_PATH', plugin_dir_path(__FILE__));
define('GMRC_URL', plugin_dir_url(__FILE__));

require GMRC_PATH . 'vendor/autoload.php';

$app = new \GreatMarketrealmCompanion\Core\Application(
    GMRC_VERSION
);

$app->boot();
