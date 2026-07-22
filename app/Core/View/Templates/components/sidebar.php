<?php

defined('ABSPATH') || exit;

$currentUser = wp_get_current_user();

?>

<aside class="gmrc-sidebar">

    <div class="gmrc-sidebar__header">

        <a
            class="gmrc-brand"
            href="<?php echo esc_url(
                remove_query_arg(
                    'gmrc_route',
                    get_permalink()
                )
            ); ?>"
        >

            <span
                class="gmrc-brand__icon"
                aria-hidden="true"
            >
                🍆
            </span>

            <span class="gmrc-brand__text">

                <strong>
                    Great Marketrealm
                </strong>

                <span>
                    Companion
                </span>

            </span>

        </a>

    </div>

    <div class="gmrc-sidebar__user">

        <div class="gmrc-sidebar__avatar">

            <?php
            echo get_avatar(
                $currentUser->ID,
                48
            );
            ?>

        </div>

        <div class="gmrc-sidebar__user-details">

            <strong>
                <?php
                echo esc_html(
                    $currentUser->display_name
                );
                ?>
            </strong>

            <span>
                Adventurer
            </span>

        </div>

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
                    href="<?php echo esc_url(
                        $item['url']
                    ); ?>"
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
                        <?php
                        echo wp_kses_post(
                            $item['icon']
                        );
                        ?>
                    </span>

                    <span class="gmrc-navigation__label">
                        <?php
                        echo esc_html(
                            $item['label']
                        );
                        ?>
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
                        <?php
                        echo wp_kses_post(
                            $item['icon']
                        );
                        ?>
                    </span>

                    <span class="gmrc-navigation__label">
                        <?php
                        echo esc_html(
                            $item['label']
                        );
                        ?>
                    </span>

                </span>

            <?php endif; ?>

        <?php endforeach; ?>

    </nav>

    <div class="gmrc-sidebar__footer">

        <a
            href="<?php echo esc_url(
                wp_logout_url(home_url())
            ); ?>"
            class="gmrc-navigation__item"
        >

            <span
                class="gmrc-navigation__icon"
                aria-hidden="true"
            >
                ↪
            </span>

            <span class="gmrc-navigation__label">
                Logout
            </span>

        </a>

    </div>

</aside>
