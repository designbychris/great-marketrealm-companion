<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Http\Controllers\AppController;
use GreatMarketrealmCompanion\Core\Http\Response;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Front-end Service Provider.
 *
 * Connects WordPress to the Marketrealm Companion application.
 * WordPress-specific hooks remain here, while application request
 * handling is delegated to the AppController.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register front-end services.
     */
    public function register(): void
    {
        // No manual controller bindings required.
    }

    /**
     * Register WordPress front-end hooks.
     */
    public function boot(): void
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueueAssets']
        );
    
        add_shortcode(
            'gmrc_app',
            [$this, 'renderApp']
        );

        add_action(
            'admin_post_gmrc_app_request',
            [$this, 'handleApplicationRequest']
        );
    
        add_action(
            'admin_post_nopriv_gmrc_app_request',
            [$this, 'handleApplicationRequest']
        );
    }

    /**
     * Handle a Companion application form request.
     */
    public function handleApplicationRequest(): void
    {
        $method = strtoupper(
            $_SERVER['REQUEST_METHOD'] ?? 'GET'
        );
    
        if ($method !== 'POST') {
            wp_safe_redirect(
                home_url('/companion/')
            );
    
            exit;
        }
    
        $route = isset($_POST['gmrc_route'])
            && is_scalar($_POST['gmrc_route'])
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['gmrc_route']
                    )
                )
                : '';
    
        $methodOverride = isset($_POST['_method'])
            && is_scalar($_POST['_method'])
                ? strtoupper(
                    sanitize_text_field(
                        wp_unslash(
                            (string) $_POST['_method']
                        )
                    )
                )
                : 'POST';
    
        $submittedNonce = isset($_POST['gmrc_nonce'])
            && is_scalar($_POST['gmrc_nonce'])
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['gmrc_nonce']
                    )
                )
                : '';
    
        $nonceAction = $this->nonceActionForRequest(
            $methodOverride,
            $route
        );
    
        if (
            $submittedNonce === ''
            || $nonceAction === null
            || ! wp_verify_nonce(
                $submittedNonce,
                $nonceAction
            )
        ) {
            wp_die(
                esc_html__(
                    'The form request could not be verified.',
                    'great-marketrealm-companion'
                ),
                esc_html__(
                    'Invalid request',
                    'great-marketrealm-companion'
                ),
                [
                    'response' => 403,
                ]
            );
        }
    
        $result = $this->app
            ->make(AppController::class)
            ->handle();
    
        if ($result instanceof Response) {
            $result->send();
            exit;
        }
    
        echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }

    /**
     * Determine the expected nonce action for an application request.
     */
    private function nonceActionForRequest(
        string $method,
        string $route
    ): ?string {
        $method = strtoupper(
            trim($method)
        );
    
        $route = trim(
            $route,
            '/'
        );
    
        if (
            $method === 'POST'
            && $route === 'characters'
        ) {
            return 'gmrc_create_character';
        }
    
        if (
            $method === 'PUT'
            && preg_match(
                '#^characters/([^/]+)$#',
                $route,
                $matches
            )
        ) {
            return 'gmrc_update_character_'
                . sanitize_text_field(
                    $matches[1]
                );
        }
    
        if (
            $method === 'DELETE'
            && preg_match(
                '#^characters/([^/]+)$#',
                $route,
                $matches
            )
        ) {
            return 'gmrc_delete_character_'
                . sanitize_text_field(
                    $matches[1]
                );
        }
    
        return null;
    }
    
    /**
     * Load the Companion application assets.
     */
    public function enqueueAssets(): void
    {
        if (! $this->isCompanionPage()) {
            return;
        }
    
        $this->enqueueFoundation();
        $this->enqueueFonts();
        $this->enqueueComponents();
        $this->enqueueScripts();
        $this->enqueueTheme();

    }

    protected function enqueueFoundation(): void
    {
        $styles = [
            'guild-tokens',
            'scribe-typography',
            'parchment',
            'ledger-motion',
            'guild-ornaments',
        ];
    
        $dependencies = [];
    
        foreach ($styles as $style) {
    
            $handle = 'gmrc-' . $style;
    
            wp_enqueue_style(
                $handle,
                GMRC_URL . 'assets/css/foundation/' . $style . '.css',
                $dependencies,
                GMRC_VERSION
            );
    
            $dependencies = [$handle];
        }
    }

    protected function enqueueFonts(): void
    {
        wp_enqueue_style(
            'gmrc-caveat',
            'https://fonts.googleapis.com/css2?family=Caveat:wght@500;600;700&display=swap',
            [],
            null
        );
    }

    protected function enqueueComponents(): void
    {
        $components = [
            [
                'handle' => 'gmrc-margin-note',
                'path'   => 'components/furniture/margin-note.css',
            ],
            [
                'handle' => 'gmrc-guild-seal',
                'path'   => 'components/media/guild-seal.css',
            ],
            [
                'handle' => 'gmrc-illuminated-portrait',
                'path' =>
                    'components/media/'
                    . 'illuminated-portrait.css',
            ],
            [
                'handle' => 'gmrc-ledger-ribbon',
                'path'   => 'components/furniture/ledger-ribbon.css',
            ],
            [
                'handle' => 'gmrc-scribe-input',
                'path'   => 'components/controls/scribe-input.css',
            ],
            [
                'handle' => 'gmrc-parchment-select',
                'path'   => 'components/controls/parchment-select.css',
            ],
            [
                'handle' => 'gmrc-ink-checkbox',
                'path'   => 'components/controls/ink-checkbox.css',
            ],
            [
                'handle' => 'gmrc-character-inscription-form',
                'path'   => 'modules/characters/character-inscription-form.css',
            ],
            [
                'handle' => 'gmrc-final-farewell',
                'path' => 'modules/characters/final-farewell.css',
            ],
            [
                'handle' => 'gmrc-open-ledger',
                'path' => 'modules/characters/open-ledger.css',
            ],
            [
                'handle' => 'gmrc-complete-registration',
                'path' => 'modules/characters/complete-registration.css',
            ],
            [
                'handle' => 'gmrc-registrars-finishing-touches',
                'path' => 'modules/characters/registrars-finishing-touches.css',
            ],
            [
                'handle' => 'gmrc-background-selector',
                'path' => 'modules/characters/background-selector.css',
            ],
            [
                'handle' => 'gmrc-choice-selector',
                'path' => 'modules/characters/choice-selector.css',
            ],
            [
                'handle' => 'gmrc-wax-button',
                'path'   => 'components/controls/wax-button.css',
            ],
            [
                'handle' => 'gmrc-paper-button',
                'path'   => 'components/controls/paper-button.css',
            ],
            [
                'handle' => 'gmrc-character-creation-preview',
                'path' =>   'modules/characters/character-creation-preview.css',
            ],
            [
                'handle' => 'gmrc-character-creation-preview',
                'path' =>   'components/media/portrait-studio-controls.css',
            ],
            [
                'handle' => 'gmrc-auby-note',
                'path' => 'components/furniture/auby-note.css',
            ],
            [
                'handle' => 'gmrc-auby-seal-of-approval',
                'path' =>
                    'components/auby/'
                    . 'seal-of-approval.css',
            ],
            [
                'handle' => 'gmrc-auby-sticky-note',
                'path' =>
                    'components/auby/'
                    . 'sticky-note.css',
            ],
            [
                'handle' => 'gmrc-auby-desk',
                'path' =>
                    'components/guild-hall/'
                    . 'auby-desk.css',
            ],
            [
                'handle' => 'gmrc-guild-hall-dashboard',
                'path' =>
                    'modules/dashboard/'
                    . 'guild-hall-dashboard.css',
            ],
            [
                'handle' => 'gmrc-registrar',
                'path' => 'components/furniture/registrar.css',
            ],
            [
                'handle' => 'gmrc-registrars-desk-reveal',
                'path' =>
                    'components/furniture/'
                    . 'registrars-desk-reveal.css',
            ],
        ];
    
        foreach ($components as $component) {
            wp_enqueue_style(
                $component['handle'],
                GMRC_URL
                    . 'assets/css/'
                    . $component['path'],
                [
                    'gmrc-guild-ornaments',
                ],
                GMRC_VERSION
            );
        }
    }

    /**
     * Load Companion application scripts.
     */
    protected function enqueueScripts(): void
    {
        $livingLedgerScriptPath =
            GMRC_PATH
            . 'assets/js/modules/characters/'
            . 'living-ledger.js';

        wp_enqueue_script(
            'gmrc-living-ledger',
            GMRC_URL
                . 'assets/js/modules/characters/'
                . 'living-ledger.js',
            [],
            file_exists($livingLedgerScriptPath)
                ? (string) filemtime($livingLedgerScriptPath)
                : GMRC_VERSION,
            true
        );

        $completeRegistrationScriptPath =
            GMRC_PATH
            . 'assets/js/modules/characters/'
            . 'complete-registration.js';

        wp_enqueue_script(
            'gmrc-complete-registration',
            GMRC_URL
                . 'assets/js/modules/characters/'
                . 'complete-registration.js',
            [],
            file_exists(
                $completeRegistrationScriptPath
            )
                ? (string) filemtime(
                    $completeRegistrationScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $registrarScriptPath =
            GMRC_PATH
            . 'assets/js/components/furniture/registrar.js';
        
        wp_enqueue_script(
            'gmrc-registrar',
            GMRC_URL
                . 'assets/js/components/furniture/registrar.js',
            [],
            file_exists($registrarScriptPath)
                ? (string) filemtime(
                    $registrarScriptPath
                )
                : GMRC_VERSION,
            true
        );
        wp_enqueue_script(
            'gmrc-background-selector',
            GMRC_URL
                . 'assets/js/modules/characters/background-selector.js',
            [],
            GMRC_VERSION,
            true
        );
        wp_enqueue_script(
            'gmrc-choice-selector',
            GMRC_URL
                . 'assets/js/modules/characters/choice-selector.js',
            [],
            GMRC_VERSION,
            true
        );
        $previewScriptPath =
            GMRC_PATH
            . 'assets/js/modules/characters/character-creation-preview.js';
        
        wp_enqueue_script(
            'gmrc-character-creation-preview',
            GMRC_URL
                . 'assets/js/modules/characters/character-creation-preview.js',
            [
                'gmrc-registrar',
            ],
            file_exists($previewScriptPath)
                ? (string) filemtime(
                    $previewScriptPath
                )
                : GMRC_VERSION,
            true
        );
        wp_enqueue_script(
            'gmrc-auby-note',
            GMRC_URL
                . 'assets/js/components/furniture/auby-note.js',
            [],
            GMRC_VERSION,
            true
        );

        $aubySealScriptPath =
            GMRC_PATH
            . 'assets/js/components/auby/'
            . 'seal-of-approval.js';

        wp_enqueue_script(
            'gmrc-auby-seal-of-approval',
            GMRC_URL
                . 'assets/js/components/auby/'
                . 'seal-of-approval.js',
            [],
            file_exists($aubySealScriptPath)
                ? (string) filemtime(
                    $aubySealScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $aubyStickyNoteScriptPath =
            GMRC_PATH
            . 'assets/js/components/auby/'
            . 'sticky-note.js';

        wp_enqueue_script(
            'gmrc-auby-sticky-note',
            GMRC_URL
                . 'assets/js/components/auby/'
                . 'sticky-note.js',
            [],
            file_exists($aubyStickyNoteScriptPath)
                ? (string) filemtime(
                    $aubyStickyNoteScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $aubyDeskScriptPath =
            GMRC_PATH
            . 'assets/js/components/guild-hall/'
            . 'auby-desk.js';

        wp_enqueue_script(
            'gmrc-auby-desk',
            GMRC_URL
                . 'assets/js/components/guild-hall/'
                . 'auby-desk.js',
            ['gmrc-auby-sticky-note'],
            file_exists($aubyDeskScriptPath)
                ? (string) filemtime(
                    $aubyDeskScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $livingGuildScriptPath =
            GMRC_PATH
            . 'assets/js/components/guild-hall/'
            . 'living-guild.js';

        wp_enqueue_script(
            'gmrc-living-guild',
            GMRC_URL
                . 'assets/js/components/guild-hall/'
                . 'living-guild.js',
            ['gmrc-auby-desk'],
            file_exists($livingGuildScriptPath)
                ? (string) filemtime(
                    $livingGuildScriptPath
                )
                : GMRC_VERSION,
            true
        );
        $portraitStudioScriptPath =
            GMRC_PATH
            . 'assets/js/components/media/portrait-studio.js';
        
        wp_enqueue_script(
            'gmrc-portrait-studio',
            GMRC_URL
                . 'assets/js/components/media/portrait-studio.js',
            [],
            file_exists($portraitStudioScriptPath)
                ? (string) filemtime(
                    $portraitStudioScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $portraitStudioModules = [
            'namespace',
            'state',
            'variants',
            'layer-updater',
            'randomiser',
            'controls',
            'app',
        ];
        
        $previousHandle = 'gmrc-portrait-studio';
        
        foreach ($portraitStudioModules as $module) {
            $handle = 'gmrc-portrait-studio-' . $module;
            $path = GMRC_PATH
                . 'assets/js/components/media/portrait-studio/'
                . $module
                . '.js';
        
            wp_enqueue_script(
                $handle,
                GMRC_URL
                    . 'assets/js/components/media/portrait-studio/'
                    . $module
                    . '.js',
                [$previousHandle],
                file_exists($path)
                    ? (string) filemtime($path)
                    : GMRC_VERSION,
                true
            );
        
            $previousHandle = $handle;
        }

        $generationTwoPortraitPath =
            GMRC_PATH
            . 'assets/js/components/media/portrait-studio/generation2.js';
        
        wp_enqueue_script(
            'gmrc-portrait-studio-generation-two',
            GMRC_URL
                . 'assets/js/components/media/portrait-studio/generation2.js',
            ['gmrc-portrait-studio-app'],
            file_exists($generationTwoPortraitPath)
                ? (string) filemtime($generationTwoPortraitPath)
                : GMRC_VERSION,
            true
        );

        $livingPortraitScriptPath =
            GMRC_PATH
            . 'assets/js/components/media/portrait-studio/living-portrait.js';
        
        wp_enqueue_script(
            'gmrc-portrait-studio-living-portrait',
            GMRC_URL
                . 'assets/js/components/media/portrait-studio/living-portrait.js',
            ['gmrc-portrait-studio-generation-two'],
            file_exists($livingPortraitScriptPath)
                ? (string) filemtime($livingPortraitScriptPath)
                : GMRC_VERSION,
            true
        );

        $registrarsDeskScriptPath =
            GMRC_PATH
            . 'assets/js/components/furniture/'
            . 'registrars-desk.js';

        wp_enqueue_script(
            'gmrc-registrars-desk',
            GMRC_URL
                . 'assets/js/components/furniture/'
                . 'registrars-desk.js',
            [
                'gmrc-portrait-studio-living-portrait',
            ],
            file_exists($registrarsDeskScriptPath)
                ? (string) filemtime(
                    $registrarsDeskScriptPath
                )
                : GMRC_VERSION,
            true
        );

        $registrarsDeskStatusPath =
            GMRC_PATH
            . 'assets/js/components/furniture/'
            . 'registrars-desk-status.js';

        wp_enqueue_script(
            'gmrc-registrars-desk-status',
            GMRC_URL
                . 'assets/js/components/furniture/'
                . 'registrars-desk-status.js',
            ['gmrc-registrars-desk'],
            file_exists($registrarsDeskStatusPath)
                ? (string) filemtime(
                    $registrarsDeskStatusPath
                )
                : GMRC_VERSION,
            true
        );
        
        $livingPortraitStylePath =
            GMRC_PATH
            . 'assets/css/components/media/generation2-living-portrait.css';
        
        wp_enqueue_style(
            'gmrc-generation-two-living-portrait',
            GMRC_URL
                . 'assets/css/components/media/generation2-living-portrait.css',
            [],
            file_exists($livingPortraitStylePath)
                ? (string) filemtime($livingPortraitStylePath)
                : GMRC_VERSION
        );
        
    }
    

    protected function enqueueTheme(): void
    {
        wp_enqueue_style(
            'gmrc-guild-ledger',
            GMRC_URL . 'assets/css/guild-ledger.css',
            [
                'gmrc-guild-ornaments',
            ],
            GMRC_VERSION
        );
    
        wp_enqueue_style(
            'gmrc-companion-app',
            GMRC_URL . 'assets/css/companion-app.css',
            [
                'gmrc-guild-ledger',
            ],
            GMRC_VERSION
        );
    }

    /**
     * Render the Companion application shortcode.
     *
     * The attributes and content parameters are retained for compatibility
     * with the WordPress shortcode callback API.
     *
     * @param array<string, mixed> $attributes Shortcode attributes.
     * @param string|null          $content    Enclosed shortcode content.
     */
    public function renderApp(
        array $attributes = [],
        ?string $content = null
    ): string {
        unset($attributes, $content);

        return $this->app
            ->make(AppController::class)
            ->handle();
    }

    /**
     * Determine whether the current page contains the Companion shortcode.
     */
    protected function isCompanionPage(): bool
    {
        if (! is_singular()) {
            return false;
        }

        global $post;

        if (! $post instanceof WP_Post) {
            return false;
        }

        return has_shortcode(
            $post->post_content,
            'gmrc_app'
        );
    }
}
