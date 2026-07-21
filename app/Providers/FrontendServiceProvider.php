<?php

namespace GreatMarketrealmCompanion\Providers;

defined('ABSPATH') || exit;

/**
 * Front-end Service Provider.
 *
 * Registers the initial Marketrealm Companion application screen.
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
        // No container bindings are required yet.
    }

    /**
     * Boot front-end functionality.
     */
    public function boot(): void
    {
        add_shortcode(
            'gmrc_app',
            [$this, 'renderApp']
        );
    }

    /**
     * Render the initial application screen.
     */
    public function renderApp(): string
    {
        ob_start();
        ?>

        <div class="gmrc-app">
            <aside class="gmrc-sidebar">
                <div class="gmrc-brand">
                    <span class="gmrc-brand__icon" aria-hidden="true">🍆</span>

                    <div>
                        <strong>Marketrealm Companion</strong>
                        <span>Keeper of the Kingdoms</span>
                    </div>
                </div>

                <nav class="gmrc-navigation" aria-label="Companion navigation">
                    <a class="gmrc-navigation__item is-active" href="#">
                        Dashboard
                    </a>

                    <span class="gmrc-navigation__item is-disabled">
                        Characters
                    </span>

                    <span class="gmrc-navigation__item is-disabled">
                        Campaigns
                    </span>

                    <span class="gmrc-navigation__item is-disabled">
                        Achievements
                    </span>
                </nav>
            </aside>

            <main class="gmrc-content">
                <div class="gmrc-status">
                    <span class="gmrc-status__icon" aria-hidden="true">✓</span>
                    Framework booted successfully
                </div>

                <p class="gmrc-eyebrow">
                    Welcome to the Kingdoms
                </p>

                <h1>Marketrealm Companion is alive!</h1>

                <p class="gmrc-introduction">
                    The new Composer-powered application framework has loaded
                    successfully inside WordPress.
                </p>

                <div class="gmrc-system-grid">
                    <div class="gmrc-system-card">
                        <span>Composer autoloader</span>
                        <strong>Online</strong>
                    </div>

                    <div class="gmrc-system-card">
                        <span>Application Kernel</span>
                        <strong>Online</strong>
                    </div>

                    <div class="gmrc-system-card">
                        <span>Service Providers</span>
                        <strong>Online</strong>
                    </div>

                    <div class="gmrc-system-card">
                        <span>Characters Kingdom</span>
                        <strong>Loaded</strong>
                    </div>
                </div>

                <footer class="gmrc-footer">
                    Marketrealm Companion
                    <?php echo esc_html(defined('GMRC_VERSION') ? GMRC_VERSION : '0.3.0'); ?>
                </footer>
            </main>
        </div>

        <style>
            .gmrc-app,
            .gmrc-app * {
                box-sizing: border-box;
            }

            .gmrc-app {
                display: grid;
                grid-template-columns: 260px minmax(0, 1fr);
                min-height: 640px;
                margin: 40px 0;
                overflow: hidden;
                border: 1px solid #dedede;
                border-radius: 18px;
                background: #f7f4ec;
                color: #262234;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                box-shadow: 0 24px 60px rgba(38, 34, 52, 0.12);
            }

            .gmrc-sidebar {
                padding: 32px 22px;
                background: #342f68;
                color: #ffffff;
            }

            .gmrc-brand {
                display: flex;
                align-items: center;
                gap: 14px;
                margin-bottom: 44px;
            }

            .gmrc-brand__icon {
                display: grid;
                width: 52px;
                height: 52px;
                place-items: center;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.12);
                font-size: 28px;
            }

            .gmrc-brand strong,
            .gmrc-brand span {
                display: block;
            }

            .gmrc-brand strong {
                font-size: 16px;
                line-height: 1.25;
            }

            .gmrc-brand div > span {
                margin-top: 4px;
                color: rgba(255, 255, 255, 0.68);
                font-size: 12px;
            }

            .gmrc-navigation {
                display: grid;
                gap: 8px;
            }

            .gmrc-navigation__item {
                display: block;
                padding: 12px 14px;
                border-radius: 9px;
                color: rgba(255, 255, 255, 0.72);
                text-decoration: none;
            }

            .gmrc-navigation__item.is-active {
                background: rgba(255, 255, 255, 0.14);
                color: #ffffff;
                font-weight: 700;
            }

            .gmrc-navigation__item.is-disabled {
                cursor: default;
                opacity: 0.55;
            }

            .gmrc-content {
                display: flex;
                flex-direction: column;
                padding: clamp(32px, 6vw, 72px);
            }

            .gmrc-status {
                display: inline-flex;
                align-self: flex-start;
                align-items: center;
                gap: 9px;
                padding: 8px 13px;
                border-radius: 999px;
                background: #e4f5e3;
                color: #267222;
                font-size: 14px;
                font-weight: 700;
            }

            .gmrc-status__icon {
                display: grid;
                width: 22px;
                height: 22px;
                place-items: center;
                border-radius: 50%;
                background: #3aaa35;
                color: #ffffff;
            }

            .gmrc-eyebrow {
                margin: 50px 0 10px;
                color: #6c6582;
                font-size: 14px;
                font-weight: 800;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }

            .gmrc-content h1 {
                max-width: 700px;
                margin: 0;
                color: #342f68;
                font-size: clamp(38px, 6vw, 72px);
                line-height: 1;
                letter-spacing: -0.045em;
            }

            .gmrc-introduction {
                max-width: 680px;
                margin: 24px 0 38px;
                color: #5e596c;
                font-size: 19px;
                line-height: 1.7;
            }

            .gmrc-system-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            .gmrc-system-card {
                padding: 20px;
                border: 1px solid #e3dfd5;
                border-radius: 12px;
                background: #ffffff;
            }

            .gmrc-system-card span,
            .gmrc-system-card strong {
                display: block;
            }

            .gmrc-system-card span {
                color: #777181;
                font-size: 14px;
            }

            .gmrc-system-card strong {
                margin-top: 8px;
                color: #3aaa35;
                font-size: 17px;
            }

            .gmrc-footer {
                margin-top: auto;
                padding-top: 48px;
                color: #918a9b;
                font-size: 13px;
            }

            @media (max-width: 760px) {
                .gmrc-app {
                    grid-template-columns: 1fr;
                    margin: 20px 0;
                }

                .gmrc-sidebar {
                    padding: 22px;
                }

                .gmrc-brand {
                    margin-bottom: 20px;
                }

                .gmrc-navigation {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .gmrc-content {
                    padding: 32px 22px;
                }

                .gmrc-system-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <?php

        return (string) ob_get_clean();
    }
}
