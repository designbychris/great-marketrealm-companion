<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Margin Note component.
 *
 * Variables:
 *
 * @var string $content
 * @var string $author
 * @var string $title
 * @var string $position
 * @var string $tone
 * @var string $class
 */

$content  = isset($content) ? trim((string) $content) : '';
$author   = isset($author) ? trim((string) $author) : '';
$title    = isset($title) ? trim((string) $title) : '';
$position = isset($position) ? trim((string) $position) : 'right';
$tone     = isset($tone) ? trim((string) $tone) : 'ink';
$class    = isset($class) ? trim((string) $class) : '';

if ($content === '') {
    return;
}

$allowedPositions = [
    'left',
    'right',
    'inline',
];

if (! in_array($position, $allowedPositions, true)) {
    $position = 'right';
}

$allowedTones = [
    'ink',
    'purple',
    'wax',
    'faded',
];

if (! in_array($tone, $allowedTones, true)) {
    $tone = 'ink';
}

$classes = array_filter(
    [
        'margin-note',
        'margin-note--' . $position,
        'margin-note--' . $tone,
        $class,
    ]
);
?>

<aside
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    aria-label="<?php echo esc_attr(
        $title !== ''
            ? $title
            : 'Ledger margin note'
    ); ?>"
>
    <span
        class="margin-note__mark"
        aria-hidden="true"
    >
        ✦
    </span>

    <?php if ($title !== '') : ?>
        <p class="margin-note__title">
            <?php echo esc_html($title); ?>
        </p>
    <?php endif; ?>

    <p class="margin-note__content">
        <?php echo esc_html($content); ?>
    </p>

    <?php if ($author !== '') : ?>
        <p class="margin-note__author">
            <span aria-hidden="true">—</span>
            <?php echo esc_html($author); ?>
        </p>
    <?php endif; ?>
</aside>
