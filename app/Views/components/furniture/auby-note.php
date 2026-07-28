<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Services\Auby\Quote;

defined('ABSPATH') || exit;

/**
 * @var Quote|null $quote
 */

if (
    ! isset($quote)
    || ! $quote instanceof Quote
) {
    return;
}

$variationSeed = crc32(
    $quote->text()
);

$rotation = (($variationSeed % 5) - 2) / 2;
?>

<aside
    class="auby-note"
    style="--auby-note-rotation: <?php echo esc_attr((string) $rotation); ?>deg;"
    aria-label="A note from Auby"
>
    <span
        class="auby-note__pin"
        aria-hidden="true"
    ></span>

    <div class="auby-note__paper">
        <div class="auby-note__writing">
            <p class="auby-note__quote">
                <?php echo esc_html($quote->text()); ?>
            </p>

            <p class="auby-note__signature">
                <span aria-hidden="true">—</span>
                <?php echo esc_html($quote->author()); ?>
            </p>
        </div>

        <div
            class="auby-note__quill"
            aria-hidden="true"
        >
            <!-- Keep your existing inline SVG quill here -->
        </div>
    </div>
</aside>
