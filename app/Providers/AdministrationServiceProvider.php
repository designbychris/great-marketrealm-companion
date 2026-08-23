<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalBestiarySteward;
use GreatMarketrealmCompanion\Modules\Administration\Diagnostics\StewardDiagnostics;
use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\Administration\Settings\CompanionSettings;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Repositories\CanonicalBestiary;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Steward's Office administration provider.
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
        $this->app->singleton(CanonicalBestiarySteward::class);
    }

    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_post_gmrc_save_gate_security', [$this, 'saveGateSecurity']);
        add_action('admin_post_gmrc_save_companion_settings', [$this, 'saveCompanionSettings']);
        add_action('admin_post_gmrc_save_canonical_monster', [$this, 'saveCanonicalMonster']);
        add_action('admin_post_gmrc_reset_canonical_monster', [$this, 'resetCanonicalMonster']);
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

        $section = sanitize_key((string) ($_GET['section'] ?? ''));
        if ($section !== 'canonical-records') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'gmrc-canonical-bestiary-steward',
            GMRC_URL . 'assets/js/admin/canonical-bestiary.js',
            ['jquery'],
            GMRC_VERSION,
            true
        );
    }

    public function saveGateSecurity(): void
    {
        $this->guard();
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
        $this->guard();
        check_admin_referer('gmrc_save_companion_settings', 'gmrc_companion_settings_nonce');

        $this->app->make(CompanionSettings::class)->save(
            sanitize_email(wp_unslash((string) ($_POST['steward_email'] ?? ''))),
            ! empty($_POST['show_environment_details'])
        );

        wp_safe_redirect(add_query_arg(['page' => self::MENU_SLUG, 'gmrc_settings_saved' => '1'], admin_url('admin.php')));
        exit;
    }

    public function saveCanonicalMonster(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['monster_key'] ?? '')));
        check_admin_referer('gmrc_save_canonical_monster_' . $key, 'gmrc_canonical_monster_nonce');

        try {
            $this->app->make(CanonicalBestiarySteward::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_canonical_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_canonical_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->canonicalUrl($key, $args));
        exit;
    }

    public function resetCanonicalMonster(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['monster_key'] ?? '')));
        check_admin_referer('gmrc_reset_canonical_monster_' . $key, 'gmrc_canonical_reset_nonce');

        $this->app->make(CanonicalBestiarySteward::class)->reset($key);
        wp_safe_redirect($this->canonicalUrl($key, ['gmrc_canonical_reset' => '1']));
        exit;
    }

    public function renderOffice(): void
    {
        $this->guard(true);

        $section = sanitize_key((string) ($_GET['section'] ?? ''));
        if ($section === 'canonical-records') {
            $steward = $this->app->make(CanonicalBestiarySteward::class);
            $canonicalMonsters = $steward->all();
            $selectedKey = sanitize_key((string) ($_GET['monster'] ?? ''));
            $selectedMonster = $selectedKey !== '' ? $steward->find($selectedKey) : null;
            $selectedOverridden = $selectedMonster ? $steward->hasOverride($selectedMonster->key()) : false;
            require GMRC_PATH . 'app/Modules/Administration/Views/canonical-records.php';
            return;
        }

        $gateSecurity = $this->app->make(GateSecuritySettings::class)->all();
        $gateSecurityConfigured = $this->app->make(GateSecuritySettings::class)->configured();
        $companionSettings = $this->app->make(CompanionSettings::class)->all();
        $diagnostics = $this->app->make(StewardDiagnostics::class)->report();
        require GMRC_PATH . 'app/Modules/Administration/Views/stewards-office.php';
    }

    private function guard(bool $render = false): void
    {
        if (current_user_can(self::CAPABILITY)) {
            return;
        }

        wp_die(
            esc_html($render
                ? __('You do not have permission to enter the Steward\'s Office.', 'great-marketrealm-companion')
                : __('Access denied.', 'great-marketrealm-companion')),
            esc_html__('Access denied', 'great-marketrealm-companion'),
            ['response' => 403]
        );
    }

    /** @param array<string,string> $extra */
    private function canonicalUrl(string $key = '', array $extra = []): string
    {
        $args = array_merge([
            'page' => self::MENU_SLUG,
            'section' => 'canonical-records',
        ], $extra);
        if ($key !== '') {
            $args['monster'] = $key;
        }
        return add_query_arg($args, admin_url('admin.php'));
    }
}
