<?php

defined('ABSPATH') || exit;

?>

<section class="gmrc-page">

    <header class="gmrc-page__header">

        <h1>Characters Kingdom</h1>

        <p>
            Welcome to the Characters Kingdom.
        </p>

    </header>

    <main class="gmrc-page__content">

        <?php if (empty($characters)) : ?>

            <p>
                No characters have been created yet.
            </p>

        <?php else : ?>

            <ul>

                <?php foreach ($characters as $character) : ?>

                    <li>
                        <?= esc_html($character->name()); ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        <?php endif; ?>

    </main>

</section>
