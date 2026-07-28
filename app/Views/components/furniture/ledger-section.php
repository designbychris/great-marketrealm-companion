<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Ledger Section component.
 *
 * Variables:
 *
 * @var string $title
 * @var string $content
 * @var string $eyebrow
 * @var string $description
 * @var string $ornament
 * @var string $class
 */

$title       = isset($title) ? trim((string) $title) : '';
$content     = isset($content) ? (string) $content : '';
$eyebrow     = isset($eyebrow) ? trim((string) $eyebrow) : '';
$description = isset($description) ? trim((string) $description) : '';
$ornament    = isset($ornament) ? trim((string) $ornament) : '✦';
$class       = isset($class) ? trim((string) $class) : '';

$classes = array_filter(
    [
        'ledger-section',
        $class,
    ]
);

if ($title === '' && $content === '') {
    return;
}
?>

<section class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <header class="ledger-section__header">
        <?php if ($eyebrow !== '') : ?>
            <p class="ledger-section__eyebrow">
                <?php echo esc_html($eyebrow); ?>
            </p>
        <?php endif; ?>

        <?php if ($title !== '') : ?>
            <h2 class="ledger-section__title">
                <span
                    class="ledger-section__ornament"
                    aria-hidden="true"
                >
                    <?php echo esc_html($ornament); ?>
                </span>

                <span>
                    <?php echo esc_html($title); ?>
                </span>

                <span
                    class="ledger-section__ornament"
                    aria-hidden="true"
                >
                    <?php echo esc_html($ornament); ?>
                </span>
            </h2>
        <?php endif; ?>

        <?php if ($description !== '') : ?>
            <p class="ledger-section__description">
                <?php echo esc_html($description); ?>
            </p>
        <?php endif; ?>
    </header>

    <div class="ledger-section__body">
        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
</section>
