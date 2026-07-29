<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Ink Checkbox component.
 *
 * Variables:
 *
 * @var string $name
 * @var string $label
 * @var string $value
 * @var string $id
 * @var string $description
 * @var string $error
 * @var string $class
 * @var bool   $checked
 * @var bool   $required
 * @var bool   $disabled
 */

$name        = isset($name) ? trim((string) $name) : '';
$label       = isset($label) ? trim((string) $label) : '';
$value       = isset($value) ? (string) $value : '1';
$id          = isset($id) ? trim((string) $id) : '';
$description = isset($description)
    ? trim((string) $description)
    : '';

$error    = isset($error) ? trim((string) $error) : '';
$class    = isset($class) ? trim((string) $class) : '';
$checked  = isset($checked) && (bool) $checked;
$required = isset($required) && (bool) $required;
$disabled = isset($disabled) && (bool) $disabled;

if ($name === '' || $label === '') {
    return;
}

if ($id === '') {
    $id = 'gmrc-field-' . sanitize_html_class($name);
}

$descriptionId = $description !== ''
    ? $id . '-description'
    : '';

$errorId = $error !== ''
    ? $id . '-error'
    : '';

$describedBy = array_filter([
    $descriptionId,
    $errorId,
]);

$classes = array_filter([
    'ink-checkbox-field',
    $checked ? 'ink-checkbox-field--checked' : '',
    $error !== ''
        ? 'ink-checkbox-field--invalid'
        : '',
    $disabled
        ? 'ink-checkbox-field--disabled'
        : '',
    $class,
]);
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <label
        class="ink-checkbox-field__label"
        for="<?php echo esc_attr($id); ?>"
    >
        <input
            class="ink-checkbox"
            type="checkbox"
            id="<?php echo esc_attr($id); ?>"
            name="<?php echo esc_attr($name); ?>"
            value="<?php echo esc_attr($value); ?>"

            <?php checked($checked); ?>

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

        <span
            class="ink-checkbox-field__mark"
            aria-hidden="true"
        >
            <span class="ink-checkbox-field__stroke">
                ✓
            </span>
        </span>

        <span class="ink-checkbox-field__content">
            <span class="ink-checkbox-field__text">
                <?php echo esc_html($label); ?>

                <?php if ($required) : ?>
                    <span
                        class="ink-checkbox-field__required"
                        aria-hidden="true"
                    >
                        *
                    </span>
                <?php endif; ?>
            </span>

            <?php if ($description !== '') : ?>
                <span
                    class="ink-checkbox-field__description"
                    id="<?php echo esc_attr(
                        $descriptionId
                    ); ?>"
                >
                    <?php echo esc_html($description); ?>
                </span>
            <?php endif; ?>
        </span>
    </label>

    <?php if ($error !== '') : ?>
        <p
            class="ink-checkbox-field__error"
            id="<?php echo esc_attr($errorId); ?>"
            role="alert"
        >
            <?php echo esc_html($error); ?>
        </p>
    <?php endif; ?>
</div>
