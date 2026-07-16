<?php

defined('ABSPATH') || exit;

$current_user = wp_get_current_user();

?>

<aside class="gmrc-sidebar">

    <div class="gmrc-sidebar__brand">

        <a href="#" class="gmrc-sidebar__logo">

            <span class="gmrc-sidebar__logo-icon">
                🍎
            </span>

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

                Adventurer

            </span>

        </div>

    </div>

    <nav class="gmrc-sidebar__nav">

        <ul>

            <li>
                <a class="active" href="#">
                    🏠 Dashboard
                </a>
            </li>

            <li>
                <a href="#">
                    👤 Characters
                </a>
            </li>

            <li>
                <a href="#">
                    🎒 Inventory
                </a>
            </li>

            <li>
                <a href="#">
                    📖 Journal
                </a>
            </li>

            <li>
                <a href="#">
                    🗺 Campaigns
                </a>
            </li>

            <li>
                <a href="#">
                    ⚔ Quests
                </a>
            </li>

            <li>
                <a href="#">
                    📚 Compendium
                </a>
            </li>

            <li>
                <a href="#">
                    🎲 Dice Roller
                </a>
            </li>

        </ul>

    </nav>

    <div class="gmrc-sidebar__footer">

        <ul>

            <li>

                <a href="#">

                    ⚙ Settings

                </a>

            </li>

            <li>

                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">

                    🚪 Logout

                </a>

            </li>

        </ul>

    </div>

</aside>
