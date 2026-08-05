<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

$portraitModel = isset($portrait)
    && $portrait instanceof PortraitViewModel
        ? $portrait
        : null;

/*
 * Preserve compatibility with the live Character Creator,
 * which passes primitive values before a Character exists.
 */
$name = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->name()
    : (
        isset($name) && is_scalar($name)
            ? trim((string) $name)
            : ''
    );

$race = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->race()
    : (
        isset($race) && is_scalar($race)
            ? sanitize_key((string) $race)
            : ''
    );

$raceLabel = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->raceLabel()
    : (
        isset($raceLabel) && is_scalar($raceLabel)
            ? trim((string) $raceLabel)
            : ''
    );

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

$classLabel = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->classLabel()
    : (
        isset($classLabel) && is_scalar($classLabel)
            ? trim((string) $classLabel)
            : ''
    );

$mode = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->mode()
    : 'generated';

$layers = $portraitModel instanceof PortraitViewModel
    ? $portraitModel->layers()
    : [];

$customPortraitUrl =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->attachmentUrl()
        : null;

$isCustom =
    $mode === 'custom'
    && is_string($customPortraitUrl)
    && $customPortraitUrl !== '';

$displayName = $name !== ''
    ? $name
    : 'Awaiting Subject';

$displayRace = $raceLabel !== ''
    ? $raceLabel
    : 'Heritage unwritten';

$displayClass = $classLabel !== ''
    ? $classLabel
    : 'Calling unchosen';

$initial = $name !== ''
    ? (
        function_exists('mb_substr')
            ? mb_substr($name, 0, 1)
            : substr($name, 0, 1)
    )
    : '?';

$initial = function_exists('mb_strtoupper')
    ? mb_strtoupper($initial)
    : strtoupper($initial);

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

if (
    $name !== ''
    && $race !== ''
    && $characterClass !== ''
) {
    $stateClasses[] =
        'gmrc-illuminated-portrait--complete';
}

/*
 * Recipe-derived provisional visual variations.
 */
$backgroundVariant =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->variant(
            'background',
            3
        )
        : 1;

$bodyVariant =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->variant(
            'body',
            3
        )
        : 1;

$outfitVariant =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->variant(
            'outfit',
            3
        )
        : 1;

$equipmentVariant =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->variant(
            'equipment',
            3
        )
        : 1;

$effectVariant =
    $portraitModel instanceof PortraitViewModel
        ? $portraitModel->variant(
            'effects',
            3
        )
        : 1;

$uniqueSource =
    ($portraitModel?->seed() ?? '')
    . '|'
    . $name
    . '|'
    . $race
    . '|'
    . $characterClass;

$uniqueId = substr(
    hash('sha256', $uniqueSource),
    0,
    10
);

$backgroundGradientId =
    'gmrc-portrait-background-'
    . $uniqueId;

$silhouetteGradientId =
    'gmrc-portrait-silhouette-'
    . $uniqueId;

$garmentGradientId =
    'gmrc-portrait-garment-'
    . $uniqueId;

$shadowId =
    'gmrc-portrait-shadow-'
    . $uniqueId;

$titleId =
    'gmrc-portrait-title-'
    . $uniqueId;

$backgroundPalettes = [
    1 => [
        '#fff4ce',
        '#e5c884',
        '#a77a3c',
    ],
    2 => [
        '#eef1d2',
        '#b9c48d',
        '#68774b',
    ],
    3 => [
        '#eee1f1',
        '#b48cb8',
        '#604368',
    ],
];

$bodyPalettes = [
    1 => [
        '#705078',
        '#392240',
    ],
    2 => [
        '#77895d',
        '#38482f',
    ],
    3 => [
        '#a25f4e',
        '#593125',
    ],
];

$outfitPalettes = [
    1 => [
        '#9d5162',
        '#5c2433',
    ],
    2 => [
        '#687f50',
        '#344329',
    ],
    3 => [
        '#596f94',
        '#2e3d5a',
    ],
];

$backgroundColours =
    $backgroundPalettes[$backgroundVariant];

$bodyColours =
    $bodyPalettes[$bodyVariant];

$outfitColours =
    $outfitPalettes[$outfitVariant];

