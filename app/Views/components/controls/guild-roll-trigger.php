<?php

defined('ABSPATH') || exit;

$label = isset($label) && is_string($label)
    ? $label
    : 'D20 Roll';

$modifier = isset($modifier)
    ? (int) $modifier
    : 0;

$primary = isset($primary) && is_scalar($primary)
    ? (string) $primary
    : ($modifier >= 0 ? '+' . $modifier : (string) $modifier);

$secondary = isset($secondary) && is_scalar($secondary)
    ? (string) $secondary
    : '';

$variant = isset($variant) && is_string($variant)
    ? sanitize_html_class($variant)
    : 'inline';

$modifierLabel = $modifier >= 0
    ? 'plus ' . $modifier
    : 'minus ' . abs($modifier);

$kind = isset($kind) && is_string($kind)
    ? sanitize_key($kind)
    : 'check';

$source = isset($source) && is_string($source)
    ? $source
    : $label;

$ability = isset($ability) && is_string($ability)
    ? $ability
    : '';

$proficiency = isset($proficiency) && is_string($proficiency)
    ? $proficiency
    : 'none';
?>

<button
    class="gmrc-guild-roll-trigger gmrc-guild-roll-trigger--<?php echo esc_attr($variant); ?>"
    type="button"
    data-guild-roll="d20"
    data-roll-label="<?php echo esc_attr($label); ?>"
    data-roll-modifier="<?php echo esc_attr((string) $modifier); ?>"
    data-roll-kind="<?php echo esc_attr($kind); ?>"
    data-roll-source="<?php echo esc_attr($source); ?>"
    data-roll-ability="<?php echo esc_attr($ability); ?>"
    data-roll-proficiency="<?php echo esc_attr($proficiency); ?>"
    aria-label="<?php echo esc_attr(
        sprintf(
            'Roll %s with modifier %s',
            $label,
            $modifierLabel
        )
    ); ?>"
>
    <span class="gmrc-guild-roll-trigger__die" aria-hidden="true">20</span>

    <span class="gmrc-guild-roll-trigger__values">
        <strong><?php echo esc_html($primary); ?></strong>

        <?php if ($secondary !== '') : ?>
            <small><?php echo esc_html($secondary); ?></small>
        <?php endif; ?>
    </span>
</button>
