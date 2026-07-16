<?php

defined('ABSPATH') || exit;

$current_user = wp_get_current_user();

/**
 * Temporary navigation.
 *
 * TODO:
 * Replace with Navigation::sidebar()
 * once the Navigation helper has been created.
 */
$items = [
    [
        'label'  => __('Dashboard', 'great-marketrealm-companion'),
        'icon'   => '🏠',
        'url'    => '#',
        'active' => true,
    ],
    [
        'label'  => __('Characters', 'great-marketrealm-companion'),
        'icon'   => '👤',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Inventory', 'great-marketrealm-companion'),
        'icon'   => '🎒',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Journal', 'great-marketrealm-companion'),
        'icon'   => '📖',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Campaigns', 'great-marketrealm-companion'),
        'icon'   => '🗺',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Quests', 'great-marketrealm-companion'),
        'icon'   => '⚔',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Compendium', 'great-marketrealm-companion'),
        'icon'   => '📚',
        'url'    => '#',
        'active' => false,
    ],
    [
        'label'  => __('Dice Roller', 'great-marketrealm-companion'),
        'icon'   => '🎲',
        'url'    => '#',
        'active' => false,
    ],
];

?>

<aside class="gmrc-sidebar">

    <div class="gmrc-sidebar__brand">

        <a href="#" class="gmrc-sidebar__logo">

            <span class="gmrc-sidebar__logo-icon">🍎</span>

            <div class="gmrc-sidebar__logo-text">

                <strong>Great Marketrealm</strong>

                <span>Companion</span>

            </div>

        </a>

    </div>

    <div class="gmrc-sidebar__user">

        <div class="gmrc-sidebar__avatar">
            <?php echo get_avatar($current_user->ID, 60); ?>
        </div>

        <div class="gmrc-sidebar__user-details">

            <strong>
                <?php echo esc_html($current_user->display_name); ?>
            </strong>

            <span>
                <?php esc_html_e('Adventurer', 'great-marketrealm-companion'); ?>
            </span>

        </div>

    </div>

    <nav class="gmrc-sidebar__nav">

        <ul>

            <?php foreach ($items as $item) : ?>

                <li>

                    <a
                        href="<?php echo esc_url($item['url']); ?>"
                        class="<?php echo $item['active'] ? 'is-active' : ''; ?>">

                        <span class="gmrc-sidebar__icon">

                            <?php echo esc_html($item['icon']); ?>

                        </span>

                        <span class="gmrc-sidebar__label">

                            <?php echo esc_html($item['label']); ?>

                        </span>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

    </nav>

    <div class="gmrc-sidebar__footer">

        <ul>

            <li>

                <a href="#">

                    ⚙

                    <span><?php esc_html_e('Settings', 'great-marketrealm-companion'); ?></span>

                </a>

            </li>

            <li>

                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">

                    🚪

                    <span><?php esc_html_e('Logout', 'great-marketrealm-companion'); ?></span>

                </a>

            </li>

        </ul>

    </div>

</aside>
