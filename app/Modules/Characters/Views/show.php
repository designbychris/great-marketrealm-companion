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

$armourClass = isset($inventoryArmourClass)
    ? (int) $inventoryArmourClass
    : $character
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

$inventory = isset($inventory)
    && is_array($inventory)
        ? $inventory
        : [
            'rows' => [],
            'total_weight' => 0,
            'capacity' => 0,
            'load_percent' => 0,
            'catalogue' => [],
            'equipped_count' => 0,
        ];

$attacks = isset($attacks) && is_array($attacks)
    ? $attacks
    : [];

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

$appRequestUrl = admin_url(
    'admin-post.php'
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

$advancementUrl = add_query_arg(
    'gmrc_route',
    'characters/'
        . rawurlencode($characterId)
        . '/progression/advance',
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

$arcana = isset($arcana) && is_array($arcana)
    ? $arcana
    : [
        'casting_ability' => null,
        'casting_modifier' => 0,
        'spell_attack' => null,
        'save_dc' => null,
        'slots' => [],
        'entries' => [],
        'has_spells' => false,
    ];

$progression = isset($progression) && is_array($progression)
    ? $progression
    : [
        'level' => $level,
        'experience' => $experience,
        'level_start_xp' => 0,
        'next_level_xp' => null,
        'xp_to_next' => 0,
        'progress_percent' => 0,
        'can_level_up' => false,
        'pending_levels' => 0,
        'highest_eligible_level' => $level,
        'is_maximum' => false,
        'next_level' => null,
        'current_proficiency' => $proficiencyBonus,
        'next_proficiency' => null,
        'hit_die' => '',
        'next_hit_point_gain' => 0,
        'current_max_hp' => $hitPoints->maximum(),
    ];

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

$advancementHistory = isset($advancementHistory)
    && is_array($advancementHistory)
        ? $advancementHistory
        : [];

$pathGifts = isset($pathGifts)
    && is_array($pathGifts)
        ? $pathGifts
        : [
            'path' => '',
            'path_label' => '',
            'gifts' => [],
            'count' => 0,
        ];

$callingPath = $character
    ->callingPath()
    ->value();

$callingPathLabel = $callingPath !== ''
    ? ucwords(
        str_replace(
            '-',
            ' ',
            $callingPath
        )
    )
    : '';

?>

<section
    class="gmrc-open-ledger"
    data-living-ledger
    aria-labelledby="gmrc-open-ledger-title"
>
    <header class="gmrc-open-ledger__toolbar">
        <div class="gmrc-open-ledger__toolbar-copy">
            <p class="gmrc-eyebrow">
                The Open Ledger
            </p>

            <h1 id="gmrc-open-ledger-title">
                <?php echo esc_html($name); ?>
            </h1>

            <p>
                Level <?php echo esc_html(
                        (string) $level
                    ); ?>
                    <?php echo esc_html($race); ?>
                    <?php echo esc_html(
                        $characterClass
                    ); ?>
                    <?php if (
                        $callingPathLabel !== ''
                    ) : ?>
                        · <?php echo esc_html(
                            $callingPathLabel
                        ); ?>
                    <?php endif; ?>
                    · <?php echo esc_html(
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

    <?php if (
        isset($completeAdventurer)
        && is_array($completeAdventurer)
    ) : ?>
        <section
            class="gmrc-complete-adventurer <?php echo esc_attr(
                ! empty($completeAdventurer['complete'])
                    ? 'is-complete'
                    : 'needs-review'
            ); ?>"
            aria-labelledby="gmrc-complete-adventurer-title"
            data-complete-adventurer
        >
            <header class="gmrc-complete-adventurer__header">
                <div>
                    <p class="gmrc-eyebrow">Registrar’s Final Audit</p>
                    <h2 id="gmrc-complete-adventurer-title">
                        <?php echo esc_html(
                            (string) ($completeAdventurer['label'] ?? '')
                        ); ?>
                    </h2>
                    <p>
                        <?php echo esc_html(
                            (string) ($completeAdventurer['summary'] ?? '')
                        ); ?>
                    </p>
                </div>

                <div class="gmrc-complete-adventurer__header-actions">
                    <strong class="gmrc-complete-adventurer__count">
                        <?php echo esc_html(
                            (string) ($completeAdventurer['ready_count'] ?? 0)
                        ); ?>
                        /
                        <?php echo esc_html(
                            (string) ($completeAdventurer['total'] ?? 0)
                        ); ?>
                        folios ready
                    </strong>

                    <button
                        type="button"
                        class="gmrc-complete-adventurer__toggle"
                        data-registrar-audit-toggle
                        data-audit-storage-key="<?php echo esc_attr(
                            'gmrc-audit-collapsed-' . $characterId
                        ); ?>"
                        aria-expanded="true"
                        aria-controls="gmrc-registrars-audit-content-<?php echo esc_attr(
                            $characterId
                        ); ?>"
                    >
                        <span data-registrar-audit-toggle-label>Hide Audit</span>
                        <span
                            class="gmrc-complete-adventurer__toggle-symbol"
                            data-registrar-audit-toggle-symbol
                            aria-hidden="true"
                        >−</span>
                    </button>
                </div>
            </header>

            <div
                id="gmrc-registrars-audit-content-<?php echo esc_attr(
                    $characterId
                ); ?>"
                class="gmrc-complete-adventurer__content"
                data-registrar-audit-content
            >
            <?php if (
                ! empty($completeAdventurer['certified'])
            ) : ?>
                <aside
                    class="gmrc-adventurers-seal"
                    data-adventurers-seal
                    data-auby-seal-surface
                    aria-labelledby="gmrc-adventurers-seal-title"
                >
                    <div class="gmrc-adventurers-seal__mark">
                        <?php echo $this->component(
                            'components.auby.seal-of-approval',
                            [
                                'variant' => 'gold',
                                'context' => 'adventurer',
                                'trigger' => 'visible',
                            ]
                        ); ?>
                    </div>

                    <div class="gmrc-adventurers-seal__copy">
                        <p class="gmrc-eyebrow">Guild Certified</p>

                        <h3 id="gmrc-adventurers-seal-title">
                            <?php echo esc_html(
                                (string) (
                                    $completeAdventurer['seal_title']
                                    ?? 'The Adventurer’s Seal'
                                )
                            ); ?>
                        </h3>

                        <strong>
                            <?php echo esc_html(
                                (string) (
                                    $completeAdventurer['seal_status']
                                    ?? ''
                                )
                            ); ?>
                        </strong>

                        <p>
                            <?php echo esc_html(
                                (string) (
                                    $completeAdventurer['seal_copy']
                                    ?? ''
                                )
                            ); ?>
                        </p>

                        <small>
                            Certified by the Guild Registrar
                        </small>
                    </div>
                </aside>
            <?php endif; ?>

            <div class="gmrc-complete-adventurer__folios">
                <?php foreach (
                    ($completeAdventurer['sections'] ?? [])
                    as $folio
                ) : ?>
                    <button
                        type="button"
                        class="gmrc-complete-adventurer__folio <?php echo esc_attr(
                            ! empty($folio['ready'])
                                ? 'is-ready'
                                : 'needs-review'
                        ); ?>"
                        data-ledger-jump="<?php echo esc_attr(
                            (string) ($folio['panel'] ?? 'overview')
                        ); ?>"
                        aria-label="Open <?php echo esc_attr(
                            (string) ($folio['label'] ?? 'Guild folio')
                        ); ?> folio"
                    >
                        <span class="gmrc-complete-adventurer__symbol" aria-hidden="true"><?php echo esc_html(
                            (string) ($folio['symbol'] ?? '•')
                        ); ?></span>

                        <span class="gmrc-complete-adventurer__folio-copy">
                            <strong><?php echo esc_html(
                                (string) ($folio['label'] ?? '')
                            ); ?></strong>
                            <span><?php echo esc_html(
                                (string) ($folio['status'] ?? '')
                            ); ?></span>
                            <small><?php echo esc_html(
                                (string) ($folio['detail'] ?? '')
                            ); ?></small>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
            </div>
        </section>
    <?php endif; ?>

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
                        'portraitPersisted' => true,
                        'controlsEnabled' => false,
                    ]
                );

                echo $this->component(
                    'components.media.illuminator-workbench',
                    [
                        'characterId' => $character->id()->value(),
                        'isCustom' => $portrait->mode() === 'custom',
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
        id="gmrc-ledger-panel-equipment"
        class="gmrc-ledger-tabpanel"
        role="tabpanel"
        aria-labelledby="gmrc-ledger-tab-equipment"
        data-ledger-panel="equipment"
        hidden
    >
        <article class="gmrc-ledger-book gmrc-ledger-book--equipment">
            <span
                class="gmrc-ledger-book__ribbon"
                aria-hidden="true"
            ></span>

            <div
                class="gmrc-ledger-book__binding"
                aria-hidden="true"
            ></div>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--pack"
                aria-labelledby="gmrc-ledger-pack-title"
            >
                <p class="gmrc-ledger-page__folio">
                    Adventurer’s Pack · V
                </p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Auby’s Packing Register</p>
                    <h2 id="gmrc-ledger-pack-title">Equipment & Inventory</h2>
                    <p>
                        Everything currently entrusted to this adventurer,
                        from trusty steel to emergency biscuits.
                    </p>
                </header>

                <section class="gmrc-pack-summary" aria-label="Pack load summary">
                    <div>
                        <span class="gmrc-pack-summary__icon" aria-hidden="true">🎒</span>
                        <p><strong><?php echo esc_html((string) count($inventory['rows'])); ?></strong> kinds of item</p>
                    </div>
                    <div>
                        <span class="gmrc-pack-summary__icon" aria-hidden="true">⚖</span>
                        <p>
                            <strong><?php echo esc_html((string) $inventory['total_weight']); ?> lb</strong>
                            of <?php echo esc_html((string) $inventory['capacity']); ?> lb
                        </p>
                    </div>
                    <div>
                        <span class="gmrc-pack-summary__icon" aria-hidden="true">✦</span>
                        <p><strong><?php echo esc_html((string) $inventory['equipped_count']); ?></strong> equipped</p>
                    </div>
                </section>

                <div class="gmrc-pack-load" aria-label="Pack load <?php echo esc_attr((string) $inventory['load_percent']); ?> percent">
                    <span style="width:<?php echo esc_attr((string) $inventory['load_percent']); ?>%"></span>
                </div>

                <?php if ($inventory['rows'] === []) : ?>
                    <div class="gmrc-pack-empty">
                        <span aria-hidden="true">🧺</span>
                        <h3>The satchel is suspiciously light.</h3>
                        <p>
                            Nothing has been entered into the Packing Register yet.
                            The Guild stores are open on the opposite page.
                        </p>
                    </div>
                <?php else : ?>
                    <div class="gmrc-pack-list">
                        <?php foreach ($inventory['rows'] as $item) : ?>
                            <article class="gmrc-pack-item <?php echo $item['equipped'] ? 'is-equipped' : ''; ?>">
                                <div class="gmrc-pack-item__main">
                                    <span class="gmrc-pack-item__category">
                                        <?php echo esc_html(ucfirst($item['category'])); ?>
                                    </span>
                                    <h3><?php echo esc_html($item['label']); ?></h3>
                                    <p><?php echo esc_html($item['description']); ?></p>
                                    <?php if ($item['damage_die'] !== null) : ?>
                                        <p class="gmrc-pack-item__mechanics">
                                            <?php echo esc_html($item['damage_die'] . ' ' . $item['damage_type']); ?>
                                            <?php if ($item['properties'] !== []) : ?>
                                                · <?php echo esc_html(implode(', ', $item['properties'])); ?>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="gmrc-pack-item__meta">
                                    <?php if ($item['equipped']) : ?>
                                        <span class="gmrc-equipped-seal">Equipped</span>
                                    <?php endif; ?>
                                    <span>Qty <?php echo esc_html((string) $item['quantity']); ?></span>
                                    <span><?php echo esc_html((string) $item['total_weight']); ?> lb</span>
                                </div>

                                <div class="gmrc-pack-item__actions">
                                    <?php
                                    $itemRoute = 'characters/'
                                        . rawurlencode($characterId)
                                        . '/inventory/'
                                        . rawurlencode($item['id']);

                                    $equipRoute = $itemRoute
                                        . '/equip';
                                    ?>

                                    <form method="post" action="<?php echo esc_url($appRequestUrl); ?>" class="gmrc-pack-quantity-form">
                                        <input type="hidden" name="action" value="gmrc_app_request">
                                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($itemRoute); ?>">
                                        <input type="hidden" name="_method" value="PUT">
                                        <?php wp_nonce_field('gmrc_character_inventory_' . $characterId, 'gmrc_nonce'); ?>
                                        <label>
                                            <span class="screen-reader-text">Quantity for <?php echo esc_html($item['label']); ?></span>
                                            <input type="number" name="quantity" min="0" max="99" value="<?php echo esc_attr((string) $item['quantity']); ?>">
                                        </label>
                                        <button type="submit">Update</button>
                                    </form>

                                    <?php if ($item['equippable']) : ?>
                                        <form method="post" action="<?php echo esc_url($appRequestUrl); ?>">
                                            <input type="hidden" name="action" value="gmrc_app_request">
                                            <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($equipRoute); ?>">
                                            <?php wp_nonce_field('gmrc_character_inventory_' . $characterId, 'gmrc_nonce'); ?>
                                            <button type="submit">
                                                <?php echo esc_html($item['equipped'] ? 'Unequip' : 'Equip'); ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="post" action="<?php echo esc_url($appRequestUrl); ?>">
                                        <input type="hidden" name="action" value="gmrc_app_request">
                                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($itemRoute); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <?php wp_nonce_field('gmrc_character_inventory_' . $characterId, 'gmrc_nonce'); ?>
                                        <button type="submit" class="gmrc-pack-remove">Remove</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <p class="gmrc-ledger-page__number" aria-hidden="true">5</p>
            </section>

            <section
                class="gmrc-ledger-page gmrc-ledger-page--stores"
                aria-labelledby="gmrc-guild-stores-title"
            >
                <p class="gmrc-ledger-page__folio">
                    Guild Stores · VI
                </p>

                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Quartermaster’s Counter</p>
                    <h2 id="gmrc-guild-stores-title">Pack Another Item</h2>
                    <p>
                        Add a catalogue item to the adventurer’s record.
                        More exotic Marketrealm gear will join these shelves later.
                    </p>
                </header>

                <?php
                $inventoryRoute = 'characters/'
                    . rawurlencode($characterId)
                    . '/inventory';
                ?>

                <form class="gmrc-guild-stores-form" method="post" action="<?php echo esc_url($appRequestUrl); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($inventoryRoute); ?>">
                    <?php wp_nonce_field('gmrc_character_inventory_' . $characterId, 'gmrc_nonce'); ?>

                    <label>
                        <span>Guild stores catalogue</span>
                        <select name="item_id" required>
                            <option value="">Choose an item…</option>
                            <?php foreach ($inventory['catalogue'] as $catalogueItem) : ?>
                                <option value="<?php echo esc_attr($catalogueItem->id()); ?>">
                                    <?php echo esc_html($catalogueItem->label()); ?>
                                    — <?php echo esc_html(ucfirst($catalogueItem->category())); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="gmrc-guild-stores-form__quantity">
                        <span>Quantity</span>
                        <input type="number" name="quantity" value="1" min="1" max="99">
                    </label>

                    <button class="gmrc-button gmrc-button--secondary" type="submit">
                        Add to Adventurer’s Pack
                    </button>
                </form>

                <div class="gmrc-guild-stores-shelves">
                    <?php foreach (array_slice($inventory['catalogue'], 0, 6) as $catalogueItem) : ?>
                        <article>
                            <span class="gmrc-guild-stores-shelves__category">
                                <?php echo esc_html(ucfirst($catalogueItem->category())); ?>
                            </span>
                            <h3><?php echo esc_html($catalogueItem->label()); ?></h3>
                            <p><?php echo esc_html($catalogueItem->description()); ?></p>
                            <small><?php echo esc_html((string) $catalogueItem->weight()); ?> lb</small>
                        </article>
                    <?php endforeach; ?>
                </div>

                <blockquote class="gmrc-ledger-auby-note gmrc-ledger-auby-note--archive">
                    <p>“I packed the emergency biscuit. Then I packed another emergency biscuit for the emergency involving the first biscuit.”</p>
                    <footer>— Auby</footer>
                </blockquote>

                <p class="gmrc-ledger-page__number" aria-hidden="true">6</p>
            </section>
        </article>
    </div>

    <div
        id="gmrc-ledger-panel-attacks"
        class="gmrc-ledger-tabpanel"
        role="tabpanel"
        aria-labelledby="gmrc-ledger-tab-attacks"
        data-ledger-panel="attacks"
        hidden
    >
        <article class="gmrc-ledger-book gmrc-ledger-book--attacks">
            <span class="gmrc-ledger-book__ribbon" aria-hidden="true"></span>
            <div class="gmrc-ledger-book__binding" aria-hidden="true"></div>

            <section class="gmrc-ledger-page gmrc-ledger-page--attacks" aria-labelledby="gmrc-ledger-attacks-title">
                <p class="gmrc-ledger-page__folio">Clash Register · VII</p>
                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">The Clash of the Ledger</p>
                    <h2 id="gmrc-ledger-attacks-title">Attacks & Weapons</h2>
                    <p>Equipped weapons are copied here automatically by the Guild Quartermaster.</p>
                </header>

                <?php if ($attacks === []) : ?>
                    <div class="gmrc-combat-empty">
                        <span aria-hidden="true">⚔</span>
                        <h3>No weapon is readied.</h3>
                        <p>Equip a weapon in the Adventurer’s Pack and its attack will appear here.</p>
                    </div>
                <?php else : ?>
                    <div class="gmrc-attack-list">
                        <?php foreach ($attacks as $attack) : ?>
                            <article class="gmrc-attack-card">
                                <header class="gmrc-attack-card__header">
                                    <div>
                                        <p class="gmrc-attack-card__eyebrow"><?php echo esc_html($attack['range']); ?></p>
                                        <h3><?php echo esc_html($attack['label']); ?></h3>
                                    </div>
                                    <span class="gmrc-attack-card__bonus"><?php echo esc_html(($attack['attack_bonus'] >= 0 ? '+' : '') . (string) $attack['attack_bonus']); ?></span>
                                </header>
                                <p><?php echo esc_html($attack['description']); ?></p>
                                <dl class="gmrc-attack-card__facts">
                                    <div><dt>Attack ability</dt><dd><?php echo esc_html($attack['ability']); ?></dd></div>
                                    <div><dt>Damage</dt><dd><?php echo esc_html($attack['damage_die'] . ($attack['damage_modifier'] >= 0 ? ' +' : ' ') . (string) $attack['damage_modifier'] . ' ' . $attack['damage_type']); ?></dd></div>
                                </dl>
                                <?php if ($attack['properties'] !== []) : ?>
                                    <ul class="gmrc-attack-properties" aria-label="Weapon properties">
                                        <?php foreach ($attack['properties'] as $property) : ?><li><?php echo esc_html(ucfirst($property)); ?></li><?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="gmrc-attack-card__rolls">
                                    <button type="button" class="gmrc-guild-roll-trigger" data-guild-roll="d20" data-roll-kind="attack" data-roll-label="<?php echo esc_attr($attack['label'] . ' — Attack'); ?>" data-roll-modifier="<?php echo esc_attr((string) $attack['attack_bonus']); ?>" data-roll-result-suffix="to hit">
                                        <span aria-hidden="true">⚔</span> Roll Attack
                                    </button>
                                    <button type="button" class="gmrc-guild-roll-trigger gmrc-guild-roll-trigger--damage" data-guild-roll="damage" data-roll-kind="damage" data-roll-label="<?php echo esc_attr($attack['label'] . ' — Damage'); ?>" data-roll-formula="<?php echo esc_attr($attack['damage_die']); ?>" data-roll-modifier="<?php echo esc_attr((string) $attack['damage_modifier']); ?>" data-roll-damage-type="<?php echo esc_attr($attack['damage_type']); ?>">
                                        <span aria-hidden="true">✹</span> Roll Damage
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <p class="gmrc-ledger-page__number" aria-hidden="true">7</p>
            </section>

            <section class="gmrc-ledger-page gmrc-ledger-page--combat-notes" aria-labelledby="gmrc-combat-notes-title">
                <p class="gmrc-ledger-page__folio">Combat Notes · VIII</p>
                <header class="gmrc-ledger-page__heading">
                    <p class="gmrc-eyebrow">Registrar’s Combat Notes</p>
                    <h2 id="gmrc-combat-notes-title">How the Guild Counts</h2>
                </header>
                <div class="gmrc-combat-rules">
                    <article><strong>d20 + modifier</strong><span>Attack roll</span></article>
                    <article><strong>Natural 20</strong><span>Critical hit — double the weapon dice</span></article>
                    <article><strong>Natural 1</strong><span>The Guild records an especially unfortunate attempt</span></article>
                </div>
                <blockquote class="gmrc-ledger-auby-note gmrc-ledger-auby-note--archive"><p>“Point the sharp end away from the paperwork.”</p><footer>— Auby</footer></blockquote>
                <p class="gmrc-ledger-page__number" aria-hidden="true">8</p>
            </section>
        </article>
    </div>


<div
    id="gmrc-ledger-panel-arcana"
    class="gmrc-ledger-tabpanel"
    role="tabpanel"
    aria-labelledby="gmrc-ledger-tab-arcana"
    data-ledger-panel="arcana"
    hidden
>
    <article class="gmrc-ledger-book gmrc-ledger-book--arcane-spread">
        <div class="gmrc-ledger-book__binding" aria-hidden="true"></div>

        <section
            class="gmrc-ledger-page gmrc-ledger-page--arcane-pantry"
            aria-labelledby="gmrc-arcane-pantry-title"
        >
            <p class="gmrc-ledger-page__folio">Arcane Pantry · IX</p>

            <header class="gmrc-ledger-page__heading">
                <p class="gmrc-eyebrow">Dangerously Magical</p>
                <h2 id="gmrc-arcane-pantry-title">The Arcane Pantry</h2>
            </header>

            <?php if ($arcana['casting_ability'] !== null) : ?>
                <dl class="gmrc-arcane-summary">
                    <div>
                        <dt>Casting ability</dt>
                        <dd><?php echo esc_html($arcana['casting_ability']); ?></dd>
                    </div>
                    <div>
                        <dt>Spell attack</dt>
                        <dd><?php echo esc_html(
                            ($arcana['spell_attack'] ?? 0) >= 0
                                ? '+' . (string) $arcana['spell_attack']
                                : (string) $arcana['spell_attack']
                        ); ?></dd>
                    </div>
                    <div>
                        <dt>Save DC</dt>
                        <dd><?php echo esc_html((string) $arcana['save_dc']); ?></dd>
                    </div>
                </dl>

                <?php if ($arcana['slots'] !== []) : ?>
                    <div class="gmrc-spell-slots" aria-label="Spell slots">
                        <?php foreach ($arcana['slots'] as $slot) : ?>
                            <span>
                                <strong><?php echo esc_html(
                                    (string) $slot['total']
                                ); ?></strong>
                                Level <?php echo esc_html(
                                    (string) $slot['level']
                                ); ?> slots
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <div class="gmrc-arcane-summary gmrc-arcane-summary--features">
                    <span aria-hidden="true">✦</span>
                    <p>
                        This adventurer’s magic is written in deeds rather than spell slots.
                        Class features and special abilities are recorded below.
                    </p>
                </div>
            <?php endif; ?>

            <div class="gmrc-arcane-card-list">
                <?php if ($arcana['entries'] === []) : ?>
                    <article class="gmrc-arcane-empty">
                        <span aria-hidden="true">📜</span>
                        <h3>Pages Awaiting Discovery</h3>
                        <p>
                            No spells or class abilities have been entered for this calling yet.
                            The Guild Archivists have left plenty of room.
                        </p>
                    </article>
                <?php else : ?>
                    <?php foreach ($arcana['entries'] as $ability) : ?>
                        <article class="gmrc-arcane-card">
                            <header>
                                <span class="gmrc-arcane-card__kind">
                                    <?php echo esc_html(
                                        ucfirst((string) $ability['kind'])
                                    ); ?>
                                </span>
                                <?php if (
                                    ! empty($ability['learned'])
                                    && in_array(
                                        $ability['kind'],
                                        ['spell', 'cantrip'],
                                        true
                                    )
                                ) : ?>
                                    <span class="gmrc-arcane-card__learned">
                                        In Spellbook ✓
                                    </span>
                                <?php endif; ?>
                                <h3><?php echo esc_html($ability['label']); ?></h3>
                                <p><?php echo esc_html($ability['description']); ?></p>
                            </header>

                            <dl class="gmrc-arcane-card__facts">
                                <div><dt>Use</dt><dd><?php echo esc_html($ability['activation']); ?></dd></div>
                                <div><dt>Range</dt><dd><?php echo esc_html($ability['range']); ?></dd></div>
                                <div><dt>Duration</dt><dd><?php echo esc_html($ability['duration']); ?></dd></div>
                                <div><dt>Stock</dt><dd><?php echo esc_html($ability['uses']); ?></dd></div>
                            </dl>

                            <?php if ($ability['save_dc'] !== null) : ?>
                                <p class="gmrc-arcane-save">
                                    <strong>
                                        DC <?php echo esc_html((string) $ability['save_dc']); ?>
                                        <?php echo esc_html(ucfirst((string) $ability['save_ability'])); ?>
                                    </strong>
                                    saving throw
                                </p>
                            <?php endif; ?>

                            <div class="gmrc-arcane-card__rolls">
                                <?php if ($ability['spell_attack'] !== null) : ?>
                                    <button
                                        type="button"
                                        class="gmrc-guild-roll-trigger"
                                        data-guild-roll="d20"
                                        data-roll-kind="spell-attack"
                                        data-roll-label="<?php echo esc_attr($ability['label'] . ' — Spell Attack'); ?>"
                                        data-roll-modifier="<?php echo esc_attr((string) $ability['spell_attack']); ?>"
                                        data-roll-result-suffix="to hit"
                                    >
                                        <span aria-hidden="true">20</span>
                                        Roll Spell Attack
                                    </button>
                                <?php endif; ?>

                                <?php if (
                                    $ability['formula'] !== null
                                    && $ability['roll_kind'] !== null
                                ) : ?>
                                    <button
                                        type="button"
                                        class="gmrc-guild-roll-trigger gmrc-guild-roll-trigger--formula"
                                        data-guild-roll="<?php echo esc_attr($ability['roll_kind']); ?>"
                                        data-roll-kind="<?php echo esc_attr($ability['roll_kind']); ?>"
                                        data-roll-label="<?php echo esc_attr(
                                            $ability['label']
                                            . ' — '
                                            . ucfirst((string) $ability['roll_kind'])
                                        ); ?>"
                                        data-roll-formula="<?php echo esc_attr($ability['formula']); ?>"
                                        data-roll-modifier="<?php echo esc_attr((string) $ability['roll_modifier']); ?>"
                                        data-roll-damage-type="<?php echo esc_attr((string) ($ability['damage_type'] ?? '')); ?>"
                                    >
                                        <span aria-hidden="true">✦</span>
                                        Roll <?php echo esc_html(ucfirst((string) $ability['roll_kind'])); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p class="gmrc-ledger-page__number" aria-hidden="true">9</p>
        </section>

        <section
            class="gmrc-ledger-page gmrc-ledger-page--arcane-notes"
            aria-labelledby="gmrc-arcane-notes-title"
        >
            <p class="gmrc-ledger-page__folio">Mystic Measures · X</p>

            <header class="gmrc-ledger-page__heading">
                <p class="gmrc-eyebrow">Auby’s Shelf of Questionable Power</p>
                <h2 id="gmrc-arcane-notes-title">Spell & Ability Notes</h2>
            </header>

            <div class="gmrc-arcane-rules">
                <article>
                    <strong>d20 + ability + proficiency</strong>
                    <span>Spell attack</span>
                </article>
                <article>
                    <strong>8 + ability + proficiency</strong>
                    <span>Spell save DC</span>
                </article>
                <article>
                    <strong>Animated formula dice</strong>
                    <span>Damage and healing</span>
                </article>
            </div>

            <section class="gmrc-ledger-section">
                <header class="gmrc-ledger-section__heading">
                    <h3>Prepared for Progression</h3>
                </header>
                <p class="gmrc-ledger-copy">
                    The Pantry catalogue is level-aware and ready for future spell preparation,
                    limited-use tracking, subclass features and higher-level unlocks.
                </p>
            </section>

            <blockquote class="gmrc-ledger-auby-note gmrc-ledger-auby-note--archive">
                <p>“If the label says ‘do not shake’, I cannot stress enough that it means you.”</p>
                <footer>— Auby</footer>
            </blockquote>

            <p class="gmrc-ledger-page__number" aria-hidden="true">10</p>
        </section>
    </article>
</div>


<div
    id="gmrc-ledger-panel-progression"
    class="gmrc-ledger-tabpanel"
    role="tabpanel"
    aria-labelledby="gmrc-ledger-tab-progression"
    data-ledger-panel="progression"
    hidden
>
    <article class="gmrc-ledger-book gmrc-ledger-book--rising-register">
        <div class="gmrc-ledger-book__binding" aria-hidden="true"></div>

        <section class="gmrc-ledger-page gmrc-ledger-page--rising-register" aria-labelledby="gmrc-rising-register-title">
            <p class="gmrc-ledger-page__folio">Rising Register · XI</p>
            <header class="gmrc-ledger-page__heading">
                <p class="gmrc-eyebrow">Adventurer Progression</p>
                <h2 id="gmrc-rising-register-title">The Rising Register</h2>
            </header>

            <div class="gmrc-rise-level-seal" aria-label="Current level">
                <span>Level</span>
                <strong><?php echo esc_html((string) $progression['level']); ?></strong>
            </div>

            <section class="gmrc-rise-progress" aria-labelledby="gmrc-rise-xp-title">
                <header>
                    <h3 id="gmrc-rise-xp-title">Experience Ledger</h3>
                    <strong><?php echo esc_html(number_format_i18n((int) $progression['experience'])); ?> XP</strong>
                </header>
                <div class="gmrc-rise-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr((string) $progression['progress_percent']); ?>" aria-label="Progress toward next level">
                    <span style="width:<?php echo esc_attr((string) $progression['progress_percent']); ?>%"></span>
                </div>
                <?php if ($progression['is_maximum']) : ?>
                    <p>Maximum level certified. The Guild has run out of larger numbers.</p>
                <?php elseif ($progression['can_level_up']) : ?>
                    <p>
                        Level <?php echo esc_html((string) $progression['next_level']); ?>
                        is ready for Guild advancement.
                        <?php if ((int) $progression['pending_levels'] > 1) : ?>
                            <?php echo esc_html(
                                (string) $progression['pending_levels']
                            ); ?> certifications are currently waiting.
                        <?php endif; ?>
                    </p>
                <?php else : ?>
                    <p><?php echo esc_html(number_format_i18n((int) $progression['xp_to_next'])); ?> XP until Level <?php echo esc_html((string) $progression['next_level']); ?>.</p>
                <?php endif; ?>
            </section>

            <form class="gmrc-rise-xp-form" method="post" action="<?php echo esc_url($appRequestUrl); ?>">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('characters/' . rawurlencode($characterId) . '/progression/experience'); ?>">
                <?php wp_nonce_field('gmrc_character_progression_' . $characterId, 'gmrc_nonce'); ?>
                <label for="gmrc-rise-xp-<?php echo esc_attr($characterId); ?>">
                    <span>Record earned XP</span>
                    <input id="gmrc-rise-xp-<?php echo esc_attr($characterId); ?>" type="number" name="experience" min="1" max="1000000" step="1" value="100" required>
                </label>
                <button class="gmrc-button gmrc-button--secondary" type="submit">Enter into Register</button>
            </form>

            <p class="gmrc-ledger-page__number" aria-hidden="true">11</p>
        </section>

        <section class="gmrc-ledger-page gmrc-ledger-page--level-certification gmrc-ledger-page--living-register" aria-labelledby="gmrc-living-register-title" aria-describedby="gmrc-living-register-intro" data-living-register>
            <p class="gmrc-ledger-page__folio">Living Register · XII</p>
            <header class="gmrc-ledger-page__heading">
                <p class="gmrc-eyebrow">Current Guild Record</p>
                <h2 id="gmrc-living-register-title">The Living Register</h2>
                <p id="gmrc-living-register-intro">Only completed Guild certifications are entered here. Pending advancement paperwork remains in the Rising Register until it is sealed.</p>
            </header>

            <dl class="gmrc-living-register__summary" aria-label="Current certified progression record">
                <div><dt>Certified Level</dt><dd><?php echo esc_html((string) $livingRegister['level']); ?></dd></div>
                <div><dt>Calling</dt><dd><?php echo esc_html((string) $livingRegister['calling']); ?></dd></div>
                <div><dt>Path</dt><dd><?php echo esc_html((string) ($livingRegister['path_label'] !== '' ? $livingRegister['path_label'] : 'Not yet certified')); ?></dd></div>
                <div><dt>Proficiency</dt><dd><?php echo esc_html((string) $livingRegister['proficiency']); ?></dd></div>
                <div><dt>Vitality</dt><dd><?php echo esc_html((string) $livingRegister['current_hp']); ?> / <?php echo esc_html((string) $livingRegister['maximum_hp']); ?> HP</dd></div>
                <div><dt>Path Gifts</dt><dd><?php echo esc_html((string) $livingRegister['path_gift_count']); ?></dd></div>
                <div><dt>Learned Arcana</dt><dd><?php echo esc_html((string) $livingRegister['arcana_known']); ?></dd></div>
                <div><dt>Certifications</dt><dd><?php echo esc_html((string) $livingRegister['certification_count']); ?></dd></div>
                <div><dt>Register Standing</dt><dd><?php echo esc_html((string) $livingRegister['register_status']); ?></dd></div>
            </dl>

            <?php if (! empty($livingRegister['is_unsealed_journey'])) : ?>
                <aside class="gmrc-living-register__empty-state" data-living-register-empty>
                    <span aria-hidden="true">◇</span>
                    <div>
                        <strong>The Chronicle awaits its first advancement seal.</strong>
                        <p>This adventurer’s current certified state is already recorded above. Fresh Ink, milestones and journey history will appear after the first completed Guild advancement.</p>
                    </div>
                </aside>
            <?php elseif (! empty($livingRegister['is_maximum_level'])) : ?>
                <aside class="gmrc-living-register__final-seal" data-living-register-final-seal>
                    <span aria-hidden="true">★</span>
                    <div>
                        <strong>The Final Level is sealed.</strong>
                        <p>Level 20 stands fully certified in the Living Register. The Chronicle remains open for remembrance, but no higher Guild level awaits.</p>
                    </div>
                </aside>
            <?php endif; ?>

            <?php if (! empty($livingRegister['has_chronicle'])) : ?>
                <nav class="gmrc-living-register__index" aria-label="Living Register sections" data-living-register-index>
                    <span>Register index</span>
                    <?php if (! empty($livingRegister['has_fresh_ink'])) : ?><a href="#gmrc-fresh-ink-title">Fresh Ink</a><?php endif; ?>
                    <?php if (! empty($livingRegister['has_journey_measure'])) : ?><a href="#gmrc-journey-measure-title">Journey Measure</a><?php endif; ?>
                    <?php if (! empty($livingRegister['has_change_record'])) : ?><a href="#gmrc-change-record-title">Record of Change</a><?php endif; ?>
                    <a href="#gmrc-sealed-chronicle-title">Sealed Chronicle</a>
                </nav>
            <?php endif; ?>

            <?php if (! empty($livingRegister['has_fresh_ink']) && is_array($livingRegister['fresh_ink'])) : ?>
                <?php $freshInk = $livingRegister['fresh_ink']; ?>
                <section class="gmrc-living-register__fresh-ink" aria-labelledby="gmrc-fresh-ink-title" tabindex="-1">
                    <header>
                        <p class="gmrc-eyebrow">Most recent certification</p>
                        <h3 id="gmrc-fresh-ink-title">Fresh Ink in the Register</h3>
                        <p>The Guild’s latest sealed changes to this adventurer.</p>
                    </header>
                    <ul class="gmrc-living-register__changes">
                        <li><strong>Level <?php echo esc_html((string) $freshInk['target_level']); ?></strong><span>Certified from Level <?php echo esc_html((string) $freshInk['from_level']); ?>.</span></li>
                        <?php if ((int) $freshInk['hit_point_gain'] > 0) : ?>
                            <li><strong>+<?php echo esc_html((string) $freshInk['hit_point_gain']); ?> maximum HP</strong><span><?php echo esc_html((string) $freshInk['old_maximum_hp']); ?> → <?php echo esc_html((string) $freshInk['new_maximum_hp']); ?> HP.</span></li>
                        <?php endif; ?>
                        <?php if ($freshInk['spells_learned'] !== []) : ?>
                            <li><strong><?php echo esc_html((string) count($freshInk['spells_learned'])); ?> spell<?php echo count($freshInk['spells_learned']) === 1 ? '' : 's'; ?> entered</strong><span><?php echo esc_html(implode(', ', array_map(static fn (string $key): string => ucwords(str_replace('-', ' ', $key)), $freshInk['spells_learned']))); ?></span></li>
                        <?php endif; ?>
                        <?php if ($freshInk['cantrips_learned'] !== []) : ?>
                            <li><strong><?php echo esc_html((string) count($freshInk['cantrips_learned'])); ?> cantrip<?php echo count($freshInk['cantrips_learned']) === 1 ? '' : 's'; ?> entered</strong><span><?php echo esc_html(implode(', ', array_map(static fn (string $key): string => ucwords(str_replace('-', ' ', $key)), $freshInk['cantrips_learned']))); ?></span></li>
                        <?php endif; ?>
                        <?php if ($freshInk['path_gifts_granted'] !== []) : ?>
                            <li><strong>Path Gifts granted</strong><span><?php echo esc_html(implode(', ', $freshInk['path_gifts_granted'])); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (! $progression['is_maximum']) : ?>
            <section class="gmrc-living-register__next" aria-labelledby="gmrc-level-certification-title">
                <header>
                    <p class="gmrc-eyebrow">What the next stamp changes</p>
                    <h3 id="gmrc-level-certification-title">Next Guild Certification</h3>
                </header>

                <dl class="gmrc-rise-measures">
                    <div><dt>Current proficiency</dt><dd><?php echo esc_html((string) $progression['current_proficiency']); ?></dd></div>
                    <div><dt>Next proficiency</dt><dd><?php echo esc_html((string) ($progression['next_proficiency'] ?? '—')); ?></dd></div>
                    <div><dt>Class hit die</dt><dd><?php echo esc_html((string) $progression['hit_die']); ?></dd></div>
                    <div><dt>HP on next level</dt><dd>+<?php echo esc_html((string) $progression['next_hit_point_gain']); ?></dd></div>
                </dl>
            </section>
            <?php endif; ?>

            <?php if (
                ! $progression['is_maximum']
                && $progression['can_level_up']
            ) : ?>
                <div class="gmrc-rise-awaiting gmrc-rise-awaiting--ready">
                    <span aria-hidden="true">✦</span>
                    <div>
                        <strong>Advancement ready</strong>
                        <p>
                            Enough experience has been recorded for Level
                            <?php echo esc_html(
                                (string) $progression['next_level']
                            ); ?>.
                            The current certified level remains unchanged
                            until the Advancement Ledger is completed.
                        </p>
                        <a
                            class="gmrc-button gmrc-button--primary"
                            href="<?php echo esc_url($advancementUrl); ?>"
                        >
                            Begin Advancement
                        </a>
                    </div>
                </div>
            <?php elseif (! $progression['is_maximum']) : ?>
                <div class="gmrc-rise-awaiting">
                    <span aria-hidden="true">✦</span>
                    <p>
                        The next advancement becomes available when the
                        Experience Ledger reaches
                        <?php echo esc_html(
                            number_format_i18n(
                                (int) $progression['next_level_xp']
                            )
                        ); ?> XP.
                    </p>
                </div>
            <?php else : ?>
                <div class="gmrc-rise-awaiting gmrc-rise-awaiting--complete">
                    <span aria-hidden="true">★</span>
                    <p>Level 20: fully certified by the Great Marketrealm Guild.</p>
                </div>
            <?php endif; ?>

            <?php if (
                ! empty($pathGifts['gifts'])
                && is_array($pathGifts['gifts'])
            ) : ?>
                <section
                    class="gmrc-path-gifts-ledger"
                    aria-labelledby="gmrc-path-gifts-title"
                >
                    <header>
                        <div>
                            <p class="gmrc-eyebrow">Gifts of the Path</p>
                            <h3 id="gmrc-path-gifts-title">
                                <?php echo esc_html(
                                    (string) ($pathGifts['path_label'] ?? 'Path of Calling')
                                ); ?>
                            </h3>
                        </div>
                        <strong>
                            <?php echo esc_html((string) ($pathGifts['count'] ?? 0)); ?>
                            certified
                        </strong>
                    </header>

                    <div class="gmrc-path-gifts-ledger__grid">
                        <?php foreach ($pathGifts['gifts'] as $gift) : ?>
                            <article>
                                <span aria-hidden="true">✦</span>
                                <div>
                                    <small>
                                        Level <?php echo esc_html((string) ($gift['level'] ?? '')); ?> Gift
                                    </small>
                                    <h4><?php echo esc_html((string) ($gift['label'] ?? '')); ?></h4>
                                    <p><?php echo esc_html((string) ($gift['summary'] ?? '')); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (! empty($livingRegister['has_journey_measure']) && is_array($livingRegister['journey_measure'] ?? null)) : ?>
                <?php $journeyMeasure = $livingRegister['journey_measure']; ?>
                <section class="gmrc-living-register__journey" aria-labelledby="gmrc-journey-measure-title" data-journey-measure tabindex="-1">
                    <header>
                        <div>
                            <p class="gmrc-eyebrow">The Measure of the Journey</p>
                            <h3 id="gmrc-journey-measure-title">What the Chronicle Has Carried</h3>
                            <p>A compact measure of growth drawn only from completed Guild Certifications.</p>
                        </div>
                        <strong><?php echo esc_html((string) $journeyMeasure['certifications']); ?> seals</strong>
                    </header>

                    <dl class="gmrc-living-register__journey-grid">
                        <div><dt>Maximum HP gained</dt><dd>+<?php echo esc_html((string) $journeyMeasure['maximum_hp_gained']); ?></dd></div>
                        <div><dt>Spells entered</dt><dd><?php echo esc_html((string) $journeyMeasure['spells_learned']); ?></dd></div>
                        <div><dt>Cantrips entered</dt><dd><?php echo esc_html((string) $journeyMeasure['cantrips_learned']); ?></dd></div>
                        <div><dt>Path Gifts granted</dt><dd><?php echo esc_html((string) $journeyMeasure['path_gifts_granted']); ?></dd></div>
                        <div><dt>Guild Milestones</dt><dd><?php echo esc_html((string) $journeyMeasure['milestones']); ?></dd></div>
                    </dl>
                </section>
            <?php endif; ?>

            <?php if (! empty($livingRegister['has_change_record']) && is_array($livingRegister['change_record'] ?? null)) : ?>
                <?php $changeRecord = $livingRegister['change_record']; ?>
                <section class="gmrc-living-register__change-record" aria-labelledby="gmrc-change-record-title" data-living-change-record tabindex="-1">
                    <header>
                        <div>
                            <p class="gmrc-eyebrow">The Living Record of Change</p>
                            <h3 id="gmrc-change-record-title">From First Seal to Present Day</h3>
                            <p>The Guild’s clearest measure of how this adventurer has changed across certified advancement.</p>
                        </div>
                        <strong>+<?php echo esc_html((string) $changeRecord['levels_gained']); ?> levels</strong>
                    </header>

                    <dl class="gmrc-living-register__change-grid">
                        <div>
                            <dt>Certified Level</dt>
                            <dd><?php echo esc_html((string) $changeRecord['starting_level']); ?> <span aria-hidden="true">→</span><span class="screen-reader-text"> to </span> <?php echo esc_html((string) $changeRecord['current_level']); ?></dd>
                        </div>
                        <div>
                            <dt>Maximum HP</dt>
                            <dd>
                                <?php if ((int) $changeRecord['starting_maximum_hp'] > 0) : ?>
                                    <?php echo esc_html((string) $changeRecord['starting_maximum_hp']); ?> <span aria-hidden="true">→</span><span class="screen-reader-text"> to </span> <?php echo esc_html((string) $changeRecord['current_maximum_hp']); ?>
                                <?php else : ?>
                                    +<?php echo esc_html((string) $changeRecord['maximum_hp_change']); ?> certified
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>

                    <ol class="gmrc-living-register__change-moments" aria-label="First certified changes">
                        <?php if (is_array($changeRecord['first_path'] ?? null)) : ?>
                            <li><span aria-hidden="true">✦</span><div><strong>Calling Path entered</strong><small>First recorded at Level <?php echo esc_html((string) $changeRecord['first_path']['level']); ?> · Certification <?php echo esc_html((string) $changeRecord['first_path']['sequence']); ?></small></div></li>
                        <?php endif; ?>
                        <?php if (is_array($changeRecord['first_path_gift'] ?? null)) : ?>
                            <li><span aria-hidden="true">✧</span><div><strong>First Gift of the Path</strong><small>First granted at Level <?php echo esc_html((string) $changeRecord['first_path_gift']['level']); ?> · Certification <?php echo esc_html((string) $changeRecord['first_path_gift']['sequence']); ?></small></div></li>
                        <?php endif; ?>
                        <?php if (is_array($changeRecord['first_arcana'] ?? null)) : ?>
                            <li><span aria-hidden="true">◇</span><div><strong>Arcana expanded</strong><small>First recorded spell or cantrip growth at Level <?php echo esc_html((string) $changeRecord['first_arcana']['level']); ?> · Certification <?php echo esc_html((string) $changeRecord['first_arcana']['sequence']); ?></small></div></li>
                        <?php endif; ?>
                    </ol>
                </section>
            <?php endif; ?>

            <?php if (! empty($livingRegister['has_chronicle']) && is_array($livingRegister['chronicle'])) : ?>
                <section class="gmrc-living-register__chronicle" aria-labelledby="gmrc-sealed-chronicle-title" data-sealed-chronicle tabindex="-1">
                    <header>
                        <div>
                            <p class="gmrc-eyebrow">Permanent Guild History</p>
                            <h3 id="gmrc-sealed-chronicle-title">The Sealed Chronicle</h3>
                            <p>Every completed certification remains here as part of the adventurer’s living record.</p>
                        </div>
                        <strong>
                            <?php echo esc_html((string) count($livingRegister['chronicle'])); ?> sealed
                            <?php if (! empty($livingRegister['has_milestones'])) : ?>
                                · <?php echo esc_html((string) $livingRegister['milestone_count']); ?> milestone<?php echo (int) $livingRegister['milestone_count'] === 1 ? '' : 's'; ?>
                            <?php endif; ?>
                        </strong>
                    </header>

                    <ol class="gmrc-living-register__chronicle-list">
                        <?php foreach ($livingRegister['chronicle'] as $entry) : ?>
                            <li<?php echo ! empty($entry['is_latest']) ? ' class="is-latest"' : ''; ?>>
                                <span class="gmrc-living-register__seal" aria-hidden="true">✓</span>
                                <div class="gmrc-living-register__chronicle-entry">
                                    <header>
                                        <div>
                                            <small>Certification <?php echo esc_html((string) $entry['sequence']); ?><?php echo ! empty($entry['is_latest']) ? ' · Latest certification' : ''; ?></small>
                                            <h4>Level <?php echo esc_html((string) $entry['from_level']); ?> → <?php echo esc_html((string) $entry['target_level']); ?></h4>
                                        </div>
                                        <?php if ((string) $entry['certified_at'] !== '') : ?>
                                            <time datetime="<?php echo esc_attr((string) $entry['certified_at']); ?>"><?php echo esc_html(substr((string) $entry['certified_at'], 0, 10)); ?></time>
                                        <?php endif; ?>
                                    </header>
                                    <ul>
                                        <?php if ((int) $entry['hit_point_gain'] > 0) : ?><li>+<?php echo esc_html((string) $entry['hit_point_gain']); ?> maximum HP</li><?php endif; ?>
                                        <?php if ($entry['spells_learned'] !== []) : ?><li><?php echo esc_html((string) count($entry['spells_learned'])); ?> spell<?php echo count($entry['spells_learned']) === 1 ? '' : 's'; ?> learned</li><?php endif; ?>
                                        <?php if ($entry['cantrips_learned'] !== []) : ?><li><?php echo esc_html((string) count($entry['cantrips_learned'])); ?> cantrip<?php echo count($entry['cantrips_learned']) === 1 ? '' : 's'; ?> learned</li><?php endif; ?>
                                        <?php if ($entry['path_gifts_granted'] !== []) : ?><li><?php echo esc_html(implode(', ', $entry['path_gifts_granted'])); ?></li><?php endif; ?>
                                    </ul>
                                    <?php if (! empty($entry['milestones']) && is_array($entry['milestones'])) : ?>
                                        <ul class="gmrc-living-register__milestones" aria-label="Guild milestones">
                                            <?php foreach ($entry['milestones'] as $milestone) : ?>
                                                <li data-guild-milestone="<?php echo esc_attr((string) ($milestone['key'] ?? '')); ?>">
                                                    <span aria-hidden="true"><?php echo esc_html((string) ($milestone['symbol'] ?? '✦')); ?></span>
                                                    <?php echo esc_html((string) ($milestone['label'] ?? 'Guild Milestone')); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>

            <section class="gmrc-ledger-section">
                <header class="gmrc-ledger-section__heading"><h3>Automatic consequences</h3></header>
                <ul class="gmrc-rise-effects">
                    <li>Crossing an XP threshold unlocks an Advancement Ledger; it does not change the character automatically.</li>
                    <li>Each certification advances exactly one level, even when several thresholds have been crossed.</li>
                    <li>Hit point growth will be chosen during advancement rather than silently applied.</li>
                    <li>Proficiency, class features and spell progression are previewed before the advancement is sealed.</li>
                    <li>No character changes are committed until the player completes the advancement process.</li>
                </ul>
            </section>
            <blockquote class="gmrc-ledger-auby-note gmrc-ledger-auby-note--archive"><p>“Growth is mostly paperwork with better hit points.”</p><footer>— Auby</footer></blockquote>
            <p class="gmrc-ledger-page__number" aria-hidden="true">12</p>
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
                        <h4>Progression</h4>
                        <p>New spells and class features will unlock as the adventurer rises.</p>
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
            id="gmrc-ledger-tab-equipment"
            class="gmrc-ledger-tab"
            type="button"
            role="tab"
            aria-selected="false"
            aria-controls="gmrc-ledger-panel-equipment"
            tabindex="-1"
            data-ledger-tab="equipment"
        >
            <span class="gmrc-ledger-tab__icon" aria-hidden="true">🎒</span>
            <span class="gmrc-ledger-tab__label">Equipment</span>
        </button>

        <button
            id="gmrc-ledger-tab-attacks"
            class="gmrc-ledger-tab"
            type="button"
            role="tab"
            aria-selected="false"
            aria-controls="gmrc-ledger-panel-attacks"
            tabindex="-1"
            data-ledger-tab="attacks"
        >
            <span class="gmrc-ledger-tab__icon" aria-hidden="true">⚔</span>
            <span class="gmrc-ledger-tab__label">Attacks</span>
        </button>

<button
    id="gmrc-ledger-tab-arcana"
    class="gmrc-ledger-tab"
    type="button"
    role="tab"
    aria-selected="false"
    aria-controls="gmrc-ledger-panel-arcana"
    tabindex="-1"
    data-ledger-tab="arcana"
>
    <span class="gmrc-ledger-tab__icon" aria-hidden="true">✧</span>
    <span class="gmrc-ledger-tab__label">Spells & Abilities</span>
</button>


<button
    id="gmrc-ledger-tab-progression"
    class="gmrc-ledger-tab"
    type="button"
    role="tab"
    aria-selected="false"
    aria-controls="gmrc-ledger-panel-progression"
    tabindex="-1"
    data-ledger-tab="progression"
>
    <span class="gmrc-ledger-tab__icon" aria-hidden="true">↑</span>
    <span class="gmrc-ledger-tab__label">Progression</span>
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
            <div
                class="gmrc-guild-dice-reaction"
                data-guild-dice-reaction
                data-reaction="none"
                aria-hidden="true"
            >
                <span
                    class="gmrc-guild-dice-reaction__banner"
                    data-guild-dice-reaction-banner
                ></span>
                <span
                    class="gmrc-guild-dice-reaction__confetti"
                    data-guild-dice-confetti
                ></span>
            </div>

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
