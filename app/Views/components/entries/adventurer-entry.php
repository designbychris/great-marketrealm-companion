<?php

declare(strict_types=1);

/**
 * Adventurer card component.
 *
 * Variables:
 *
 * @var \GreatMarketrealmCompanion\Modules\Characters\Models\Character $character
 * @var string $companionUrl
 * @var GuildSealRegistry $sealRegistry
 */

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;

defined('ABSPATH') || exit;

if (
    ! isset($character)
    || ! $character instanceof Character
    || ! isset($companionUrl)
    || ! is_string($companionUrl)
    || ! isset($sealRegistry)
    || ! $sealRegistry instanceof GuildSealRegistry
) {
    return;
}

$characterId = $character
    ->id()
    ->value();

$name = $character
    ->name()
    ->value();

$race = $character
    ->race()
    ->label();

$characterClass = $character
    ->characterClass()
    ->label();

$level = $character
    ->level()
    ->value();

$viewUrl = add_query_arg(
    'gmrc_route',
    'characters/' . rawurlencode($characterId),
    $companionUrl
);

$editUrl = add_query_arg(
    'gmrc_route',
    'characters/'
        . rawurlencode($characterId)
        . '/edit',
    $companionUrl
);

/*
 * Use the final six characters of the ULID as a compact
 * register reference suitable for display on the card.
 */
$entryReference = strtoupper(
    substr(
        $characterId,
        -6
    )
);

/*
 * Build a portrait initial.
 *
 * mb_substr() is used when available so names beginning with
 * multibyte characters are handled correctly.
 */
$initial = function_exists('mb_substr')
    ? mb_substr($name, 0, 1)
    : substr($name, 0, 1);

$initial = function_exists('mb_strtoupper')
    ? mb_strtoupper($initial)
    : strtoupper($initial);

$displayTitleParts = array_filter(
    [
        $race,
        $characterClass,
    ]
);

$displayTitle = implode(
    ' · ',
    $displayTitleParts
);

$guildSeal = $sealRegistry->for(
    $characterClass
);
?>

<article class="adventurer-card">
    <div class="adventurer-card__visual">
        <span
            class="ledger-bookmark"
            aria-label="<?php echo esc_attr(
                sprintf(
                    'Level %d',
                    $level
                )
            ); ?>"
        >
            <span class="ledger-bookmark__label">
                Level
            </span>

            <strong class="ledger-bookmark__value">
                <?php echo esc_html((string) $level); ?>
            </strong>
        </span>

        <figure class="portrait-frame portrait-frame--card">
            <div class="portrait-frame__inner">
                <span
                    class="portrait-frame__initials"
                    aria-hidden="true"
                >
                    <?php echo esc_html($initial); ?>
                </span>
            </div>

            <figcaption class="portrait-frame__caption">
                Registered Adventurer
            </figcaption>
        </figure>
    </div>

    <div class="adventurer-card__content">
        <div class="adventurer-card__heading">
            <?php
            echo $this->component(
                'components.media.guild-seal',
                $guildSeal
            );
            ?>

            <div class="adventurer-card__identity">
                <p class="adventurer-card__kicker">
                    Entry No.
                    <?php echo esc_html($entryReference); ?>
                </p>

                <h2 class="adventurer-card__name">
                    <a href="<?php echo esc_url($viewUrl); ?>">
                        <?php echo esc_html($name); ?>
                    </a>
                </h2>

                <?php if ($displayTitle !== '') : ?>
                    <p class="adventurer-card__title">
                        <?php echo esc_html($displayTitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div
            class="recipe-divider recipe-divider--compact"
            aria-hidden="true"
        >
            <span class="recipe-divider__ornament">
                ✦
            </span>
        </div>

        <div
            class="adventurer-card__ingredients"
            aria-label="Adventurer details"
        >
            <span
                class="
                    ingredient-badge
                    ingredient-badge--race
                "
            >
                <span
                    class="ingredient-badge__label"
                    aria-hidden="true"
                >
                    Race
                </span>

                <?php echo esc_html($race); ?>
            </span>

            <span
                class="
                    ingredient-badge
                    ingredient-badge--class
                "
            >
                <span
                    class="ingredient-badge__label"
                    aria-hidden="true"
                >
                    Class
                </span>

                <?php echo esc_html($characterClass); ?>
            </span>
        </div>

        <dl class="adventurer-card__features">
            <div class="adventurer-card__feature">
                <dt>Character sheet</dt>
                <dd>Ready to explore</dd>
            </div>

            <div class="adventurer-card__feature">
                <dt>Kit bag</dt>
                <dd>Awaiting inventory</dd>
            </div>

            <div class="adventurer-card__feature">
                <dt>Guild seals</dt>
                <dd>None recorded yet</dd>
            </div>
        </dl>

        <footer class="adventurer-card__actions">
            <?php
            echo $this->component(
                'components.controls.wax-button',
                [
                    'label' => 'Open Ledger',
                    'href' => $viewUrl,
                    'symbol' => '✦',
                    'variant' => 'wax',
                    'size' => 'medium',
                ]
            );
            ?>

            <?php
            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Edit Adventurer',
                    'href' => $editUrl,
                    'symbol' => '✎',
                    'variant' => 'parchment',
                    'size' => 'medium',
                ]
            );
            ?>
        </footer>
    </div>
</article>
