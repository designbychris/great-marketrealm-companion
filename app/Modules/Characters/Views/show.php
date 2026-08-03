<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;

defined('ABSPATH') || exit;

if (
    ! isset($character)
    || ! $character instanceof Character
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

$experience = $character
    ->experience()
    ->value();

$armourClass = $character
    ->armourClass()
    ->value();

$proficiencyBonus = $character
    ->proficiencyBonus()
    ->signed();

$initiative = $character
    ->initiative()
    ->signed();

$speed = $character
    ->speed()
    ->formatted();

$hitPoints = $character->hitPoints();

$abilityScores = $character
    ->abilityScores();

$companionUrl = home_url(
    '/companion/'
);

$charactersUrl = add_query_arg(
    'gmrc_route',
    'characters',
    $companionUrl
);

$editUrl = add_query_arg(
    'gmrc_route',
    'characters/'
        . rawurlencode($characterId)
        . '/edit',
    $companionUrl
);

$entryReference = strtoupper(
    substr(
        $characterId,
        -6
    )
);

$guildSeal = $sealRegistry->for(
    $characterClass
);

$initial = function_exists('mb_substr')
    ? mb_substr($name, 0, 1)
    : substr($name, 0, 1);

$initial = function_exists('mb_strtoupper')
    ? mb_strtoupper($initial)
    : strtoupper($initial);

$abilities = [
    'Strength' => $abilityScores->strength(),
    'Dexterity' => $abilityScores->dexterity(),
    'Constitution' => $abilityScores->constitution(),
    'Intelligence' => $abilityScores->intelligence(),
    'Wisdom' => $abilityScores->wisdom(),
    'Charisma' => $abilityScores->charisma(),
];
?>

<section class="gmrc-character-sheet">
    <header class="gmrc-page-header">
        <div class="gmrc-page-header__content">
            <p class="gmrc-eyebrow">
                Adventurer’s Register · Entry
                <?php echo esc_html($entryReference); ?>
            </p>

            <h1>
                <?php echo esc_html($name); ?>
            </h1>

            <p>
                Level <?php echo esc_html((string) $level); ?>
                <?php echo esc_html($race); ?>
                <?php echo esc_html($characterClass); ?>
            </p>
        </div>

        <div class="gmrc-page-header__actions">
            <?php
            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Return to Register',
                    'href' => $charactersUrl,
                    'symbol' => '‹',
                    'variant' => 'parchment',
                    'size' => 'medium',
                ]
            );

            echo $this->component(
                'components.controls.wax-button',
                [
                    'label' => 'Edit Adventurer',
                    'href' => $editUrl,
                    'symbol' => '✎',
                    'variant' => 'wax',
                    'size' => 'medium',
                ]
            );
            ?>
        </div>
    </header>

    <div class="gmrc-character-sheet__identity">
        <figure class="portrait-frame portrait-frame--sheet">
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

        <div class="gmrc-character-sheet__summary">
            <div class="gmrc-character-sheet__seal">
                <?php
                echo $this->component(
                    'components.media.guild-seal',
                    $guildSeal
                );
                ?>
            </div>

            <dl class="gmrc-definition-list">
                <div>
                    <dt>Name</dt>
                    <dd><?php echo esc_html($name); ?></dd>
                </div>

                <div>
                    <dt>Race</dt>
                    <dd><?php echo esc_html($race); ?></dd>
                </div>

                <div>
                    <dt>Class</dt>
                    <dd><?php echo esc_html($characterClass); ?></dd>
                </div>

                <div>
                    <dt>Level</dt>
                    <dd><?php echo esc_html((string) $level); ?></dd>
                </div>

                <div>
                    <dt>Experience</dt>
                    <dd><?php echo esc_html((string) $experience); ?></dd>
                </div>

                <div>
                    <dt>Register reference</dt>
                    <dd><?php echo esc_html($entryReference); ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <div
        class="recipe-divider"
        aria-hidden="true"
    >
        <span class="recipe-divider__ornament">
            ✦
        </span>
    </div>

    <div class="gmrc-character-sheet__panels">
        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Combat Measures
                </p>
        
                <h2>Core Statistics</h2>
            </header>
        
            <dl class="gmrc-stat-grid gmrc-stat-grid--four">
                <div class="gmrc-stat-card">
                    <dt>Armour Class</dt>
        
                    <dd>
                        <?php echo esc_html(
                            (string) $armourClass
                        ); ?>
                    </dd>
                </div>
        
                <div class="gmrc-stat-card">
                    <dt>Initiative</dt>
        
                    <dd>
                        <?php echo esc_html(
                            $initiative
                        ); ?>
                    </dd>
                </div>
        
                <div class="gmrc-stat-card">
                    <dt>Speed</dt>
        
                    <dd>
                        <?php echo esc_html(
                            $speed
                        ); ?>
                    </dd>
                </div>
        
                <div class="gmrc-stat-card">
                    <dt>Proficiency Bonus</dt>
        
                    <dd>
                        <?php echo esc_html(
                            $proficiencyBonus
                        ); ?>
                    </dd>
                </div>
            </dl>
        </section>
        
        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Vital Measures
                </p>

                <h2>Hit Points</h2>
            </header>

            <dl class="gmrc-stat-grid gmrc-stat-grid--three">
                <div class="gmrc-stat-card">
                    <dt>Current</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $hitPoints->current()
                        ); ?>
                    </dd>
                </div>

                <div class="gmrc-stat-card">
                    <dt>Maximum</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $hitPoints->maximum()
                        ); ?>
                    </dd>
                </div>

                <div class="gmrc-stat-card">
                    <dt>Temporary</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $hitPoints->temporary()
                        ); ?>
                    </dd>
                </div>
            </dl>

            <p class="gmrc-status-note">
                Status:
                <strong>
                    <?php echo esc_html(
                        $character->isConscious()
                            ? 'Conscious'
                            : 'Unconscious'
                    ); ?>
                </strong>
            </p>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Adventuring Measures
                </p>

                <h2>Ability Scores</h2>
            </header>

            <dl class="gmrc-stat-grid gmrc-stat-grid--abilities">
                <?php foreach ($abilities as $label => $score) : ?>
                    <div class="gmrc-stat-card">
                        <dt>
                            <?php echo esc_html($label); ?>
                        </dt>

                        <dd>
                            <strong>
                                <?php echo esc_html(
                                    (string) $score->value()
                                ); ?>
                            </strong>

                            <span>
                                <?php
                                $modifier = $score->modifier();

                                echo esc_html(
                                    $modifier >= 0
                                        ? '+' . $modifier
                                        : (string) $modifier
                                );
                                ?>
                            </span>
                        </dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </section>
    </div>

    <section class="gmrc-ledger-panel gmrc-ledger-panel--placeholder">
        <header class="gmrc-ledger-panel__header">
            <p class="gmrc-eyebrow">
                Pages Yet to Be Written
            </p>

            <h2>Adventuring Record</h2>
        </header>

        <div class="gmrc-placeholder-grid">
            <article>
                <h3>Inventory</h3>
                <p>
                    The adventurer’s kit bag is awaiting its first
                    recorded items.
                </p>
            </article>

            <article>
                <h3>Features</h3>
                <p>
                    Race and class features will be recorded here as
                    the Archive expands.
                </p>
            </article>

            <article>
                <h3>Achievements</h3>
                <p>
                    Guild honours and Marketrealm achievements will
                    appear here.
                </p>
            </article>
        </div>
    </section>
</section>
