<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

defined('ABSPATH') || exit;

/**
 * Steward's Office administration provider.
 *
 * Establishes the administrator-only WordPress workspace used by later
 * Companion security and canonical-content management slices.
 */
final class AdministrationServiceProvider extends ServiceProvider
{
    public const CAPABILITY = 'manage_options';
    public const MENU_SLUG = 'gmrc-stewards-office';

    public function register(): void
    {
    }

    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function registerMenu(): void
    {
        add_menu_page(
            "The Steward's Office",
            "Steward's Office",
            self::CAPABILITY,
            self::MENU_SLUG,
            [$this, 'renderOffice'],
            'dashicons-shield-alt',
            58
        );
    }

    public function enqueueAssets(string $hookSuffix): void
    {
        if ($hookSuffix !== 'toplevel_page_' . self::MENU_SLUG) {
            return;
        }

        wp_enqueue_style(
            'gmrc-stewards-office',
            GMRC_URL . 'assets/css/admin.css',
            [],
            GMRC_VERSION
        );
    }

    public function renderOffice(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(
                esc_html__('You do not have permission to enter the Steward\'s Office.', 'great-marketrealm-companion'),
                esc_html__('Access denied', 'great-marketrealm-companion'),
                ['response' => 403]
            );
        }

        require GMRC_PATH . 'app/Modules/Administration/Views/stewards-office.php';
    }
}
