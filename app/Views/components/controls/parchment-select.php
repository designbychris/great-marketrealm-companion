<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Parchment Select component.
 *
 * Variables:
 *
 * @var string               $name
 * @var string               $label
 * @var string|int           $value
 * @var array<string, mixed> $options
 * @var string               $id
 * @var string               $placeholder
 * @var string               $help
 * @var string               $error
 * @var string               $class
 * @var bool                 $required
 * @var bool                 $disabled
 */

$name        = isset($name) ? trim((string) $name) : '';
$label       = isset($label) ? trim((string) $label) : '';
$value       = isset($value) ? (string) $value : '';
$options     = isset($options) && is_array($options)
    ? $options
    : [];

$id          = isset($id) ? trim((string) $id) : '';
$placeholder = isset($placeholder)
    ? trim((string) $placeholder)
    : 'Choose an entry';

$help     = isset($help) ? trim((string) $help) : '';
$error    = isset($error) ? trim((string) $error) : '';
$class    = isset($class) ? trim((string) $class) : '';
$required = isset($required) && (bool) $required;
$disabled = isset($disabled) && (bool) $disabled;

if ($name === '' || $label === '') {
    return;
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
    'parchment-select-field',
    $error !== ''
        ? 'parchment-select-field--invalid'
        : '',
    $disabled
        ? 'parchment-select-field--disabled'
        : '',
    $class,
]);
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <label
        class="parchment-select-field__label"
        for="<?php echo esc_attr($id); ?>"
    >
        <span>
            <?php echo esc_html($label); ?>
        </span>

        <?php if ($required) : ?>
            <span
                class="parchment-select-field__required"
                aria-hidden="true"
            >
                *
            </span>
        <?php endif; ?>
    </label>

    <div class="parchment-select-field__control">
        <select
            class="parchment-select"
            id="<?php echo esc_attr($id); ?>"
            name="<?php echo esc_attr($name); ?>"

            <?php if ($required) : ?>
                required
                aria-required="true"
            <?php endif; ?>

            <?php if ($disabled) : ?>
                disabled
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
            <?php if ($placeholder !== '') : ?>
                <option
                    value=""
                    <?php selected($value, ''); ?>
                    <?php disabled($required); ?>
                >
                    <?php echo esc_html($placeholder); ?>
                </option>
            <?php endif; ?>

            <?php foreach ($options as $optionValue => $optionLabel) : ?>
                <option
                    value="<?php echo esc_attr(
                        (string) $optionValue
                    ); ?>"
                    <?php selected(
                        $value,
                        (string) $optionValue
                    ); ?>
                >
                    <?php echo esc_html(
                        (string) $optionLabel
                    ); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <span
            class="parchment-select-field__arrow"
            aria-hidden="true"
        >
            ◆
        </span>
    </div>

    <?php if ($help !== '') : ?>
        <p
            class="parchment-select-field__help"
            id="<?php echo esc_attr($helpId); ?>"
        >
            <?php echo esc_html($help); ?>
        </p>
    <?php endif; ?>

    <?php if ($error !== '') : ?>
        <p
            class="parchment-select-field__error"
            id="<?php echo esc_attr($errorId); ?>"
            role="alert"
        >
            <?php echo esc_html($error); ?>
        </p>
    <?php endif; ?>
</div>
