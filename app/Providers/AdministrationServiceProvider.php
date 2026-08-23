<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Modules\Administration\Diagnostics\StewardDiagnostics;
use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\Administration\Settings\CompanionSettings;

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
        $this->app->singleton(GateSecuritySettings::class);
        $this->app->singleton(CompanionSettings::class);
        $this->app->singleton(StewardDiagnostics::class);
    }

    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_post_gmrc_save_gate_security', [$this, 'saveGateSecurity']);
        add_action('admin_post_gmrc_save_companion_settings', [$this, 'saveCompanionSettings']);
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

    public function saveGateSecurity(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Access denied.', 'great-marketrealm-companion'), '', ['response' => 403]);
        }

        check_admin_referer('gmrc_save_gate_security', 'gmrc_gate_security_nonce');

        $settings = $this->app->make(GateSecuritySettings::class);
        if (! empty($_POST['clear_secret'])) {
            $settings->clearSecret();
        } else {
            $settings->save(
                sanitize_text_field(wp_unslash((string) ($_POST['site_key'] ?? ''))),
                sanitize_text_field(wp_unslash((string) ($_POST['secret_key'] ?? ''))),
                ! empty($_POST['protect_registration']),
                ! empty($_POST['protect_login'])
            );
        }

        wp_safe_redirect(add_query_arg(['page' => self::MENU_SLUG, 'gmrc_saved' => '1'], admin_url('admin.php')));
        exit;
    }

    public function saveCompanionSettings(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('Access denied.', 'great-marketrealm-companion'), '', ['response' => 403]);
        }

        check_admin_referer('gmrc_save_companion_settings', 'gmrc_companion_settings_nonce');

        $this->app->make(CompanionSettings::class)->save(
            sanitize_email(wp_unslash((string) ($_POST['steward_email'] ?? ''))),
            ! empty($_POST['show_environment_details'])
        );

        wp_safe_redirect(add_query_arg(['page' => self::MENU_SLUG, 'gmrc_settings_saved' => '1'], admin_url('admin.php')));
        exit;
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

        $gateSecurity = $this->app->make(GateSecuritySettings::class)->all();
        $gateSecurityConfigured = $this->app->make(GateSecuritySettings::class)->configured();
        $companionSettings = $this->app->make(CompanionSettings::class)->all();
        $diagnostics = $this->app->make(StewardDiagnostics::class)->report();
        require GMRC_PATH . 'app/Modules/Administration/Views/stewards-office.php';
    }
}
