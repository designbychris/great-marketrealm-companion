<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalBestiarySteward;
use GreatMarketrealmCompanion\Modules\Administration\Workshop\MonsterWorkshop;
use GreatMarketrealmCompanion\Modules\Administration\Workshop\SpellWorkshop;
use GreatMarketrealmCompanion\Modules\Administration\Workshop\BackgroundWorkshop;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;
use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalCallingRegister;
use GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords\CanonicalBackgroundRegister;
use GreatMarketrealmCompanion\Modules\Administration\Diagnostics\StewardDiagnostics;
use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\Administration\Settings\CompanionSettings;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Services\StartingEquipmentCoverage;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\CanonicalSpellRegister;
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
        $this->app->singleton(MonsterWorkshop::class);
        $this->app->singleton(SpellWorkshop::class);
        $this->app->singleton(BackgroundWorkshop::class);
        $this->app->singleton(CanonicalCallingRegister::class);
        $this->app->singleton(CanonicalBackgroundRegister::class);
        $this->app->singleton(CanonicalSpellRegister::class);
        $this->app->singleton(StartingEquipmentPackageRegister::class);
        $this->app->singleton(StartingEquipmentCoverage::class);
    }

    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_post_gmrc_save_gate_security', [$this, 'saveGateSecurity']);
        add_action('admin_post_gmrc_save_companion_settings', [$this, 'saveCompanionSettings']);
        add_action('admin_post_gmrc_save_canonical_monster', [$this, 'saveCanonicalMonster']);
        add_action('admin_post_gmrc_reset_canonical_monster', [$this, 'resetCanonicalMonster']);
        add_action('admin_post_gmrc_save_steward_monster', [$this, 'saveStewardMonster']);
        add_action('admin_post_gmrc_save_steward_spell', [$this, 'saveStewardSpell']);
        add_action('admin_post_gmrc_save_steward_background', [$this, 'saveStewardBackground']);
        add_action('admin_post_gmrc_save_canonical_calling', [$this, 'saveCanonicalCalling']);
        add_action('admin_post_gmrc_reset_canonical_calling', [$this, 'resetCanonicalCalling']);
        add_action('admin_post_gmrc_save_canonical_background', [$this, 'saveCanonicalBackground']);
        add_action('admin_post_gmrc_reset_canonical_background', [$this, 'resetCanonicalBackground']);
        add_action('admin_post_gmrc_save_canonical_spell', [$this, 'saveCanonicalSpell']);
        add_action('admin_post_gmrc_reset_canonical_spell', [$this, 'resetCanonicalSpell']);
        add_action('admin_post_gmrc_save_starting_equipment_package', [$this, 'saveStartingEquipmentPackage']);
        add_action('admin_post_gmrc_reset_starting_equipment_package', [$this, 'resetStartingEquipmentPackage']);
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
        if ($section === 'canonical-spells') {
            wp_enqueue_script(
                'gmrc-canonical-spell-steward',
                GMRC_URL . 'assets/js/admin/canonical-spells.js',
                [],
                GMRC_VERSION,
                true
            );
            return;
        }
        if ($section === 'background-workshop') {
            $workshop = $this->app->make(BackgroundWorkshop::class);
            $stewardBackgrounds = $workshop->all();
            $selectedKey = sanitize_key((string) ($_GET['background'] ?? ''));
            $selectedBackground = $selectedKey !== '' ? $workshop->find($selectedKey) : null;
            require GMRC_PATH . 'app/Modules/Administration/Views/background-workshop.php';
            return;
        }
        if ($section === 'canonical-backgrounds') {
            wp_enqueue_script(
                'gmrc-canonical-background-steward',
                GMRC_URL . 'assets/js/admin/canonical-backgrounds.js',
                [],
                GMRC_VERSION,
                true
            );
            return;
        }

        if ($section === 'canonical-callings') {
            wp_enqueue_script(
                'gmrc-canonical-calling-steward',
                GMRC_URL . 'assets/js/admin/canonical-callings.js',
                [],
                GMRC_VERSION,
                true
            );
            return;
        }

        if (! in_array($section, ['canonical-records', 'monster-workshop'], true)) {
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


    public function saveStewardMonster(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['monster_key'] ?? '')));
        check_admin_referer('gmrc_save_steward_monster_' . ($key ?: 'new'), 'gmrc_steward_monster_nonce');

        try {
            $key = $this->app->make(MonsterWorkshop::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_workshop_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_workshop_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->workshopUrl($key, $args));
        exit;
    }


    public function saveStewardSpell(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['spell_key'] ?? '')));
        check_admin_referer('gmrc_save_steward_spell_' . ($key ?: 'new'), 'gmrc_steward_spell_nonce');
        try {
            $key = $this->app->make(SpellWorkshop::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_spell_workshop_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_spell_workshop_error' => rawurlencode($exception->getMessage())];
        }
        wp_safe_redirect($this->spellWorkshopUrl($key, $args));
        exit;
    }


    public function saveStewardBackground(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['background_key'] ?? '')));
        check_admin_referer('gmrc_save_steward_background_' . ($key !== '' ? $key : 'new'), 'gmrc_steward_background_nonce');

        try {
            $key = $this->app->make(BackgroundWorkshop::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_background_workshop_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_background_workshop_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->backgroundWorkshopUrl($key, $args));
        exit;
    }

    public function saveCanonicalCalling(): void
    {
        $this->guard();
        $kind = sanitize_key(wp_unslash((string) ($_POST['calling_kind'] ?? '')));
        $key = sanitize_key(wp_unslash((string) ($_POST['calling_key'] ?? '')));
        check_admin_referer('gmrc_save_canonical_calling_' . $kind . '_' . $key, 'gmrc_canonical_calling_nonce');

        try {
            $this->app->make(CanonicalCallingRegister::class)->save($kind, $key, wp_unslash($_POST));
            $args = ['gmrc_calling_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_calling_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->callingUrl($kind, $key, $args));
        exit;
    }

    public function resetCanonicalCalling(): void
    {
        $this->guard();
        $kind = sanitize_key(wp_unslash((string) ($_POST['calling_kind'] ?? '')));
        $key = sanitize_key(wp_unslash((string) ($_POST['calling_key'] ?? '')));
        check_admin_referer('gmrc_reset_canonical_calling_' . $kind . '_' . $key, 'gmrc_canonical_calling_reset_nonce');
        $this->app->make(CanonicalCallingRegister::class)->reset($kind, $key);
        wp_safe_redirect($this->callingUrl($kind, $key, ['gmrc_calling_reset' => '1']));
        exit;
    }

    public function saveCanonicalSpell(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['spell_key'] ?? '')));
        check_admin_referer('gmrc_save_canonical_spell_' . $key, 'gmrc_canonical_spell_nonce');

        try {
            $this->app->make(CanonicalSpellRegister::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_spell_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_spell_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->spellUrl($key, $args));
        exit;
    }

    public function resetCanonicalSpell(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['spell_key'] ?? '')));
        check_admin_referer('gmrc_reset_canonical_spell_' . $key, 'gmrc_canonical_spell_reset_nonce');
        $this->app->make(CanonicalSpellRegister::class)->reset($key);
        wp_safe_redirect($this->spellUrl($key, ['gmrc_spell_reset' => '1']));
        exit;
    }

    public function saveCanonicalBackground(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['background_key'] ?? '')));
        check_admin_referer('gmrc_save_canonical_background_' . $key, 'gmrc_canonical_background_nonce');

        try {
            $this->app->make(CanonicalBackgroundRegister::class)->save($key, wp_unslash($_POST));
            $args = ['gmrc_background_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_background_error' => rawurlencode($exception->getMessage())];
        }

        wp_safe_redirect($this->backgroundUrl($key, $args));
        exit;
    }

    public function resetCanonicalBackground(): void
    {
        $this->guard();
        $key = sanitize_key(wp_unslash((string) ($_POST['background_key'] ?? '')));
        check_admin_referer('gmrc_reset_canonical_background_' . $key, 'gmrc_canonical_background_reset_nonce');
        $this->app->make(CanonicalBackgroundRegister::class)->reset($key);
        wp_safe_redirect($this->backgroundUrl($key, ['gmrc_background_reset' => '1']));
        exit;
    }


    public function saveStartingEquipmentPackage(): void
    {
        $this->guard();
        $id = sanitize_key(wp_unslash((string) ($_POST['package_id'] ?? '')));
        check_admin_referer('gmrc_save_starting_equipment_package_' . $id, 'gmrc_starting_equipment_nonce');
        try {
            $this->app->make(StartingEquipmentPackageRegister::class)->save($id, wp_unslash($_POST));
            $args = ['gmrc_equipment_saved' => '1'];
        } catch (RuntimeException $exception) {
            $args = ['gmrc_equipment_error' => rawurlencode($exception->getMessage())];
        }
        wp_safe_redirect($this->startingEquipmentUrl($id, $args));
        exit;
    }

    public function resetStartingEquipmentPackage(): void
    {
        $this->guard();
        $id = sanitize_key(wp_unslash((string) ($_POST['package_id'] ?? '')));
        check_admin_referer('gmrc_reset_starting_equipment_package_' . $id, 'gmrc_starting_equipment_reset_nonce');
        $this->app->make(StartingEquipmentPackageRegister::class)->reset($id);
        wp_safe_redirect($this->startingEquipmentUrl($id, ['gmrc_equipment_reset' => '1']));
        exit;
    }

    public function renderOffice(): void
    {
        $this->guard(true);

        $section = sanitize_key((string) ($_GET['section'] ?? ''));
        if ($section === 'starting-equipment') {
            $register = $this->app->make(StartingEquipmentPackageRegister::class);
            $startingEquipmentPackages = $register->all();
            $startingEquipmentCoverage = $this->app->make(StartingEquipmentCoverage::class)->report();
            $selectedId = sanitize_key((string) ($_GET['package'] ?? ''));
            $selectedPackage = $selectedId !== '' ? $register->find($selectedId) : null;
            $selectedPackageOverridden = $selectedPackage ? $register->hasOverride($selectedPackage->id()) : false;
            require GMRC_PATH . 'app/Modules/Administration/Views/starting-equipment.php';
            return;
        }
        if ($section === 'spell-workshop') {
            $workshop = $this->app->make(SpellWorkshop::class);
            $stewardSpells = $workshop->all();
            $selectedKey = sanitize_key((string) ($_GET['spell'] ?? ''));
            $selectedSpell = $selectedKey !== '' ? $workshop->find($selectedKey) : null;
            require GMRC_PATH . 'app/Modules/Administration/Views/spell-workshop.php';
            return;
        }
        if ($section === 'canonical-spells') {
            $register = $this->app->make(CanonicalSpellRegister::class);
            $spells = $register->all();
            $selectedKey = sanitize_key((string) ($_GET['spell'] ?? ''));
            $selectedSpell = $selectedKey !== '' ? $register->find($selectedKey) : null;
            $selectedSpellOverridden = $selectedSpell ? $register->hasOverride($selectedSpell) : false;
            $selectedSpellNotes = $selectedSpell ? $register->stewardNotes($selectedSpell) : '';
            require GMRC_PATH . 'app/Modules/Administration/Views/canonical-spells.php';
            return;
        }
        if ($section === 'background-workshop') {
            $workshop = $this->app->make(BackgroundWorkshop::class);
            $stewardBackgrounds = $workshop->all();
            $selectedKey = sanitize_key((string) ($_GET['background'] ?? ''));
            $selectedBackground = $selectedKey !== '' ? $workshop->find($selectedKey) : null;
            require GMRC_PATH . 'app/Modules/Administration/Views/background-workshop.php';
            return;
        }
        if ($section === 'canonical-backgrounds') {
            $register = $this->app->make(CanonicalBackgroundRegister::class);
            $backgrounds = $register->all();
            $selectedKey = sanitize_key((string) ($_GET['background'] ?? ''));
            $selectedBackground = $selectedKey !== '' ? $register->find($selectedKey) : null;
            $selectedBackgroundOverridden = $selectedBackground ? $register->hasOverride($selectedBackground) : false;
            require GMRC_PATH . 'app/Modules/Administration/Views/canonical-backgrounds.php';
            return;
        }

        if ($section === 'canonical-callings') {
            $register = $this->app->make(CanonicalCallingRegister::class);
            $callings = $register->all();
            $selectedKind = sanitize_key((string) ($_GET['kind'] ?? ''));
            $selectedKey = sanitize_key((string) ($_GET['calling'] ?? ''));
            $selectedCalling = ($selectedKind !== '' && $selectedKey !== '') ? $register->find($selectedKind, $selectedKey) : null;
            $selectedCallingOverridden = $selectedCalling ? $register->hasOverride($selectedCalling) : false;
            require GMRC_PATH . 'app/Modules/Administration/Views/canonical-callings.php';
            return;
        }

        if ($section === 'monster-workshop') {
            $workshop = $this->app->make(MonsterWorkshop::class);
            $stewardMonsters = $workshop->all();
            $selectedKey = sanitize_key((string) ($_GET['monster'] ?? ''));
            $selectedData = $selectedKey !== '' ? $workshop->find($selectedKey) : null;
            $selectedMonster = is_array($selectedData) ? new CanonicalMonster($selectedData) : null;
            require GMRC_PATH . 'app/Modules/Administration/Views/monster-workshop.php';
            return;
        }

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
    private function startingEquipmentUrl(string $id, array $extra = []): string
    {
        return add_query_arg(array_merge([
            'page' => self::MENU_SLUG,
            'section' => 'starting-equipment',
            'package' => $id,
        ], $extra), admin_url('admin.php'));
    }

    /** @param array<string,string> $extra */
    /** @param array<string,string> $args */
    private function spellWorkshopUrl(string $key = '', array $args = []): string
    {
        $base = ['page' => self::MENU_SLUG, 'section' => 'spell-workshop'];
        if ($key !== '') $base['spell'] = $key;
        return add_query_arg(array_merge($base, $args), admin_url('admin.php'));
    }

    private function spellUrl(string $key, array $extra = []): string
    {
        return add_query_arg(array_merge([
            'page' => self::MENU_SLUG,
            'section' => 'canonical-spells',
            'spell' => $key,
        ], $extra), admin_url('admin.php'));
    }

    /** @param array<string,string> $extra */
    private function backgroundWorkshopUrl(string $key = '', array $args = []): string
    {
        $base = ['page' => self::MENU_SLUG, 'section' => 'background-workshop'];
        if ($key !== '') {
            $base['background'] = $key;
        }
        return add_query_arg(array_merge($base, $args), admin_url('admin.php'));
    }

    private function backgroundUrl(string $key, array $extra = []): string
    {
        return add_query_arg(array_merge([
            'page' => self::MENU_SLUG,
            'section' => 'canonical-backgrounds',
            'background' => $key,
        ], $extra), admin_url('admin.php'));
    }

    /** @param array<string,string> $extra */
    private function callingUrl(string $kind, string $key, array $extra = []): string
    {
        return add_query_arg(array_merge([
            'page' => self::MENU_SLUG,
            'section' => 'canonical-callings',
            'kind' => $kind,
            'calling' => $key,
        ], $extra), admin_url('admin.php'));
    }

    /** @param array<string,string> $extra */
    private function workshopUrl(string $key = '', array $extra = []): string
    {
        $args = array_merge(['page' => self::MENU_SLUG, 'section' => 'monster-workshop'], $extra);
        if ($key !== '') $args['monster'] = $key;
        return add_query_arg($args, admin_url('admin.php'));
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
