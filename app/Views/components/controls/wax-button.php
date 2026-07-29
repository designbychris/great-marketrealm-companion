<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Wax Button component.
 *
 * Variables:
 *
 * @var string $label
 * @var string $href
 * @var string $type
 * @var string $name
 * @var string $value
 * @var string $symbol
 * @var string $variant
 * @var string $size
 * @var string $class
 * @var string $ariaLabel
 * @var bool   $disabled
 * @var bool   $fullWidth
 */

$label     = isset($label) ? trim((string) $label) : '';
$href      = isset($href) ? trim((string) $href) : '';
$type      = isset($type) ? trim((string) $type) : 'button';
$name      = isset($name) ? trim((string) $name) : '';
$value     = isset($value) ? (string) $value : '';
$symbol    = isset($symbol) ? trim((string) $symbol) : '✦';
$variant   = isset($variant) ? trim((string) $variant) : 'wax';
$size      = isset($size) ? trim((string) $size) : 'medium';
$class     = isset($class) ? trim((string) $class) : '';
$ariaLabel = isset($ariaLabel)
    ? trim((string) $ariaLabel)
    : '';

$disabled  = isset($disabled) && (bool) $disabled;
$fullWidth = isset($fullWidth) && (bool) $fullWidth;

if ($label === '') {
    return;
}

$allowedTypes = [
    'button',
    'submit',
    'reset',
];

if (! in_array($type, $allowedTypes, true)) {
    $type = 'button';
}

$allowedVariants = [
    'wax',
    'gold',
    'purple',
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

$classes = array_filter([
    'wax-button',
    'wax-button--' . $variant,
    'wax-button--' . $size,
    $fullWidth ? 'wax-button--full' : '',
    $disabled ? 'wax-button--disabled' : '',
    $class,
]);

$classAttribute = implode(' ', $classes);
?>

<?php if ($href !== '' && ! $disabled) : ?>
    <a
        class="<?php echo esc_attr($classAttribute); ?>"
        href="<?php echo esc_url($href); ?>"

        <?php if ($ariaLabel !== '') : ?>
            aria-label="<?php echo esc_attr($ariaLabel); ?>"
        <?php endif; ?>
    >
        <span
            class="wax-button__seal"
            aria-hidden="true"
        >
            <?php echo esc_html($symbol); ?>
        </span>

        <span class="wax-button__label">
            <?php echo esc_html($label); ?>
        </span>
    </a>
<?php else : ?>
    <button
        class="<?php echo esc_attr($classAttribute); ?>"
        type="<?php echo esc_attr($type); ?>"

        <?php if ($name !== '') : ?>
            name="<?php echo esc_attr($name); ?>"
        <?php endif; ?>

        <?php if ($value !== '') : ?>
            value="<?php echo esc_attr($value); ?>"
        <?php endif; ?>

        <?php if ($ariaLabel !== '') : ?>
            aria-label="<?php echo esc_attr($ariaLabel); ?>"
        <?php endif; ?>

        <?php if ($disabled) : ?>
            disabled
        <?php endif; ?>
    >
        <span
            class="wax-button__seal"
            aria-hidden="true"
        >
            <?php echo esc_html($symbol); ?>
        </span>

        <span class="wax-button__label">
            <?php echo esc_html($label); ?>
        </span>
    </button>
<?php endif; ?>
