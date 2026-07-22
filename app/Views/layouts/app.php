<?php

defined('ABSPATH') || exit;

$pageTitle = $pageTitle ?? 'Dashboard';
$content   = $content ?? '';
$version   = defined('GMRC_VERSION') ? GMRC_VERSION : '0.3.0';
?>

<div class="gmrc-app-shell">
    <aside class="gmrc-sidebar">
        <div class="gmrc-sidebar__header">
            <a class="gmrc-brand" href="#" aria-label="Marketrealm Companion dashboard">
                <span class="gmrc-brand__mascot" aria-hidden="true">
                    🍆
                </span>

                <span class="gmrc-brand__text">
                    <strong>Marketrealm Companion</strong>
                    <small>Keeper of the Kingdoms</small>
                </span>
            </a>
        </div>

        <nav class="gmrc-navigation" aria-label="Companion navigation">
            <a
                class="gmrc-navigation__item is-active"
                href="#"
                aria-current="page"
            >
                <span class="gmrc-navigation__icon" aria-hidden="true">⌂</span>
                <span>Dashboard</span>
            </a>

            <span class="gmrc-navigation__item is-disabled">
                <span class="gmrc-navigation__icon" aria-hidden="true">♙</span>
                <span>Characters</span>
                <small>Soon</small>
            </span>

            <span class="gmrc-navigation__item is-disabled">
                <span class="gmrc-navigation__icon" aria-hidden="true">◇</span>
                <span>Campaigns</span>
                <small>Soon</small>
            </span>

            <span class="gmrc-navigation__item is-disabled">
                <span class="gmrc-navigation__icon" aria-hidden="true">★</span>
                <span>Achievements</span>
                <small>Soon</small>
            </span>
        </nav>

        <footer class="gmrc-sidebar__footer">
            <span>Marketrealm Companion</span>
            <small>Version <?php echo esc_html($version); ?></small>
        </footer>
    </aside>

    <div class="gmrc-app-main">
        <header class="gmrc-topbar">
            <div>
                <p class="gmrc-topbar__eyebrow">
                    Marketrealm Companion
                </p>

                <h2 class="gmrc-topbar__title">
                    <?php echo esc_html($pageTitle); ?>
                </h2>
            </div>

            <div class="gmrc-user-badge">
                <span class="gmrc-user-badge__avatar" aria-hidden="true">
                    <?php
                    $currentUser = wp_get_current_user();

                    echo esc_html(
                        strtoupper(
                            substr(
                                $currentUser->display_name ?: 'A',
                                0,
                                1
                            )
                        )
                    );
                    ?>
                </span>

                <span class="gmrc-user-badge__details">
                    <strong>
                        <?php
                        echo esc_html(
                            $currentUser->display_name ?: 'Adventurer'
                        );
                        ?>
                    </strong>

                    <small>Realm Traveller</small>
                </span>
            </div>
        </header>

        <main class="gmrc-content" id="gmrc-main-content">
            <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </main>
    </div>
</div>
