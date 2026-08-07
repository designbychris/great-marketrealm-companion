<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$title = trim(
    (string) ($title ?? __('Auby left a note', 'great-marketrealm-companion'))
);

$message = trim(
    (string) ($message ?? '')
);

$variant = sanitize_key(
    (string) ($variant ?? 'general')
);

$rotation = (float) ($rotation ?? -1.4);

$allowedVariants = [
    'general',
    'found',
    'tip',
    'warning',
    'memory',
];

if (! in_array($variant, $allowedVariants, true)) {
    $variant = 'general';
}

if ($message === '') {
    return;
}
?>

<aside
    class="<?php echo esc_attr(
        'gmrc-auby-sticky-note '
        . 'gmrc-auby-sticky-note--'
        . $variant
    ); ?>"
    style="<?php echo esc_attr(
        '--gmrc-auby-sticky-rotation: '
        . $rotation
        . 'deg;'
    ); ?>"
    data-auby-sticky-note
    aria-label="<?php echo esc_attr(
        $title
    ); ?>"
>
    <span
        class="gmrc-auby-sticky-note__tape
            gmrc-auby-sticky-note__tape--left"
        aria-hidden="true"
    ></span>

    <span
        class="gmrc-auby-sticky-note__tape
            gmrc-auby-sticky-note__tape--right"
        aria-hidden="true"
    ></span>

    <div class="gmrc-auby-sticky-note__paper">
        <span
            class="gmrc-auby-sticky-note__doodle"
            aria-hidden="true"
        >
            🍆
        </span>

        <p class="gmrc-auby-sticky-note__eyebrow">
            <?php echo esc_html($title); ?>
        </p>

        <p class="gmrc-auby-sticky-note__message">
            <?php echo esc_html($message); ?>
        </p>

        <p class="gmrc-auby-sticky-note__signature">
            — Auby
        </p>
    </div>
</aside>
