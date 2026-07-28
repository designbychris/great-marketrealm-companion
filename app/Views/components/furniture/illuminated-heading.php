<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var string $title
 * @var string $subtitle
 * @var string $initial
 * @var string $class
 */

$title = trim((string)($title ?? ''));

if ($title === '') {
    return;
}

$subtitle = trim((string)($subtitle ?? ''));

$initial = trim((string)($initial ?? ''));

if ($initial === '') {
    if (function_exists('mb_substr')) {
        $initial = mb_substr($title, 0, 1);
    } else {
        $initial = substr($title, 0, 1);
    }
}

$remainder = function_exists('mb_substr')
    ? mb_substr($title, 1)
    : substr($title, 1);

$class = trim((string)($class ?? ''));
?>

<header class="illuminated-heading <?php echo esc_attr($class); ?>">

    <?php if ($subtitle !== '') : ?>

        <p class="illuminated-heading__subtitle">
            <?php echo esc_html($subtitle); ?>
        </p>

    <?php endif; ?>

    <h2 class="illuminated-heading__title">

        <span
            class="illuminated-heading__initial"
            aria-hidden="true"
        >
            <?php echo esc_html($initial); ?>
        </span>

        <span class="illuminated-heading__text">
            <?php echo esc_html($remainder); ?>
        </span>

    </h2>

    <div
        class="illuminated-heading__rule"
        aria-hidden="true"
    >
        <span>❦</span>
    </div>

</header>
