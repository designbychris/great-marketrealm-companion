<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;

defined('ABSPATH') || exit;

if (
    ! isset($character)
    || ! $character instanceof Character
    || ! isset($sealRegistry)
    || ! $sealRegistry instanceof GuildSealRegistry
    || ! isset($portrait)
    || ! $portrait instanceof PortraitViewModel
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

$background = $character
    ->background()
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

$passivePerception = $character
    ->passivePerception()
    ->value();

$hitPoints = $character->hitPoints();

$savingThrows = $character
    ->savingThrows();

$skills = $character
    ->skills();

$languages = $character
    ->languages();

$toolProficiencies = $character
    ->toolProficiencies();

$conditions = $character
    ->conditions();

$savingThrowLabels = [
    'strength' => 'Strength',
    'dexterity' => 'Dexterity',
    'constitution' => 'Constitution',
    'intelligence' => 'Intelligence',
    'wisdom' => 'Wisdom',
    'charisma' => 'Charisma',
];

$skillLabels = [
    'acrobatics' => 'Acrobatics',
    'animal-handling' => 'Animal Handling',
    'arcana' => 'Arcana',
    'athletics' => 'Athletics',
    'deception' => 'Deception',
    'history' => 'History',
    'insight' => 'Insight',
    'intimidation' => 'Intimidation',
    'investigation' => 'Investigation',
    'medicine' => 'Medicine',
    'nature' => 'Nature',
    'perception' => 'Perception',
    'performance' => 'Performance',
    'persuasion' => 'Persuasion',
    'religion' => 'Religion',
    'sleight-of-hand' => 'Sleight of Hand',
    'stealth' => 'Stealth',
    'survival' => 'Survival',
];

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
                · <?php echo esc_html($background); ?>
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
        <div class="gmrc-character-sheet__portrait">
            <?php
            echo $this->component(
                'components.media.illuminated-portrait',
                [
                    'portrait' => $portrait,
                ]
            );
            ?>
        </div>

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
                    <dt>Background</dt>
                    <dd><?php echo esc_html($background); ?></dd>
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

                <div class="gmrc-stat-card">
                    <dt>Passive Perception</dt>

                    <dd>
                        <?php echo esc_html(
                            (string) $passivePerception
                        ); ?>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Defensive Measures
                </p>

                <h2>Saving Throws</h2>
            </header>

            <dl class="gmrc-stat-grid gmrc-stat-grid--abilities">
                <?php foreach ($savingThrowLabels as $ability => $label) : ?>
                    <?php
                    $savingThrow = $savingThrows->get(
                        $ability
                    );
                    ?>

                    <div
                        class="
                            gmrc-stat-card
                            <?php echo $savingThrow->isProficient()
                                ? 'gmrc-stat-card--proficient'
                                : ''; ?>
                        "
                    >
                        <dt>
                            <?php if ($savingThrow->isProficient()) : ?>
                                <span
                                    class="gmrc-proficiency-marker"
                                    aria-label="Proficient"
                                    title="Proficient"
                                >
                                    ●
                                </span>
                            <?php endif; ?>

                            <?php echo esc_html($label); ?>
                        </dt>

                        <dd>
                            <?php echo esc_html(
                                $savingThrow->signed()
                            ); ?>
                        </dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <p class="gmrc-status-note">
                <span aria-hidden="true">●</span>
                indicates a proficient saving throw.
            </p>
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

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Trained Talents
                </p>

                <h2>Skills</h2>
            </header>

            <dl class="gmrc-stat-grid gmrc-stat-grid--abilities">
                <?php foreach ($skillLabels as $identifier => $label) : ?>
                    <?php
                    $skill = $skills->get(
                        $identifier
                    );

                    $skillClass = '';

                    if ($skill->hasExpertise()) {
                        $skillClass =
                            'gmrc-stat-card--expertise';
                    } elseif ($skill->isProficient()) {
                        $skillClass =
                            'gmrc-stat-card--proficient';
                    }
                    ?>

                    <div
                        class="
                            gmrc-stat-card
                            <?php echo esc_attr(
                                $skillClass
                            ); ?>
                        "
                    >
                        <dt>
                            <?php if ($skill->hasExpertise()) : ?>
                                <span
                                    class="gmrc-proficiency-marker"
                                    aria-label="Expertise"
                                    title="Expertise"
                                >
                                    ◆
                                </span>
                            <?php elseif ($skill->isProficient()) : ?>
                                <span
                                    class="gmrc-proficiency-marker"
                                    aria-label="Proficient"
                                    title="Proficient"
                                >
                                    ●
                                </span>
                            <?php endif; ?>

                            <?php echo esc_html($label); ?>
                        </dt>

                        <dd>
                            <?php echo esc_html(
                                $skill->signed()
                            ); ?>
                        </dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <p class="gmrc-status-note">
                <span aria-hidden="true">●</span>
                indicates proficiency.
                <span aria-hidden="true">◆</span>
                indicates expertise.
            </p>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Personal History
                </p>

                <h2>Background</h2>
            </header>

            <dl class="gmrc-definition-list">
                <div>
                    <dt>Background</dt>

                    <dd>
                        <?php echo esc_html($background); ?>
                    </dd>
                </div>

                <div>
                    <dt>Additional language choices</dt>

                    <dd>
                        <?php echo esc_html(
                            (string) $character
                                ->background()
                                ->languageChoices()
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Skill proficiencies</dt>

                    <dd>
                        <?php
                        $backgroundSkills = array_map(
                            static fn (
                                string $skill
                            ): string => $skillLabels[$skill]
                                ?? ucwords(
                                    str_replace(
                                        '-',
                                        ' ',
                                        $skill
                                    )
                                ),
                            $character
                                ->background()
                                ->skillProficiencies()
                                ->proficiencies()
                        );

                        echo esc_html(
                            $backgroundSkills !== []
                                ? implode(
                                    ', ',
                                    $backgroundSkills
                                )
                                : 'None'
                        );
                        ?>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Spoken and Written Knowledge
                </p>

                <h2>Languages</h2>
            </header>

            <?php if ($languages->isEmpty()) : ?>
                <p class="gmrc-status-note">
                    No fixed languages are currently recorded.
                    This background permits
                    <?php echo esc_html(
                        (string) $character
                            ->background()
                            ->languageChoices()
                    ); ?>
                    additional language
                    <?php echo $character
                        ->background()
                        ->languageChoices() === 1
                            ? 'choice'
                            : 'choices'; ?>.
                </p>
            <?php else : ?>
                <ul class="gmrc-tag-list">
                    <?php foreach ($languages->all() as $language) : ?>
                        <li class="gmrc-tag-list__item">
                            <?php echo esc_html(
                                $language->label()
                            ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Trades and Practical Knowledge
                </p>

                <h2>Tool Proficiencies</h2>
            </header>

            <?php if ($toolProficiencies->isEmpty()) : ?>
                <p class="gmrc-status-note">
                    No tool proficiencies are recorded.
                </p>
            <?php else : ?>
                <ul class="gmrc-tag-list">
                    <?php
                    foreach (
                        $toolProficiencies->all()
                        as $tool
                    ) :
                        ?>
                        <li
                            class="
                                gmrc-tag-list__item
                                <?php echo $tool->isChoiceCategory()
                                    ? 'gmrc-tag-list__item--choice'
                                    : ''; ?>
                            "
                        >
                            <?php echo esc_html(
                                $tool->label()
                            ); ?>

                            <?php if ($tool->isChoiceCategory()) : ?>
                                <span class="gmrc-tag-list__note">
                                    Choice required
                                </span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (
                    $toolProficiencies
                        ->hasUnresolvedChoices()
                ) : ?>
                    <p class="gmrc-status-note">
                        One or more background tool choices
                        still need to be resolved.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="gmrc-ledger-panel">
            <header class="gmrc-ledger-panel__header">
                <p class="gmrc-eyebrow">
                    Current Afflictions
                </p>

                <h2>Conditions</h2>
            </header>

            <?php if ($conditions->isEmpty()) : ?>
                <p class="gmrc-status-note">
                    No active conditions are recorded.
                </p>
            <?php else : ?>
                <ul class="gmrc-tag-list gmrc-tag-list--conditions">
                    <?php foreach ($conditions->all() as $condition) : ?>
                        <li class="gmrc-tag-list__item">
                            <?php echo esc_html(
                                $condition->label()
                            ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
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
