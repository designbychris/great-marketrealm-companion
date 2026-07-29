<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Ledger Ribbon component.
 *
 * Variables:
 *
 * @var string $label
 * @var string $variant
 * @var string $position
 * @var string $icon
 * @var string $class
 */

$label    = isset($label) ? trim((string) $label) : '';
$variant  = isset($variant) ? trim((string) $variant) : 'purple';
$position = isset($position) ? trim((string) $position) : 'top-right';
$icon     = isset($icon) ? trim((string) $icon) : '';
$class    = isset($class) ? trim((string) $class) : '';

if ($label === '') {
    return;
}

$allowedVariants = [
    'purple',
    'wax',
    'gold',
    'leather',
];

if (! in_array($variant, $allowedVariants, true)) {
    $variant = 'purple';
}

$allowedPositions = [
    'top-left',
    'top-right',
    'inline',
];

if (! in_array($position, $allowedPositions, true)) {
    $position = 'top-right';
}

$classes = array_filter(
    [
        'ledger-ribbon',
        'ledger-ribbon--' . $variant,
        'ledger-ribbon--' . $position,
        $class,
    ]
);
?>

<span class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if ($icon !== '') : ?>
        <span
            class="ledger-ribbon__icon"
            aria-hidden="true"
        >
            <?php echo esc_html($icon); ?>
        </span>
    <?php endif; ?>

    <span class="ledger-ribbon__label">
        <?php echo esc_html($label); ?>
    </span>
</span>
