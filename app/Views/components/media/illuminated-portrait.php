<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitRenderContext;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering\PortraitSvgRenderer;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

/*
 * A persisted Character supplies a PortraitViewModel.
 *
 * The live Character Creator still passes primitive name,
 * Race and Class values before a Character has been saved.
 */
$portraitModel = isset($portrait)
    && $portrait instanceof PortraitViewModel
        ? $portrait
        : null;

/*
 * Resolve the portrait subject's name.
 */
$name = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->name()
    : (
        isset($name)
        && is_scalar($name)
            ? trim(
                (string) $name
            )
            : ''
    );

/*
 * Resolve the canonical Race identifier.
 */
$race = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->race()
    : (
        isset($race)
        && is_scalar($race)
            ? sanitize_key(
                (string) $race
            )
            : ''
    );

/*
 * Resolve the human-readable Race label.
 */
$raceLabel = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->raceLabel()
    : (
        isset($raceLabel)
        && is_scalar($raceLabel)
            ? trim(
                (string) $raceLabel
            )
            : ''
    );

/*
 * Resolve the canonical Class identifier.
 */
$characterClass =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->characterClass()
        : (
            isset($characterClass)
            && is_scalar($characterClass)
                ? sanitize_key(
                    (string) $characterClass
                )
                : ''
        );

/*
 * Resolve the human-readable Class label.
 */
$classLabel = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->classLabel()
    : (
        isset($classLabel)
        && is_scalar($classLabel)
            ? trim(
                (string) $classLabel
            )
            : ''
    );

/*
 * Generated portraits are the default.
 *
 * A persisted PortraitViewModel may instead reference a custom
 * WordPress media attachment.
 */
$mode = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->mode()
    : 'generated';

$customPortraitUrl =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->attachmentUrl()
        : null;

$isCustom =
    $mode === 'custom'
    && is_string($customPortraitUrl)
    && $customPortraitUrl !== '';

/*
 * Build the immutable rendering context consumed by the
 * procedural SVG renderer.
 */
$renderContext =
    $portraitModel instanceof PortraitViewModel
        ? PortraitRenderContext::fromViewModel(
            $portraitModel
        )
        : PortraitRenderContext::provisional(
            $name,
            $race,
            $characterClass
        );

/*
 * Resolve the SVG rendering service through the application
 * container so the view does not construct the layer stack.
 */
$svgRenderer = gmrc()->make(
    PortraitSvgRenderer::class
);

/*
 * Prepare fallback display values used by the caption and
 * accessible custom-image description.
 */
$displayName = $name !== ''
    ? $name
    : 'Awaiting Subject';

$displayRace = $raceLabel !== ''
    ? $raceLabel
    : 'Heritage unwritten';

$displayClass = $classLabel !== ''
    ? $classLabel
    : 'Calling unchosen';

/*
 * Build state classes used by the portrait animations.
 */
$stateClasses = [
    'gmrc-illuminated-portrait',
    'gmrc-illuminated-portrait--mode-'
        . sanitize_html_class($mode),
];

if ($name !== '') {
    $stateClasses[] =
        'gmrc-illuminated-portrait--named';
}

if ($race !== '') {
    $stateClasses[] =
        'gmrc-illuminated-portrait--has-race';
}

if ($characterClass !== '') {
    $stateClasses[] =
        'gmrc-illuminated-portrait--has-class';
}

$isComplete =
    $name !== ''
    && $race !== ''
    && $characterClass !== '';

if ($isComplete) {
    $stateClasses[] =
        'gmrc-illuminated-portrait--complete';
}

/*
 * Determine the caption status.
 */
if ($isCustom) {
    $status =
        'Custom illumination registered';
} elseif ($isComplete) {
    $status =
        'Illumination complete';
} else {
    $status =
        'Portrait awaiting inscription';
}
?>

