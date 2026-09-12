<?php
/**
 * Plugin Name: Great Marketrealm Companion
 * Description: A modular D&D RPG companion platform for the Great Marketrealm setting.
 * Version: 0.3.1-alpha.11.3
 * Author: Marketrealm Studios
 * Text Domain: great-marketrealm-companion
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('GMRC_VERSION', '0.3.1-alpha.11.3');
define('GMRC_PATH', plugin_dir_path(__FILE__));
define('GMRC_URL', plugin_dir_url(__FILE__));
define('GMRC_PLUGIN_FILE', __FILE__);

/**
 * Load Companion interface translations from the bundled language-pack directory.
 *
 * English remains the canonical source language; locale packs can be added without
 * changing application code. MarketRealm rules/lore translations are intentionally
 * kept separate from interface gettext catalogues.
 */
add_action(
    'init',
    static function (): void {
        load_plugin_textdomain(
            'great-marketrealm-companion',
            false,
            dirname(plugin_basename(GMRC_PLUGIN_FILE)) . '/languages'
        );
    },
    1
);

$autoload = GMRC_PATH . 'vendor/autoload.php';

if (! file_exists($autoload)) {
    add_action(
        'admin_notices',
        static function (): void {
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php esc_html_e('Marketrealm Companion:', 'great-marketrealm-companion'); ?></strong>
                    <?php esc_html_e('Composer dependencies are missing. Run', 'great-marketrealm-companion'); ?>
                    <code>composer install</code>
                    <?php esc_html_e('inside the plugin directory.', 'great-marketrealm-companion'); ?>
                </p>
            </div>
            <?php
        }
    );

    return;
}

require $autoload;

$app = new GreatMarketrealmCompanion\Core\Application(
    GMRC_VERSION
);

$app->boot();
