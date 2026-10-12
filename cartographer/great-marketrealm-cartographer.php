<?php
/**
 * Plugin Name: Great MarketRealm Cartographer
 * Description: Optional Dungeon Master cartography and construction workshop.
 * Version: 0.1.0-alpha.1
 * Author: Christopher Mitchell
 * Text Domain: great-marketrealm-cartographer
 * Domain Path: /languages
 * Requires PHP: 8.1
 */
declare(strict_types=1);
defined('ABSPATH') || exit;
define('GMRCART_VERSION', '0.1.0-alpha.1');
define('GMRCART_PATH', plugin_dir_path(__FILE__));
require_once GMRCART_PATH . 'src/Workshop.php';
add_action('init', static function (): void {
    load_plugin_textdomain('great-marketrealm-cartographer', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
add_action('plugins_loaded', static function (): void {
    \GreatMarketrealmCartographer\Workshop::boot();
}, 30);

/** Public read-only bridge for the Companion campaign dashboard. */
function gmrcart_campaign_access(int $userId, string $campaignId): bool
{
    if ($userId < 1 || $campaignId === '' || !class_exists('GreatMarketrealmCartographer\CompanionBridge')) return false;
    return \GreatMarketrealmCartographer\CompanionBridge::canEditCampaign(false, $userId, $campaignId);
}
function gmrcart_workshop_url(string $campaignId): string
{
    return add_query_arg('campaign_id', $campaignId, admin_url('admin.php?page=gmrcart-workshop'));
}