<figure
    class="<?php echo esc_attr(
        implode(
            ' ',
            $stateClasses
        )
    ); ?>"
    data-portrait-studio
    data-portrait-mode="<?php echo esc_attr(
        $mode
    ); ?>"
    data-portrait-race="<?php echo esc_attr(
        $race
    ); ?>"
    data-portrait-class="<?php echo esc_attr(
        $characterClass
    ); ?>"
    data-portrait-seed="<?php echo esc_attr(
        $renderContext->seed()
    ); ?>"
    data-portrait-background="<?php echo esc_attr(
        $renderContext->layer(
            'background'
        )
    ); ?>"
    data-portrait-body="<?php echo esc_attr(
        $renderContext->layer(
            'body'
        )
    ); ?>"
    data-portrait-head="<?php echo esc_attr(
        $renderContext->layer(
            'head'
        )
    ); ?>"
    data-portrait-eyes="<?php echo esc_attr(
        $renderContext->layer(
            'eyes'
        )
    ); ?>"
    data-portrait-mouth="<?php echo esc_attr(
        $renderContext->layer(
            'mouth'
        )
    ); ?>"
    data-portrait-palette="<?php echo esc_attr(
        $renderContext->layer(
            'palette'
        )
    ); ?>"
    data-portrait-heritage="<?php echo esc_attr(
        $renderContext->layer(
            'heritage'
        )
    ); ?>"
    data-portrait-outfit="<?php echo esc_attr(
        $renderContext->layer(
            'outfit'
        )
    ); ?>"
    data-portrait-equipment="<?php echo esc_attr(
        $renderContext->layer(
            'equipment'
        )
    ); ?>"
    data-portrait-accessory="<?php echo esc_attr(
        $renderContext->layer(
            'class_accessory'
        )
    ); ?>"
    data-portrait-frame="<?php echo esc_attr(
        $renderContext->layer(
            'frame'
        )
    ); ?>"
    data-portrait-effects="<?php echo esc_attr(
        $renderContext->layer(
            'effects'
        )
    ); ?>"
>
    <div class="gmrc-illuminated-portrait__frame">
        <span
            class="
                gmrc-illuminated-portrait__corner
                gmrc-illuminated-portrait__corner--top-left
            "
            aria-hidden="true"
        >
            ✦
        </span>

        <span
            class="
                gmrc-illuminated-portrait__corner
                gmrc-illuminated-portrait__corner--top-right
            "
            aria-hidden="true"
        >
            ❧
        </span>

        <span
            class="
                gmrc-illuminated-portrait__corner
                gmrc-illuminated-portrait__corner--bottom-left
            "
            aria-hidden="true"
        >
            ❧
        </span>

        <span
            class="
                gmrc-illuminated-portrait__corner
                gmrc-illuminated-portrait__corner--bottom-right
            "
            aria-hidden="true"
        >
            ✦
        </span>

        <div class="gmrc-illuminated-portrait__canvas">
            <?php if ($isCustom) : ?>
                <img
                    class="
                        gmrc-illuminated-portrait__custom-image
                    "
                    src="<?php echo esc_url(
                        $customPortraitUrl
                    ); ?>"
                    alt="<?php echo esc_attr(
                        sprintf(
                            'Custom Guild portrait of %s',
                            $displayName
                        )
                    ); ?>"
                    loading="lazy"
                    decoding="async"
                >
            <?php else : ?>
                <?php
                echo $svgRenderer->render(
                    $renderContext
                ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            <?php endif; ?>

            <?php if (
                ! $isCustom
                && $race === ''
            ) : ?>
                <div
                    class="
                        gmrc-illuminated-portrait__waiting
                    "
                    data-portrait-waiting
                >
                    <span aria-hidden="true">
                        🎨
                    </span>

                    <strong>
                        Awaiting subject
                    </strong>

                    <small>
                        Choose a heritage and Guild calling to
                        awaken the Illuminator’s canvas.
                    </small>
                </div>
            <?php endif; ?>
        </div>

        <figcaption
            class="
                gmrc-illuminated-portrait__caption
            "
        >
            <p class="gmrc-eyebrow">
                The Guild Illuminator
            </p>

            <strong data-portrait-name>
                <?php echo esc_html(
                    $displayName
                ); ?>
            </strong>

            <span
                class="
                    gmrc-illuminated-portrait__identity
                "
            >
                <span data-portrait-race-label>
                    <?php echo esc_html(
                        $displayRace
                    ); ?>
                </span>

                <span aria-hidden="true">
                    ·
                </span>

                <span data-portrait-class-label>
                    <?php echo esc_html(
                        $displayClass
                    ); ?>
                </span>
            </span>

            <small data-portrait-status>
                <?php echo esc_html(
                    $status
                ); ?>
            </small>
        </figcaption>
    </div>
</figure>