$layerAttribute = static function (
    string $slot
) use ($layers): string {
    $value = $layers[$slot] ?? '';

    return is_string($value)
        ? $value
        : '';
};
?>

<figure
    class="<?php echo esc_attr(
        implode(' ', $stateClasses)
    ); ?>"
    data-portrait-studio
    data-portrait-mode="<?php echo esc_attr($mode); ?>"
    data-portrait-race="<?php echo esc_attr($race); ?>"
    data-portrait-class="<?php echo esc_attr(
        $characterClass
    ); ?>"
    data-portrait-seed="<?php echo esc_attr(
        $portraitModel?->seed() ?? ''
    ); ?>"
    data-portrait-background="<?php echo esc_attr(
        $layerAttribute('background')
    ); ?>"
    data-portrait-body="<?php echo esc_attr(
        $layerAttribute('body')
    ); ?>"
    data-portrait-head="<?php echo esc_attr(
        $layerAttribute('head')
    ); ?>"
    data-portrait-eyes="<?php echo esc_attr(
        $layerAttribute('eyes')
    ); ?>"
    data-portrait-mouth="<?php echo esc_attr(
        $layerAttribute('mouth')
    ); ?>"
    data-portrait-palette="<?php echo esc_attr(
        $layerAttribute('palette')
    ); ?>"
    data-portrait-heritage="<?php echo esc_attr(
        $layerAttribute('heritage')
    ); ?>"
    data-portrait-outfit="<?php echo esc_attr(
        $layerAttribute('outfit')
    ); ?>"
    data-portrait-equipment="<?php echo esc_attr(
        $layerAttribute('equipment')
    ); ?>"
    data-portrait-accessory="<?php echo esc_attr(
        $layerAttribute('class_accessory')
    ); ?>"
    data-portrait-frame="<?php echo esc_attr(
        $layerAttribute('frame')
    ); ?>"
    data-portrait-effects="<?php echo esc_attr(
        $layerAttribute('effects')
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
                <svg
                    class="gmrc-portrait-layers"
                    viewBox="0 0 480 600"
                    role="img"
                    aria-labelledby="<?php echo esc_attr(
                        $titleId
                    ); ?>"
                >
                    <title
                        id="<?php echo esc_attr(
                            $titleId
                        ); ?>"
                    >
                        Guild portrait for
                        <?php echo esc_html(
                            $displayName
                        ); ?>
                    </title>

                    <defs>
                        <radialGradient
                            id="<?php echo esc_attr(
                                $backgroundGradientId
                            ); ?>"
                            cx="50%"
                            cy="35%"
                            r="75%"
                        >
                            <stop
                                offset="0%"
                                stop-color="<?php echo esc_attr(
                                    $backgroundColours[0]
                                ); ?>"
                            />

                            <stop
                                offset="70%"
                                stop-color="<?php echo esc_attr(
                                    $backgroundColours[1]
                                ); ?>"
                            />

                            <stop
                                offset="100%"
                                stop-color="<?php echo esc_attr(
                                    $backgroundColours[2]
                                ); ?>"
                            />
                        </radialGradient>

                        <linearGradient
                            id="<?php echo esc_attr(
                                $silhouetteGradientId
                            ); ?>"
                            x1="0%"
                            y1="0%"
                            x2="100%"
                            y2="100%"
                        >
                            <stop
                                offset="0%"
                                stop-color="<?php echo esc_attr(
                                    $bodyColours[0]
                                ); ?>"
                            />

                            <stop
                                offset="100%"
                                stop-color="<?php echo esc_attr(
                                    $bodyColours[1]
                                ); ?>"
                            />
                        </linearGradient>

                        <linearGradient
                            id="<?php echo esc_attr(
                                $garmentGradientId
                            ); ?>"
                            x1="0%"
                            y1="0%"
                            x2="100%"
                            y2="100%"
                        >
                            <stop
                                offset="0%"
                                stop-color="<?php echo esc_attr(
                                    $outfitColours[0]
                                ); ?>"
                            />

                            <stop
                                offset="100%"
                                stop-color="<?php echo esc_attr(
                                    $outfitColours[1]
                                ); ?>"
                            />
                        </linearGradient>

                        <filter
                            id="<?php echo esc_attr(
                                $shadowId
                            ); ?>"
                        >
                            <feDropShadow
                                dx="0"
                                dy="10"
                                stdDeviation="8"
                                flood-color="#3b2418"
                                flood-opacity="0.3"
                            />
                        </filter>
                    </defs>

                    <g
                        class="
                            gmrc-portrait-layer
                            gmrc-portrait-layer--background
                            gmrc-portrait-layer--variant-<?php
                                echo esc_attr(
                                    (string) $backgroundVariant
                                );
                            ?>
                        "
                        data-portrait-layer="background"
                        data-layer-id="<?php echo esc_attr(
                            $layerAttribute(
                                'background'
                            )
                        ); ?>"
                    >
                        <rect
                            x="12"
                            y="12"
                            width="456"
                            height="576"
                            rx="220"
                            fill="url(#<?php echo esc_attr(
                                $backgroundGradientId
                            ); ?>)"
                        />

                        <circle
                            cx="240"
                            cy="235"
                            r="175"
                            fill="none"
                            stroke="#bc8c35"
                            stroke-width="4"
                            stroke-dasharray="3 12"
                            opacity="0.65"
                        />

                        <path
                            d="
                                M55 490
                                C120 430 160 455 205 505
                                C260 445 330 430 425 500
                            "
                            fill="none"
                            stroke="#62744d"
                            stroke-width="9"
                            stroke-linecap="round"
                            opacity="0.44"
                        />
                    </g>

                    <g
                        class="
                            gmrc-portrait-layer
                            gmrc-portrait-layer--race
                            gmrc-portrait-layer--variant-<?php
                                echo esc_attr(
                                    (string) $bodyVariant
                                );
                            ?>
                        "
                        data-portrait-layer="race"
                        data-layer-id="<?php echo esc_attr(
                            $layerAttribute('body')
                        ); ?>"
                        filter="url(#<?php echo esc_attr(
                            $shadowId
                        ); ?>)"
                    >
                        <ellipse
                            cx="240"
                            cy="455"
                            rx="<?php echo esc_attr(
                                $bodyVariant === 2
                                    ? '138'
                                    : (
                                        $bodyVariant === 3
                                            ? '116'
                                            : '126'
                                    )
                            ); ?>"
                            ry="92"
                            fill="url(#<?php echo esc_attr(
                                $silhouetteGradientId
                            ); ?>)"
                        />

                        <path
                            d="
                                M160 430
                                C165 345 190 305 240 295
                                C290 305 315 345 320 430
                                Z
                            "
                            fill="url(#<?php echo esc_attr(
                                $silhouetteGradientId
                            ); ?>)"
                        />

                        <ellipse
                            cx="240"
                            cy="225"
                            rx="<?php echo esc_attr(
                                $bodyVariant === 3
                                    ? '82'
                                    : '92'
                            ); ?>"
                            ry="<?php echo esc_attr(
                                $bodyVariant === 2
                                    ? '118'
                                    : '110'
                            ); ?>"
                            fill="url(#<?php echo esc_attr(
                                $silhouetteGradientId
                            ); ?>)"
                        />

                        <circle
                            cx="207"
                            cy="220"
                            r="8"
                            fill="#f8dfa0"
                        />

                        <circle
                            cx="273"
                            cy="220"
                            r="8"
                            fill="#f8dfa0"
                        />

                        <path
                            d="M211 260 Q240 278 269 260"
                            fill="none"
                            stroke="#f8dfa0"
                            stroke-width="7"
                            stroke-linecap="round"
                        />

                        <text
                            x="240"
                            y="245"
                            text-anchor="middle"
                            class="
                                gmrc-portrait-layers__initial
                            "
                            data-portrait-initial
                        >
                            <?php echo esc_html($initial); ?>
                        </text>
                    </g>

                    <g
                        class="
                            gmrc-portrait-layer
                            gmrc-portrait-layer--class
                            gmrc-portrait-layer--variant-<?php
                                echo esc_attr(
                                    (string) $outfitVariant
                                );
                            ?>
                        "
                        data-portrait-layer="class"
                        data-layer-id="<?php echo esc_attr(
                            $layerAttribute('outfit')
                        ); ?>"
                    >
                        <path
                            d="
                                M145 430
                                C175 360 205 340 240 340
                                C275 340 305 360 335 430
                                L365 545
                                L115 545
                                Z
                            "
                            fill="url(#<?php echo esc_attr(
                                $garmentGradientId
                            ); ?>)"
                            stroke="#f3d58a"
                            stroke-width="5"
                        />

                        <path
                            d="
                                M190 365
                                L240 425
                                L290 365
                            "
                            fill="none"
                            stroke="#f3d58a"
                            stroke-width="8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <circle
                            cx="240"
                            cy="426"
                            r="19"
                            fill="#bc8c35"
                            stroke="#fff0bd"
                            stroke-width="4"
                        />

                        <?php if ($equipmentVariant === 1) : ?>
                            <path
                                class="
                                    gmrc-portrait-layers__equipment
                                "
                                d="
                                    M350 170
                                    L366 185
                                    L250 390
                                    L228 378
                                    Z
                                "
                                fill="#69513f"
                                stroke="#35271e"
                                stroke-width="5"
                            />

                            <path
                                d="M337 156 L382 202"
                                fill="none"
                                stroke="#bc8c35"
                                stroke-width="13"
                                stroke-linecap="round"
                            />
                        <?php elseif ($equipmentVariant === 2) : ?>
                            <path
                                class="
                                    gmrc-portrait-layers__equipment
                                "
                                d="
                                    M338 160
                                    Q410 290 339 430
                                "
                                fill="none"
                                stroke="#78502c"
                                stroke-width="12"
                                stroke-linecap="round"
                            />

                            <path
                                d="
                                    M338 160
                                    Q292 295 339 430
                                "
                                fill="none"
                                stroke="#d7bd7b"
                                stroke-width="3"
                            />
                        <?php else : ?>
                            <path
                                class="
                                    gmrc-portrait-layers__equipment
                                "
                                d="
                                    M348 150
                                    L360 450
                                "
                                fill="none"
                                stroke="#68442b"
                                stroke-width="12"
                                stroke-linecap="round"
                            />

                            <circle
                                cx="348"
                                cy="145"
                                r="30"
                                fill="#7c5790"
                                stroke="#efd58c"
                                stroke-width="7"
                            />
                        <?php endif; ?>
                    </g>

                    <g
                        class="
                            gmrc-portrait-layer
                            gmrc-portrait-layer--effects
                            gmrc-portrait-layer--variant-<?php
                                echo esc_attr(
                                    (string) $effectVariant
                                );
                            ?>
                        "
                        data-portrait-layer="effects"
                        data-layer-id="<?php echo esc_attr(
                            $layerAttribute('effects')
                        ); ?>"
                    >
                        <text
                            x="92"
                            y="150"
                            class="
                                gmrc-portrait-layers__spark
                            "
                        >
                            <?php echo $effectVariant === 2
                                ? '❧'
                                : '✦'; ?>
                        </text>

                        <text
                            x="375"
                            y="290"
                            class="
                                gmrc-portrait-layers__spark
                            "
                        >
                            <?php echo $effectVariant === 3
                                ? '✺'
                                : '✧'; ?>
                        </text>

                        <text
                            x="105"
                            y="390"
                            class="
                                gmrc-portrait-layers__spark
                            "
                        >
                            ✧
                        </text>
                    </g>
                </svg>
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
                    <span aria-hidden="true">🎨</span>

                    <strong>Awaiting subject</strong>

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
                <?php echo esc_html($displayName); ?>
            </strong>

            <span
                class="
                    gmrc-illuminated-portrait__identity
                "
            >
                <span data-portrait-race-label>
                    <?php echo esc_html($displayRace); ?>
                </span>

                <span aria-hidden="true">·</span>

                <span data-portrait-class-label>
                    <?php echo esc_html(
                        $displayClass
                    ); ?>
                </span>
            </span>

            <small data-portrait-status>
                <?php echo esc_html(
                    $isCustom
                        ? 'Custom illumination registered'
                        : (
                            $name !== ''
                            && $race !== ''
                            && $characterClass !== ''
                                ? 'Illumination complete'
                                : 'Portrait awaiting inscription'
                        )
                ); ?>
            </small>
        </figcaption>
    </div>
</figure>
