<?php

use GreatMarketrealmCompanion\Application\Navigation\MenuItem;

defined('ABSPATH') || exit;

/**
 * @var \GreatMarketrealmCompanion\Core\Application $app
 */

$navigation = gmrc()->navigation();
$route      = gmrc()->route();

?>

<nav class="gmrc-sidebar" aria-label="<?php esc_attr_e('Primary navigation', 'great-marketrealm-companion'); ?>">

    <ul class="gmrc-sidebar__menu">

        <?php foreach ($navigation->items() as $item) : ?>

            <?php
            /** @var MenuItem $item */

            $active = $route->is($item->route());
            ?>

            <li class="gmrc-sidebar__item">

                <a
                    class="gmrc-sidebar__link<?php echo $active ? ' is-active' : ''; ?>"
                    href="<?php echo esc_url($route->url($item->route())); ?>"
                    <?php if ($active) : ?>
                        aria-current="page"
                    <?php endif; ?>
                >

                    <span class="gmrc-sidebar__icon" aria-hidden="true">
                        <?php echo esc_html($item->icon()); ?>
                    </span>

                    <span class="gmrc-sidebar__label">
                        <?php echo esc_html($item->label()); ?>
                    </span>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</nav>
