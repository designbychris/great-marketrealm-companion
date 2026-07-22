<?php

defined('ABSPATH') || exit;

$requestedRoute = $requestedRoute ?? '';
?>

<section class="gmrc-not-found">
    <div class="gmrc-not-found__mascot" aria-hidden="true">
        🍆
    </div>

    <p class="gmrc-eyebrow">
        Auby seems puzzled
    </p>

    <h1>This path has not been charted yet.</h1>

    <p>
        The requested Kingdom could not be found within the Companion.
    </p>

    <?php if ($requestedRoute !== '') : ?>
        <p class="gmrc-not-found__route">
            Requested route:
            <code>
                <?php echo esc_html($requestedRoute); ?>
            </code>
        </p>
    <?php endif; ?>

    <a
        class="gmrc-button"
        href="<?php echo esc_url(remove_query_arg('gmrc_route')); ?>"
    >
        Return to the Dashboard
    </a>
</section>
