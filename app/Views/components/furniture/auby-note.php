<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Services\Auby\Quote;

defined('ABSPATH') || exit;

/**
 * Expected variables:
 *
 * @var Quote|null $quote
 */

if (
    !isset($quote)
    || !$quote instanceof Quote
) {
    return;
}

$classes = [
    'auby-note',
    'auby-note--rotation-' . $quote->rotation(),
];

if ($quote->hasCoffeeStain()) {
    $classes[] = 'auby-note--coffee-stained';
}

if ($quote->hasInkBlot()) {
    $classes[] = 'auby-note--ink-blotted';
}
?>

<aside
    class="<?php echo esc_attr(implode(' ', $classes)); ?>"
    aria-label="<?php esc_attr_e("Auby's note", 'gmrc'); ?>"
    data-auby-note
    data-auby-quote-id="<?php echo esc_attr($quote->id()); ?>"
>
    <div class="auby-note__pin" aria-hidden="true">
        <span class="auby-note__pin-head"></span>
        <span class="auby-note__pin-shadow"></span>
    </div>

    <div class="auby-note__paper">
        <div
            class="auby-note__coffee-stain"
            aria-hidden="true"
        ></div>

        <div
            class="auby-note__ink-blot"
            aria-hidden="true"
        ></div>

        <div
            class="auby-note__quill"
            aria-hidden="true"
        >
            <?php
            require dirname(__DIR__) . '/media/auby-quill.php';
            ?>
        </div>

        <div class="auby-note__writing">
            <?php if ($quote->allowsCorrection()) : ?>
                <p class="auby-note__correction">
                    <span class="auby-note__crossed-out">
                        <?php echo esc_html($quote->text()); ?>
                    </span>

                    <span class="auby-note__corrected-text">
                        <?php echo esc_html(
                            $quote->correctionText() ?? ''
                        ); ?>
                    </span>
                </p>
            <?php else : ?>
                <p class="auby-note__quote">
                    <?php echo esc_html($quote->text()); ?>
                </p>
            <?php endif; ?>

            <p class="auby-note__signature">
                <span aria-hidden="true">—</span>
                <?php echo esc_html($quote->author()); ?>
            </p>
        </div>
    </div>
</aside>
