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

$initiativeValue = $character
    ->initiative();

$initiative = $initiativeValue
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

$abilities = [
    'Strength' => $abilityScores->strength(),
    'Dexterity' => $abilityScores->dexterity(),
    'Constitution' => $abilityScores->constitution(),
    'Intelligence' => $abilityScores->intelligence(),
    'Wisdom' => $abilityScores->wisdom(),
    'Charisma' => $abilityScores->charisma(),
];

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

$deleteUrl = add_query_arg(
    'gmrc_route',
    'characters/'
        . rawurlencode($characterId)
        . '/delete',
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
?>

<section
    class="gmrc-open-ledger"
    data-living-ledger
    aria-labelledby="gmrc-open-ledger-title"
>
    <header class="gmrc-open-ledger__toolbar">
        <div class="gmrc-open-ledger__toolbar-copy">
            <p class="gmrc-eyebrow">
                Character Lifecycle Initiative · Phase II
            </p>

            <h1 id="gmrc-open-ledger-title">
                The Open Ledger
            </h1>

            <p>
                Adventurer’s Register · Entry
                <?php echo esc_html(
                    $entryReference
                ); ?>
            </p>
        </div>

        <div class="gmrc-open-ledger__actions">
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

            echo $this->component(
                'components.controls.paper-button',
                [
                    'label' => 'Delete Adventurer',
                    'href' => $deleteUrl,
                    'symbol' => '×',
                    'variant' => 'danger',
                    'size' => 'medium',
                    'ariaLabel' =>
                        'Delete ' . $name,
                ]
            );
            ?>
        </div>
    </header>

    <div
        id="gmrc-ledger-panel-overview"
        class="gmrc-ledger-tabpanel"
        role="tabpanel"
        aria-labelledby="gmrc-ledger-tab-overview"
        data-ledger-panel="overview"
    >
        <article class="gmrc-ledger-book">
        <span
            class="gmrc-ledger-book__ribbon"
            aria-hidden="true"
        ></span>

        <div
            class="gmrc-ledger-book__binding"
            aria-hidden="true"
        ></div>

        <section
            class="gmrc-ledger-page
                gmrc-ledger-page--identity"
            aria-labelledby="gmrc-ledger-identity-title"
        >
            <span
                class="gmrc-ledger-page__corner
                    gmrc-ledger-page__corner--top"
                aria-hidden="true"
            ></span>

            <p class="gmrc-ledger-page__folio">
                Guild Record · I
            </p>

            <div class="gmrc-ledger-page__portrait">
                <?php
                echo $this->component(
                    'components.media.illuminated-portrait',
                    [
                        'portrait' => $portrait,
                    ]
                );
                ?>
            </div>

            <div class="gmrc-ledger-page__seal">
                <?php
                echo $this->component(
                    'components.media.guild-seal',
                    $guildSeal
                );
                ?>
            </div>

            <header class="gmrc-ledger-identity">
                <p class="gmrc-ledger-identity__eyebrow">
                    Registered Adventurer
                </p>

                <h2 id="gmrc-ledger-identity-title">
                    <?php echo esc_html($name); ?>
                </h2>

                <p class="gmrc-ledger-identity__calling">
                    Level <?php echo esc_html(
                        (string) $level
                    ); ?>
                    <?php echo esc_html($race); ?>
                    <?php echo esc_html(
                        $characterClass
                    ); ?>
                </p>

                <p class="gmrc-ledger-identity__background">
                    <?php echo esc_html($background); ?>
                </p>
            </header>

            <dl class="gmrc-ledger-inscription">
                <div>
                    <dt>Experience</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $experience
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Register mark</dt>
                    <dd>
                        <?php echo esc_html(
                            $entryReference
                        ); ?>
                    </dd>
                </div>
            </dl>

            <blockquote class="gmrc-ledger-auby-note">
                <p>
                    “A proper Guild record deserves a proper page.
                    I straightened the corners myself.”
                </p>

                <footer>— Auby</footer>
            </blockquote>

            <p
                class="gmrc-ledger-page__number"
                aria-hidden="true"
            >
                1
            </p>
        </section>

        <section
            class="gmrc-ledger-page
                gmrc-ledger-page--measures"
            aria-labelledby="gmrc-ledger-measures-title"
        >
            <p class="gmrc-ledger-page__folio">
                Adventuring Measures · II
            </p>

            <header class="gmrc-ledger-page__heading">
                <p class="gmrc-eyebrow">
                    Field Record
                </p>

                <h2 id="gmrc-ledger-measures-title">
                    Adventuring Measures
                </h2>
            </header>

            <dl class="gmrc-ledger-vitals">
                <div>
                    <dt>Armour</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $armourClass
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Initiative</dt>
                    <dd>
                        <?php
                        echo $this->component(
                            'components.controls.guild-roll-trigger',
                            [
                                'label' => 'Initiative',
                                'modifier' => $initiativeValue->modifier(),
                                'primary' => $initiative,
                                'variant' => 'compact',
                            ]
                        );
                        ?>
                    </dd>
                </div>

                <div>
                    <dt>Speed</dt>
                    <dd>
                        <?php echo esc_html(
                            $speed
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Proficiency</dt>
                    <dd>
                        <?php echo esc_html(
                            $proficiencyBonus
                        ); ?>
                    </dd>
                </div>

                <div>
                    <dt>Perception</dt>
                    <dd>
                        <?php echo esc_html(
                            (string) $passivePerception
                        ); ?>
                    </dd>
                </div>
            </dl>

            <section class="gmrc-ledger-section">
                <header class="gmrc-ledger-section__heading">
                    <h3>Ability Scores</h3>
                </header>

                <dl class="gmrc-ledger-abilities">
                    <?php foreach (
                        $abilities
                        as $label => $score
                    ) : ?>
                        <div>
                            <dt>
                                <?php echo esc_html(
                                    $label
                                ); ?>
                            </dt>

                            <dd>
                                <?php
                                $modifier = $score->modifier();
                                $signedModifier = $modifier >= 0
                                    ? '+' . $modifier
                                    : (string) $modifier;

                                echo $this->component(
                                    'components.controls.guild-roll-trigger',
                                    [
                                        'label' => $label . ' Check',
                                        'modifier' => $modifier,
                                        'primary' => (string) $score->value(),
                                        'secondary' => $signedModifier,
                                        'variant' => 'ability',
                                    ]
                                );
                                ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>

            <section
                class="gmrc-ledger-section
                    gmrc-ledger-section--hit-points"
            >
                <header class="gmrc-ledger-section__heading">
                    <h3>Hit Points</h3>

                    <span>
                        <?php echo esc_html(
                            $character->isConscious()
                                ? 'Conscious'
                                : 'Unconscious'
                        ); ?>
                    </span>
                </header>

                <dl class="gmrc-ledger-hit-points">
                    <div>
                        <dt>Current</dt>
                        <dd>
                            <?php echo esc_html(
                                (string) $hitPoints->current()
                            ); ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Maximum</dt>
                        <dd>
                            <?php echo esc_html(
                                (string) $hitPoints->maximum()
                            ); ?>
                        </dd>
                    </div>

                    <div>
                        <dt>Temporary</dt>
                        <dd>
                            <?php echo esc_html(
                                (string) $hitPoints->temporary()
                            ); ?>
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="gmrc-ledger-section">
                <header class="gmrc-ledger-section__heading">
                    <h3>Saving Throws</h3>
                </header>

                <dl class="gmrc-ledger-saves">
                    <?php foreach (
                        $savingThrowLabels
                        as $ability => $label
                    ) : ?>
                        <?php
                        $savingThrow = $savingThrows->get(
                            $ability
                        );
                        ?>

                        <div
                            class="<?php echo esc_attr(
                                $savingThrow->isProficient()
                                    ? 'is-proficient'
                                    : ''
                            ); ?>"
                        >
                            <dt>
                                <?php if (
                                    $savingThrow->isProficient()
                                ) : ?>
                                    <span
                                        aria-label="Proficient"
                                        title="Proficient"
                                    >●</span>
                                <?php endif; ?>

                                <?php echo esc_html(
                                    $label
                                ); ?>
                            </dt>

                            <dd>
                                <?php
                                echo $this->component(
                                    'components.controls.guild-roll-trigger',
                                    [
                                        'label' => $label . ' Saving Throw',
                                        'modifier' => $savingThrow->modifier(),
                                        'primary' => $savingThrow->signed(),
                                        'variant' => 'inline',
                                    ]
                                );
                                ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </section>

            <p
                class="gmrc-ledger-page__number"
                aria-hidden="true"
            >
                2
            </p>
        </section>
    </article>

    </div>

    <div
        id="gmrc-ledger-panel-skills"
        class="gmrc-ledger-tabpanel"
        role="tabpanel"
        aria-labelledby="gmrc-ledger-tab-skills"
        data-ledger-panel="skills"
        hidden
    >
        <article
            class="gmrc-ledger-book gmrc-ledger-book--second-spread"
        >
            <div
                class="gmrc-ledger-book__binding"
                aria-hidden="true"
            ></div>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--skills"
                aria-labelledby="gmrc-ledger-skills-title"
            >
                <p class="gmrc-ledger-page__folio">
                    Training & Knowledge · III
                </p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Trained Talents</p>
                    <h2 id="gmrc-ledger-skills-title">Skills</h2>
                </header>

                <dl class="gmrc-ledger-skill-list">
                    <?php foreach ($skillLabels as $identifier => $label) : ?>
                        <?php
                        $skill = $skills->get($identifier);
                        $skillClass = '';

                        if ($skill->hasExpertise()) {
                            $skillClass = 'has-expertise';
                        } elseif ($skill->isProficient()) {
                            $skillClass = 'is-proficient';
                        }
                        ?>

                        <div class="<?php echo esc_attr($skillClass); ?>">
                            <dt>
                                <?php if ($skill->hasExpertise()) : ?>
                                    <span aria-label="Expertise" title="Expertise">◆</span>
                                <?php elseif ($skill->isProficient()) : ?>
                                    <span aria-label="Proficient" title="Proficient">●</span>
                                <?php endif; ?>

                                <?php echo esc_html($label); ?>
                            </dt>

                            <dd>
                                <?php
                                echo $this->component(
                                    'components.controls.guild-roll-trigger',
                                    [
                                        'label' => $label . ' Check',
                                        'modifier' => $skill->modifier(),
                                        'primary' => $skill->signed(),
                                        'variant' => 'inline',
                                    ]
                                );
                                ?>
                            </dd>
                        </div>
                    <?php endforeach; ?>
                </dl>

                <p class="gmrc-ledger-legend">
                    <span>● proficient</span>
                    <span>◆ expertise</span>
                </p>

                <p class="gmrc-ledger-page__number" aria-hidden="true">3</p>
            </section>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--training"
                aria-labelledby="gmrc-ledger-training-title"
            >
                <p class="gmrc-ledger-page__folio">Guild Training · IV</p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Recorded Training</p>
                    <h2 id="gmrc-ledger-training-title">Proficiencies</h2>
                </header>

                <section class="gmrc-ledger-section">
                    <header class="gmrc-ledger-section__heading">
                        <h3>Background</h3>
                    </header>

                    <dl class="gmrc-ledger-background">
                        <div>
                            <dt>History</dt>
                            <dd><?php echo esc_html($background); ?></dd>
                        </div>
                        <div>
                            <dt>Background talents</dt>
                            <dd>
                                <?php echo esc_html(
                                    $backgroundSkills !== []
                                        ? implode(', ', $backgroundSkills)
                                        : 'None'
                                ); ?>
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="gmrc-ledger-section">
                    <header class="gmrc-ledger-section__heading">
                        <h3>Languages</h3>
                    </header>
                    <?php if ($languages->isEmpty()) : ?>
                        <p class="gmrc-ledger-copy">No languages are currently recorded.</p>
                    <?php else : ?>
                        <ul class="gmrc-ledger-tags">
                            <?php foreach ($languages->all() as $language) : ?>
                                <li><?php echo esc_html($language->label()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section class="gmrc-ledger-section">
                    <header class="gmrc-ledger-section__heading">
                        <h3>Tool Proficiencies</h3>
                    </header>
                    <?php if ($toolProficiencies->isEmpty()) : ?>
                        <p class="gmrc-ledger-copy">No tool proficiencies are recorded.</p>
                    <?php else : ?>
                        <ul class="gmrc-ledger-tags">
                            <?php foreach ($toolProficiencies->all() as $tool) : ?>
                                <li><?php echo esc_html($tool->label()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <p class="gmrc-ledger-page__number" aria-hidden="true">4</p>
            </section>
        </article>
    </div>

    <div
        id="gmrc-ledger-panel-notes"
        class="gmrc-ledger-tabpanel"
        role="tabpanel"
        aria-labelledby="gmrc-ledger-tab-notes"
        data-ledger-panel="notes"
        hidden
    >
        <article class="gmrc-ledger-book gmrc-ledger-book--archive-spread">
            <div class="gmrc-ledger-book__binding" aria-hidden="true"></div>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--archive"
                aria-labelledby="gmrc-ledger-archive-title"
            >
                <p class="gmrc-ledger-page__folio">Guild Archive · V</p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Recorded Knowledge</p>
                    <h2 id="gmrc-ledger-archive-title">Archive Notes</h2>
                </header>

                <section class="gmrc-ledger-section">
                    <header class="gmrc-ledger-section__heading">
                        <h3>Conditions</h3>
                    </header>
                    <?php if ($conditions->isEmpty()) : ?>
                        <p class="gmrc-ledger-copy">No active conditions are recorded.</p>
                    <?php else : ?>
                        <ul class="gmrc-ledger-tags">
                            <?php foreach ($conditions->all() as $condition) : ?>
                                <li><?php echo esc_html($condition->label()); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>

                <section class="gmrc-ledger-section gmrc-ledger-personal-notes">
                    <header class="gmrc-ledger-section__heading">
                        <h3>Adventuring Notes</h3>
                    </header>
                    <p class="gmrc-ledger-copy">
                        No personal notes have been inscribed yet. This page is ready for the Guild Journal connection.
                    </p>
                    <div class="gmrc-ledger-note-lines" aria-hidden="true">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </section>

                <p class="gmrc-ledger-page__number" aria-hidden="true">5</p>
            </section>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--future"
                aria-labelledby="gmrc-ledger-future-title"
            >
                <p class="gmrc-ledger-page__folio">Adventuring Record · VI</p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Pages Yet to Be Written</p>
                    <h2 id="gmrc-ledger-future-title">The Road Ahead</h2>
                </header>

                <div class="gmrc-ledger-future__grid">
                    <article>
                        <span aria-hidden="true">🎒</span>
                        <h4>Leather Satchel</h4>
                        <p>Inventory and equipment will be recorded here.</p>
                    </article>
                    <article>
                        <span aria-hidden="true">✦</span>
                        <h4>Features</h4>
                        <p>Race and class features will receive illuminated entries.</p>
                    </article>
                    <article>
                        <span aria-hidden="true">🏆</span>
                        <h4>Honours</h4>
                        <p>Guild achievements will become stamps within the Ledger.</p>
                    </article>
                </div>

                <blockquote class="gmrc-ledger-auby-note gmrc-ledger-auby-note--archive">
                    <p>“Plenty of room left. That usually means adventure is about to happen.”</p>
                    <footer>— Auby</footer>
                </blockquote>

                <p class="gmrc-ledger-page__number" aria-hidden="true">6</p>
            </section>
        </article>
    </div>

    <div
        class="gmrc-ledger-tabs"
        role="tablist"
        aria-label="Open Ledger sections"
    >
        <button
            id="gmrc-ledger-tab-overview"
            class="gmrc-ledger-tab is-active"
            type="button"
            role="tab"
            aria-selected="true"
            aria-controls="gmrc-ledger-panel-overview"
            tabindex="0"
            data-ledger-tab="overview"
        >
            <span class="gmrc-ledger-tab__icon" aria-hidden="true">▣</span>
            <span class="gmrc-ledger-tab__label">Overview</span>
        </button>

        <button
            id="gmrc-ledger-tab-skills"
            class="gmrc-ledger-tab"
            type="button"
            role="tab"
            aria-selected="false"
            aria-controls="gmrc-ledger-panel-skills"
            tabindex="-1"
            data-ledger-tab="skills"
        >
            <span class="gmrc-ledger-tab__icon" aria-hidden="true">✦</span>
            <span class="gmrc-ledger-tab__label">Skills & Training</span>
        </button>

        <button
            id="gmrc-ledger-tab-notes"
            class="gmrc-ledger-tab"
            type="button"
            role="tab"
            aria-selected="false"
            aria-controls="gmrc-ledger-panel-notes"
            tabindex="-1"
            data-ledger-tab="notes"
        >
            <span class="gmrc-ledger-tab__icon" aria-hidden="true">✎</span>
            <span class="gmrc-ledger-tab__label">Archive Notes</span>
        </button>
    </div>

    <aside
        class="gmrc-guild-dice-tray"
        data-guild-dice-tray
        aria-labelledby="gmrc-guild-dice-title"
        hidden
    >
        <div class="gmrc-guild-dice-tray__pin" aria-hidden="true"></div>

        <header class="gmrc-guild-dice-tray__header">
            <div>
                <p class="gmrc-eyebrow">The Guild Dice</p>
                <h2 id="gmrc-guild-dice-title" data-guild-dice-label>D20 Roll</h2>
            </div>

            <button
                class="gmrc-guild-dice-tray__close"
                type="button"
                data-guild-dice-close
                aria-label="Close Guild Dice"
            >×</button>
        </header>

        <p class="gmrc-guild-dice-tray__modifier">
            Modifier
            <strong data-guild-dice-modifier>+0</strong>
        </p>

        <div
            class="gmrc-guild-dice-modes"
            aria-label="Choose how to roll"
        >
            <button type="button" data-guild-roll-mode="normal">Normal</button>
            <button type="button" data-guild-roll-mode="advantage">Advantage</button>
            <button type="button" data-guild-roll-mode="disadvantage">Disadvantage</button>
        </div>

        <div class="gmrc-guild-dice-result" data-guild-dice-result hidden>
            <div class="gmrc-guild-d20" data-guild-d20 aria-hidden="true">
                <span data-guild-d20-value>20</span>
            </div>

            <div class="gmrc-guild-dice-result__copy">
                <p class="gmrc-guild-dice-result__mode" data-guild-dice-mode></p>
                <p class="gmrc-guild-dice-result__math" data-guild-dice-math></p>
                <strong class="gmrc-guild-dice-result__total" data-guild-dice-total></strong>
                <p class="gmrc-guild-dice-result__auby" data-guild-dice-auby hidden></p>
            </div>
        </div>

        <div class="gmrc-guild-dice-history" data-guild-dice-history hidden>
            <h3>Recent Rolls</h3>
            <ol data-guild-dice-history-list></ol>
        </div>

        <p
            class="screen-reader-text"
            data-guild-dice-live
            aria-live="polite"
            aria-atomic="true"
        ></p>
    </aside>
</section>
