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
 * Resolve the logged-in Guild member's preferred interface language before
 * plugin text domains are loaded. The preference is shared with Tabletop via
 * the same user-meta key so every player may use their own UI language.
 */
add_filter(
    'determine_locale',
    static function (string $locale): string {
        if (! function_exists('get_current_user_id') || ! function_exists('get_user_meta')) {
            return $locale;
        }

        $userId = get_current_user_id();
        if ($userId < 1) {
            return $locale;
        }

        $preferred = (string) get_user_meta($userId, 'gmrc_interface_locale', true);
        if (in_array($preferred, ['en_GB', 'nl_NL', 'de_DE', 'es_ES', 'pt_PT', 'pt_BR'], true)) {
            return $preferred;
        }

        return $locale;
    },
    1
);

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

/** Phase III.M.1: owner-scoped, read-only Pocket Companion API. */
add_action('rest_api_init', static function () use ($app): void {
    $api = new \GreatMarketrealmCompanion\Mobile\PocketApi(
        $app->make(\GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface::class)
    );
    $api->register();
});
