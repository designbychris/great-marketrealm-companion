<?php

use GreatMarketrealmCompanion\Application\Navigation\Navigation;
use GreatMarketrealmCompanion\Application\Navigation\MenuItem;

defined('ABSPATH') || exit;

/** @var \GreatMarketrealmCompanion\Core\Application $app */

$navigation = $app->make(Navigation::class);

/**
 * Temporary active page detection.
 *
 * This will eventually move into the routing system.
 */
//$current = gmrc()->route()->current();

$active = gmrc()->route()->is(
    $item->route()
);

?>

<nav class="gmrc-sidebar">

    <ul class="gmrc-sidebar__menu">

        <?php foreach ($navigation->items() as $item) : ?>

            <?php
            /** @var MenuItem $item */

            $active = $item->route() === $current;
            ?>

            <li class="gmrc-sidebar__item">

                <a
                    class="gmrc-sidebar__link<?php echo $active ? ' is-active' : ''; ?>" <?php if ($active) : ?>aria-current="page" <?php endif; ?>
                    href="<?php echo esc_url(
                        gmrc()->route()->url(
                            $item->route()
                        )
                    ); ?>"
                >

                    <span class="gmrc-sidebar__icon">

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
