<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Guild Seal component.
 *
 * Variables:
 *
 * @var string $symbol
 * @var string $label
 * @var string $variant
 * @var string $size
 * @var bool   $decorative
 * @var string $class
 */

$symbol     = isset($symbol) ? trim((string) $symbol) : '✦';
$label      = isset($label) ? trim((string) $label) : 'Guild Seal';
$variant    = isset($variant) ? trim((string) $variant) : 'wax';
$size       = isset($size) ? trim((string) $size) : 'medium';
$decorative = isset($decorative) && (bool) $decorative;
$class      = isset($class) ? trim((string) $class) : '';

$allowedVariants = [
    'wax',
    'purple',
    'gold',
    'ink',
];

if (! in_array($variant, $allowedVariants, true)) {
    $variant = 'wax';
}

$allowedSizes = [
    'small',
    'medium',
    'large',
];

if (! in_array($size, $allowedSizes, true)) {
    $size = 'medium';
}

$classes = array_filter(
    [
        'guild-seal',
        'guild-seal--' . $variant,
        'guild-seal--' . $size,
        $class,
    ]
);

$attributes = $decorative
    ? 'aria-hidden="true"'
    : sprintf(
        'role="img" aria-label="%s"',
        esc_attr($label)
    );
?>

<span
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
    <span class="guild-seal__rim">
        <span class="guild-seal__inner">
            <?php echo esc_html($symbol); ?>
        </span>
    </span>
</span>
