<?php

use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;

defined('ABSPATH') || exit;

$currentUser = wp_get_current_user();

$navigationId =
    'gmrc-primary-navigation';

$homeUrl = remove_query_arg(
    'gmrc_route',
    get_permalink()
);

$guildRoleLabel = in_array(
    'gmrc_dm',
    (array) $currentUser->roles,
    true
) || current_user_can('gmrc_manage_campaigns')
    ? 'Dungeon Master'
    : 'Player';

$profilePortraitId = absint(
    get_user_meta(
        $currentUser->ID,
        GuildProfile::PORTRAIT_ATTACHMENT_META,
        true
    )
);

$guildAvatar = $profilePortraitId > 0
    ? wp_get_attachment_image(
        $profilePortraitId,
        [38, 38],
        false,
        [
            'class' => 'avatar avatar-38 photo',
            'alt' => '',
        ]
    )
    : get_avatar($currentUser->ID, 38);

/**
 * Render the SVG supplied by the Navigation icon registry.
 *
 * wp_kses_post() intentionally strips SVG elements, so the Guild
 * Navigation uses a deliberately narrow whitelist instead of adding
 * another icon-font or JavaScript dependency.
 */
$navigationIconHtml = static function (
    string $icon
): string {
    return wp_kses(
        $icon,
        [
            'svg' => [
                'viewBox' => true,
                'viewbox' => true,
                'aria-hidden' => true,
                'focusable' => true,
                'xmlns' => true,
            ],
            'path' => [
                'd' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
            ],
            'circle' => [
                'cx' => true,
                'cy' => true,
                'r' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
            ],
            'rect' => [
                'x' => true,
                'y' => true,
                'width' => true,
                'height' => true,
                'rx' => true,
                'ry' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
            ],
            'line' => [
                'x1' => true,
                'x2' => true,
                'y1' => true,
                'y2' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
            ],
            'polyline' => [
                'points' => true,
                'fill' => true,
                'stroke' => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
            ],
        ]
    );
};
?>

<div
    class="gmrc-sidebar"
    data-guild-navigation
>

    <button
        class="gmrc-navigation-toggle"
        type="button"
        aria-expanded="false"
        aria-controls="<?php echo esc_attr(
            $navigationId
        ); ?>"
        data-guild-navigation-toggle
    >

        <span
            class="gmrc-navigation-toggle__icon"
            aria-hidden="true"
        >
            <span></span>
            <span></span>
            <span></span>
        </span>

        <span class="gmrc-navigation-toggle__label">
            Guild Menu
        </span>

    </button>

    <nav
        id="<?php echo esc_attr(
            $navigationId
        ); ?>"
        class="gmrc-navigation"
        aria-label="Guild Hall navigation"
        data-guild-navigation-menu
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
                        echo $navigationIconHtml(
                            (string) $item['icon']
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
                        echo $navigationIconHtml(
                            (string) $item['icon']
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

    <div class="gmrc-sidebar__user">

        <div
            class="gmrc-sidebar__avatar"
            aria-hidden="true"
        >
            <?php
            echo $guildAvatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
                <?php echo esc_html($guildRoleLabel); ?>
            </span>

        </div>

        <a
            href="<?php echo esc_url(
                wp_logout_url($homeUrl)
            ); ?>"
            class="gmrc-navigation__logout"
        >
            <span aria-hidden="true">
                ↪
            </span>

            <span class="gmrc-navigation__logout-label">
                Logout
            </span>
        </a>

    </div>

</div>
