<?php

defined('ABSPATH') || exit;

$quote = $quote ?? '';
$author = $author ?? 'Auby';
$image = $image ?? '';
$variant = $variant ?? 'default';

?>

<aside class="auby-note auby-note--<?php echo esc_attr($variant); ?>">

    <div class="auby-note__portrait">

        <?php if ($image) : ?>

            <img
                src="<?php echo esc_url($image); ?>"
                alt=""
            >

        <?php else : ?>

            <div
                class="auby-note__placeholder"
                aria-hidden="true"
            >
                🍆
            </div>

        <?php endif; ?>

    </div>

    <div class="auby-note__body">

        <p class="auby-note__title">
            Auby's Note
        </p>

        <blockquote class="auby-note__quote">

            <?php echo esc_html($quote); ?>

        </blockquote>

        <footer class="auby-note__author">

            — <?php echo esc_html($author); ?>

        </footer>

    </div>

</aside>
