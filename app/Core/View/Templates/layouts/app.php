<?php

defined('ABSPATH') || exit;

$currentUser = wp_get_current_user();

?>

<div class="gmrc-app-shell">

    <aside class="gmrc-sidebar">

        <div class="gmrc-sidebar__header">

            <a
                href="<?php echo esc_url(
                    remove_query_arg('gmrc_route', get_permalink())
                ); ?>"
                class="gmrc-brand"
            >

                <span
                    class="gmrc-brand__mascot"
                    aria-hidden="true"
                >
                    🍆
                </span>

                <span class="gmrc-brand__text">

                    <strong>
                        Great Marketrealm Companion
                    </strong>

                    <small>
                        Keeper of the Kingdoms
                    </small>

                </span>

            </a>

        </div>

        <nav
            class="gmrc-navigation"
            aria-label="Companion navigation"
        >

            <?php foreach ($navigation ?? [] as $item) : ?>

                <?php
                $classes = [
                    'gmrc-navigation__item',
                ];

                if (! empty($item['active'])) {
                    $classes[] = 'is-active';
                }

                if (empty($item['enabled'])) {
                    $classes[] = 'is-disabled';
                }
                ?>

                <?php if (! empty($item['enabled'])) : ?>

                    <a
                        href="<?php echo esc_url($item['url']); ?>"
                        class="<?php echo esc_attr(
                            implode(' ', $classes)
                        ); ?>"
                        <?php
                        echo ! empty($item['active'])
                            ? 'aria-current="page"'
                            : '';
                        ?>
                    >

                        <span
                            class="gmrc-navigation__icon"
                            aria-hidden="true"
                        >
                            <?php echo wp_kses_post($item['icon']); ?>
                        </span>

                        <span>
                            <?php echo esc_html($item['label']); ?>
                        </span>

                    </a>

                <?php else : ?>

                    <span
                        class="<?php echo esc_attr(
                            implode(' ', $classes)
                        ); ?>"
                        aria-disabled="true"
                    >

                        <span
                            class="gmrc-navigation__icon"
                            aria-hidden="true"
                        >
                            <?php echo wp_kses_post($item['icon']); ?>
                        </span>

                        <span>
                            <?php echo esc_html($item['label']); ?>
                        </span>

                        <small>
                            Soon
                        </small>

                    </span>

                <?php endif; ?>

            <?php endforeach; ?>

        </nav>

        <div class="gmrc-sidebar__footer">

            <span>
                <?php echo esc_html($currentUser->display_name); ?>
            </span>

            <small>
                Adventurer
            </small>

        </div>

    </aside>

    <div class="gmrc-app-main">

        <header class="gmrc-topbar">

            <div>

                <p class="gmrc-topbar__eyebrow">
                    Great Marketrealm Companion
                </p>

                <h1 class="gmrc-topbar__title">
                    <?php echo esc_html(
                        $pageTitle ?? 'Dashboard'
                    ); ?>
                </h1>

            </div>

            <div class="gmrc-user-badge">

                <div class="gmrc-user-badge__avatar">
                    <?php
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
                </div>

                <div class="gmrc-user-badge__details">

                    <strong>
                        <?php
                        echo esc_html(
                            $currentUser->display_name
                        );
                        ?>
                    </strong>

                    <small>
                        Adventurer
                    </small>

                </div>

            </div>

        </header>

        <main class="gmrc-content">

            <?php echo $content; ?>

        </main>

    </div>

</div>
