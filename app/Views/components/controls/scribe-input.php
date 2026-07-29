<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Scribe Input component.
 *
 * Variables:
 *
 * @var string $name
 * @var string $label
 * @var string $value
 * @var string $type
 * @var string $id
 * @var string $placeholder
 * @var string $autocomplete
 * @var string $help
 * @var string $error
 * @var string $class
 * @var string $min
 * @var string $max
 * @var string $step
 * @var bool   $required
 * @var bool   $disabled
 * @var bool   $readonly
 */

$name         = isset($name) ? trim((string) $name) : '';
$label        = isset($label) ? trim((string) $label) : '';
$value        = isset($value) ? (string) $value : '';
$type         = isset($type) ? trim((string) $type) : 'text';
$id           = isset($id) ? trim((string) $id) : '';
$placeholder  = isset($placeholder) ? (string) $placeholder : '';
$autocomplete = isset($autocomplete)
    ? trim((string) $autocomplete)
    : '';

$help     = isset($help) ? trim((string) $help) : '';
$error    = isset($error) ? trim((string) $error) : '';
$class    = isset($class) ? trim((string) $class) : '';
$min      = isset($min) ? trim((string) $min) : '';
$max      = isset($max) ? trim((string) $max) : '';
$step     = isset($step) ? trim((string) $step) : '';
$required = isset($required) && (bool) $required;
$disabled = isset($disabled) && (bool) $disabled;
$readonly = isset($readonly) && (bool) $readonly;

if ($name === '' || $label === '') {
    return;
}

$allowedTypes = [
    'text',
    'email',
    'number',
    'password',
    'search',
    'tel',
    'url',
];

if (! in_array($type, $allowedTypes, true)) {
    $type = 'text';
}

if ($id === '') {
    $id = 'gmrc-field-' . sanitize_html_class($name);
}

$helpId = $help !== ''
    ? $id . '-help'
    : '';

$errorId = $error !== ''
    ? $id . '-error'
    : '';

$describedBy = array_filter([
    $helpId,
    $errorId,
]);

$classes = array_filter([
    'scribe-field',
    $error !== '' ? 'scribe-field--invalid' : '',
    $disabled ? 'scribe-field--disabled' : '',
    $readonly ? 'scribe-field--readonly' : '',
    $class,
]);
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <label
        class="scribe-field__label"
        for="<?php echo esc_attr($id); ?>"
    >
        <span class="scribe-field__label-text">
            <?php echo esc_html($label); ?>
        </span>

        <?php if ($required) : ?>
            <span
                class="scribe-field__required"
                aria-hidden="true"
            >
                *
            </span>
        <?php endif; ?>
    </label>

    <div class="scribe-field__control">
        <input
            class="scribe-input"
            type="<?php echo esc_attr($type); ?>"
            id="<?php echo esc_attr($id); ?>"
            name="<?php echo esc_attr($name); ?>"
            value="<?php echo esc_attr($value); ?>"

            <?php if ($placeholder !== '') : ?>
                placeholder="<?php echo esc_attr($placeholder); ?>"
            <?php endif; ?>

            <?php if ($autocomplete !== '') : ?>
                autocomplete="<?php echo esc_attr($autocomplete); ?>"
            <?php endif; ?>

            <?php if ($min !== '') : ?>
                min="<?php echo esc_attr($min); ?>"
            <?php endif; ?>

            <?php if ($max !== '') : ?>
                max="<?php echo esc_attr($max); ?>"
            <?php endif; ?>

            <?php if ($step !== '') : ?>
                step="<?php echo esc_attr($step); ?>"
            <?php endif; ?>

            <?php if ($required) : ?>
                required
                aria-required="true"
            <?php endif; ?>

            <?php if ($disabled) : ?>
                disabled
            <?php endif; ?>

            <?php if ($readonly) : ?>
                readonly
            <?php endif; ?>

            <?php if ($error !== '') : ?>
                aria-invalid="true"
            <?php endif; ?>

            <?php if ($describedBy !== []) : ?>
                aria-describedby="<?php echo esc_attr(
                    implode(' ', $describedBy)
                ); ?>"
            <?php endif; ?>
        >

        <span
            class="scribe-field__ink-line"
            aria-hidden="true"
        ></span>
    </div>

    <?php if ($help !== '') : ?>
        <p
            class="scribe-field__help"
            id="<?php echo esc_attr($helpId); ?>"
        >
            <?php echo esc_html($help); ?>
        </p>
    <?php endif; ?>

    <?php if ($error !== '') : ?>
        <p
            class="scribe-field__error"
            id="<?php echo esc_attr($errorId); ?>"
            role="alert"
        >
            <?php echo esc_html($error); ?>
        </p>
    <?php endif; ?>
</div>
