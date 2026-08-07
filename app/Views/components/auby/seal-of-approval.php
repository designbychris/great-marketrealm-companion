<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$variant = sanitize_key(
    (string) ($variant ?? 'ink')
);

$context = sanitize_key(
    (string) ($context ?? 'default')
);

$trigger = sanitize_key(
    (string) ($trigger ?? 'visible')
);

$allowedVariants = [
    'ink',
    'one-colour',
    'embossed',
    'gold',
];

if (! in_array($variant, $allowedVariants, true)) {
    $variant = 'ink';
}

$allowedTriggers = [
    'visible',
    'manual',
    'static',
];

if (! in_array($trigger, $allowedTriggers, true)) {
    $trigger = 'visible';
}

$asset = match ($variant) {
    'gold' =>
        'seal-of-approval-gold.svg',
    'embossed' =>
        'seal-of-approval-embossed.svg',
    'one-colour' =>
        'seal-of-approval-one-colour.svg',
    default =>
        'seal-of-approval.svg',
};

$classes = [
    'gmrc-auby-seal',
    'gmrc-auby-seal--' . $variant,
    'gmrc-auby-seal--' . $context,
];
?>

<span
    class="<?php echo esc_attr(
        implode(' ', $classes)
    ); ?>"
    data-auby-seal
    data-auby-seal-trigger="<?php echo esc_attr(
        $trigger
    ); ?>"
    role="img"
    aria-label="<?php echo esc_attr__(
        'Auby Seal of Approval',
        'great-marketrealm-companion'
    ); ?>"
>
    <span
        class="gmrc-auby-seal__splatter"
        aria-hidden="true"
    >
        <img
            src="<?php echo esc_url(
                GMRC_URL
                    . 'assets/images/auby/seals/'
                    . 'seal-ink-splatter.svg'
            ); ?>"
            alt=""
        >
    </span>

    <span class="gmrc-auby-seal__mark">
        <img
            src="<?php echo esc_url(
                GMRC_URL
                    . 'assets/images/auby/seals/'
                    . $asset
            ); ?>"
            alt=""
        >
    </span>
</span>
