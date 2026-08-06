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

$classes = [
    'auby-note',
];

if ($quote->hasCoffeeStain()) {
    $classes[] = 'auby-note--coffee-stain';
}

if ($quote->hasInkBlot()) {
    $classes[] = 'auby-note--ink-blot';
}

if ($quote->allowsCorrection()) {
    $classes[] = 'auby-note--corrected';
}

$rotation = $quote->rotation();
?>

<aside
    class="<?php echo esc_attr(
        implode(' ', $classes)
    ); ?>"
    style="<?php echo esc_attr(
        '--auby-note-rotation: '
            . $rotation
            . 'deg;'
    ); ?>"
    aria-label="A note from Auby"
    data-auby-note
>
    <span
        class="auby-note__pin"
        aria-hidden="true"
    ></span>

    <div class="auby-note__paper">
        <span
            class="auby-note__coffee-stain"
            aria-hidden="true"
        ></span>

        <span
            class="auby-note__ink-blot"
            aria-hidden="true"
        ></span>

        <div
            class="auby-note__portrait"
            aria-hidden="true"
        >
            <img
                class="auby-note__portrait-image"
                src="<?php echo esc_url(
                    GMRC_URL
                        . 'assets/images/auby/'
                        . 'auby-note-face.svg'
                ); ?>"
                alt=""
                width="64"
                height="64"
            >
        </div>

        <div class="auby-note__writing">
            <p
                class="auby-note__quote"
                data-auby-quote
            >
                <?php echo esc_html(
                    $quote->text()
                ); ?>
            </p>

            <p
                class="auby-note__correction"
                data-auby-correction
                <?php echo $quote->allowsCorrection()
                    ? ''
                    : 'hidden'; ?>
            >
                <?php echo esc_html(
                    $quote->correctionText()
                        ?? ''
                ); ?>
            </p>

            <p class="auby-note__signature">
                <span aria-hidden="true">—</span>

                <span data-auby-author>
                    <?php echo esc_html(
                        $quote->author()
                    ); ?>
                </span>
            </p>
        </div>

        <div
            class="auby-note__quill"
            aria-hidden="true"
        >
            <span class="auby-note__quill-feather">
                🪶
            </span>
        </div>
    </div>
</aside>
