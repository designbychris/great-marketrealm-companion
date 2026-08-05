<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$name = isset($name) && is_scalar($name)
    ? trim((string) $name)
    : '';

$race = isset($race) && is_scalar($race)
    ? sanitize_key((string) $race)
    : '';

$raceLabel = isset($raceLabel) && is_scalar($raceLabel)
    ? trim((string) $raceLabel)
    : '';

$characterClass = isset($characterClass)
    && is_scalar($characterClass)
        ? sanitize_key((string) $characterClass)
        : '';

$classLabel = isset($classLabel) && is_scalar($classLabel)
    ? trim((string) $classLabel)
    : '';

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
?>

<figure
    class="<?php echo esc_attr(
        implode(' ', $stateClasses)
    ); ?>"
    data-portrait-studio
    data-portrait-race="<?php echo esc_attr($race); ?>"
    data-portrait-class="<?php echo esc_attr(
        $characterClass
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
            <svg
                class="gmrc-portrait-layers"
                viewBox="0 0 480 600"
                role="img"
                aria-labelledby="gmrc-portrait-title"
            >
                <title id="gmrc-portrait-title">
                    Provisional Guild portrait for
                    <?php echo esc_html($displayName); ?>
                </title>

                <defs>
                    <radialGradient
                        id="gmrc-portrait-background"
                        cx="50%"
                        cy="35%"
                        r="75%"
                    >
                        <stop
                            offset="0%"
                            stop-color="#fff4ce"
                        />

                        <stop
                            offset="70%"
                            stop-color="#e5c884"
                        />

                        <stop
                            offset="100%"
                            stop-color="#a77a3c"
                        />
                    </radialGradient>

                    <linearGradient
                        id="gmrc-portrait-silhouette"
                        x1="0%"
                        y1="0%"
                        x2="100%"
                        y2="100%"
                    >
                        <stop
                            offset="0%"
                            stop-color="#705078"
                        />

                        <stop
                            offset="100%"
                            stop-color="#392240"
                        />
                    </linearGradient>

                    <linearGradient
                        id="gmrc-portrait-garment"
                        x1="0%"
                        y1="0%"
                        x2="100%"
                        y2="100%"
                    >
                        <stop
                            offset="0%"
                            stop-color="#9d5162"
                        />

                        <stop
                            offset="100%"
                            stop-color="#5c2433"
                        />
                    </linearGradient>

                    <filter id="gmrc-portrait-soft-shadow">
                        <feDropShadow
                            dx="0"
                            dy="10"
                            stdDeviation="8"
                            flood-color="#3b2418"
                            flood-opacity="0.3"
                        />
                    </filter>
                </defs>

                <!-- Background layer -->
                <g
                    class="
                        gmrc-portrait-layer
                        gmrc-portrait-layer--background
                    "
                    data-portrait-layer="background"
                >
                    <rect
                        x="12"
                        y="12"
                        width="456"
                        height="576"
                        rx="220"
                        fill="url(#gmrc-portrait-background)"
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

                <!-- Race/body layer -->
                <g
                    class="
                        gmrc-portrait-layer
                        gmrc-portrait-layer--race
                    "
                    data-portrait-layer="race"
                    filter="url(#gmrc-portrait-soft-shadow)"
                >
                    <ellipse
                        cx="240"
                        cy="455"
                        rx="126"
                        ry="92"
                        fill="url(#gmrc-portrait-silhouette)"
                    />

                    <path
                        d="
                            M160 430
                            C165 345 190 305 240 295
                            C290 305 315 345 320 430
                            Z
                        "
                        fill="url(#gmrc-portrait-silhouette)"
                    />

                    <ellipse
                        cx="240"
                        cy="225"
                        rx="92"
                        ry="110"
                        fill="url(#gmrc-portrait-silhouette)"
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
                        class="gmrc-portrait-layers__initial"
                        data-portrait-initial
                    >
                        <?php echo esc_html($initial); ?>
                    </text>
                </g>

                <!-- Class garment and equipment layer -->
                <g
                    class="
                        gmrc-portrait-layer
                        gmrc-portrait-layer--class
                    "
                    data-portrait-layer="class"
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
                        fill="url(#gmrc-portrait-garment)"
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
                        d="
                            M337 156
                            L382 202
                        "
                        fill="none"
                        stroke="#bc8c35"
                        stroke-width="13"
                        stroke-linecap="round"
                    />
                </g>

                <!-- Foreground magic/effects layer -->
                <g
                    class="
                        gmrc-portrait-layer
                        gmrc-portrait-layer--effects
                    "
                    data-portrait-layer="effects"
                >
                    <text
                        x="92"
                        y="150"
                        class="gmrc-portrait-layers__spark"
                    >
                        ✦
                    </text>

                    <text
                        x="375"
                        y="290"
                        class="gmrc-portrait-layers__spark"
                    >
                        ✧
                    </text>

                    <text
                        x="105"
                        y="390"
                        class="gmrc-portrait-layers__spark"
                    >
                        ✧
                    </text>
                </g>
            </svg>

            <div
                class="gmrc-illuminated-portrait__waiting"
                data-portrait-waiting
            >
                <span aria-hidden="true">🎨</span>

                <strong>Awaiting subject</strong>

                <small>
                    Choose a heritage and Guild calling to awaken
                    the Illuminator’s canvas.
                </small>
            </div>
        </div>

        <figcaption class="gmrc-illuminated-portrait__caption">
            <p class="gmrc-eyebrow">
                The Guild Illuminator
            </p>

            <strong data-portrait-name>
                <?php echo esc_html($displayName); ?>
            </strong>

            <span class="gmrc-illuminated-portrait__identity">
                <span data-portrait-race-label>
                    <?php echo esc_html($displayRace); ?>
                </span>

                <span aria-hidden="true">·</span>

                <span data-portrait-class-label>
                    <?php echo esc_html($displayClass); ?>
                </span>
            </span>

            <small data-portrait-status>
                Portrait awaiting inscription
            </small>
        </figcaption>
    </div>
</figure>
