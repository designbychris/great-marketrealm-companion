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
?>

<button
    class="gmrc-guild-roll-trigger gmrc-guild-roll-trigger--<?php echo esc_attr($variant); ?>"
    type="button"
    data-guild-roll="d20"
    data-roll-label="<?php echo esc_attr($label); ?>"
    data-roll-modifier="<?php echo esc_attr((string) $modifier); ?>"
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
