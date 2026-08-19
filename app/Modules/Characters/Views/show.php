<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skills;
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

$martialRegister = isset($martialRegister)
    && is_array($martialRegister)
        ? $martialRegister
        : [
            'supported' => false,
        ];

$rageRegister = isset($rageRegister)
    && is_array($rageRegister)
        ? $rageRegister
        : [
            'supported' => false,
        ];

$cunningRegister = isset($cunningRegister)
    && is_array($cunningRegister)
        ? $cunningRegister
        : [
            'supported' => false,
        ];

$disciplineRegister = isset($disciplineRegister)
    && is_array($disciplineRegister)
        ? $disciplineRegister
        : [
            'supported' => false,
        ];

$sacredRegister = isset($sacredRegister)
    && is_array($sacredRegister)
        ? $sacredRegister
        : [
            'supported' => false,
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
    data-character-id="<?php echo esc_attr($characterId); ?>"
    data-character-name="<?php echo esc_attr($name); ?>"
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

            <button
                type="button"
                class="gmrc-guild-dice-launcher"
                data-guild-dice-launcher
            >
                <span aria-hidden="true">★</span>
                Quick Rolls
            </button>
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

    <nav
        class="gmrc-ledger-index"
        aria-label="Character Ledger index"
    >
        <p class="gmrc-ledger-index__label">Guild Ledger Index</p>
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

    </nav>

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
                                'kind' => 'initiative',
                                'source' => 'Initiative',
                                'ability' => 'Dexterity',
                                'proficiency' => 'none',
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
                                        'kind' => 'ability-check',
                                        'source' => $label,
                                        'ability' => $label,
                                        'proficiency' => 'none',
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
                data-vital-measures
                data-maximum-hp="<?php echo esc_attr(
                    (string) $hitPoints->maximum()
                ); ?>"
            >
                <header class="gmrc-ledger-section__heading">
                    <h3>Hit Points</h3>

                    <span data-vital-consciousness>
                        <?php echo esc_html(
                            $character->isConscious()
                                ? 'Conscious'
                                : 'Unconscious'
                        ); ?>
                    </span>
                </header>

                <form
                    class="gmrc-vital-measures"
                    method="post"
                    action="<?php echo esc_url($appRequestUrl); ?>"
                    data-vital-measures-form
                >
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input
                        type="hidden"
                        name="gmrc_route"
                        value="<?php echo esc_attr(
                            'characters/'
                            . rawurlencode($characterId)
                            . '/vital-measures'
                        ); ?>"
                    >
                    <?php wp_nonce_field(
                        'gmrc_character_vitals_' . $characterId,
                        'gmrc_nonce'
                    ); ?>


                    <div class="gmrc-vital-measures__grid">
                        <fieldset>
                            <legend>Current HP</legend>
                            <div class="gmrc-vital-measures__stepper">
                                <button
                                    type="button"
                                    data-vital-adjust="current"
                                    data-vital-delta="-1"
                                    aria-label="Reduce current hit points by 1"
                                >−</button>
                                <input
                                    type="number"
                                    name="current_hp"
                                    min="0"
                                    max="<?php echo esc_attr(
                                        (string) $hitPoints->maximum()
                                    ); ?>"
                                    value="<?php echo esc_attr(
                                        (string) $hitPoints->current()
                                    ); ?>"
                                    inputmode="numeric"
                                    data-vital-current
                                    aria-label="Current hit points"
                                    required
                                >
                                <button
                                    type="button"
                                    data-vital-adjust="current"
                                    data-vital-delta="1"
                                    aria-label="Increase current hit points by 1"
                                >+</button>
                            </div>
                        </fieldset>

                        <div class="gmrc-vital-measures__maximum">
                            <span>Maximum HP</span>
                            <strong><?php echo esc_html(
                                (string) $hitPoints->maximum()
                            ); ?></strong>
                            <small>Guild certified</small>
                        </div>

                        <fieldset>
                            <legend>Temporary HP</legend>
                            <div class="gmrc-vital-measures__stepper">
                                <button
                                    type="button"
                                    data-vital-adjust="temporary"
                                    data-vital-delta="-1"
                                    aria-label="Reduce temporary hit points by 1"
                                >−</button>
                                <input
                                    type="number"
                                    name="temporary_hp"
                                    min="0"
                                    max="999"
                                    value="<?php echo esc_attr(
                                        (string) $hitPoints->temporary()
                                    ); ?>"
                                    inputmode="numeric"
                                    data-vital-temporary
                                    aria-label="Temporary hit points"
                                    required
                                >
                                <button
                                    type="button"
                                    data-vital-adjust="temporary"
                                    data-vital-delta="1"
                                    aria-label="Increase temporary hit points by 1"
                                >+</button>
                            </div>
                        </fieldset>
                    </div>

                    <div class="gmrc-vital-measures__quick">
                        <label>
                            <span>Quick amount</span>
                            <input
                                type="number"
                                min="1"
                                max="999"
                                value="1"
                                inputmode="numeric"
                                data-vital-amount
                            >
                        </label>
                        <button
                            type="button"
                            class="gmrc-vital-measures__damage"
                            data-vital-action="damage"
                        >Apply Damage</button>
                        <button
                            type="button"
                            class="gmrc-vital-measures__heal"
                            data-vital-action="heal"
                        >Apply Healing</button>
                    </div>

                    <p class="gmrc-vital-measures__note">
                        Damage uses temporary HP first. Healing cannot exceed
                        maximum HP. Save to enter the changes into the live
                        Adventuring Measures.
                    </p>

                    <button
                        type="submit"
                        class="gmrc-button gmrc-button--secondary"
                    >
                        Save Adventuring Measures
                    </button>
                </form>
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
                                        'kind' => 'saving-throw',
                                        'source' => $label . ' Save',
                                        'ability' => $label,
                                        'proficiency' => $savingThrow->isProficient()
                                            ? 'proficient'
                                            : 'none',
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
                                        'kind' => 'skill-check',
                                        'source' => $label,
                                        'ability' => ucfirst(
                                            Skills::governingAbility($identifier)
                                        ),
                                        'proficiency' => $skill->hasExpertise()
                                            ? 'expertise'
                                            : (
                                                $skill->isProficient()
                                                    ? 'proficient'
                                                    : 'none'
                                            ),
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

                <section
                    class="gmrc-adventurer-purse"
                    aria-labelledby="gmrc-adventurer-purse-title"
                >
                    <header class="gmrc-adventurer-purse__header">
                        <div>
                            <p class="gmrc-eyebrow">
                                Personal Coin
                            </p>
                            <h3 id="gmrc-adventurer-purse-title">
                                The Adventurer’s Purse
                            </h3>
                        </div>

                        <strong
                            class="gmrc-adventurer-purse__balance"
                            aria-label="<?php echo esc_attr(
                                'Current personal purse: '
                                . $character->purse()->formatted()
                            ); ?>"
                        >
                            <?php echo esc_html(
                                $character
                                    ->purse()
                                    ->formatted()
                            ); ?>
                        </strong>
                    </header>

                    <p class="gmrc-ledger-copy">
                        These coins belong to this adventurer personally.
                        Fellowship funds remain separate in the shared
                        Treasury.
                    </p>

                    <div class="gmrc-adventurer-purse__actions">
                        <?php foreach ([
                            'deposit' => [
                                'label' => 'Add Coin',
                                'button' => 'Add to Purse',
                            ],
                            'withdraw' => [
                                'label' => 'Spend Coin',
                                'button' => 'Spend from Purse',
                            ],
                        ] as $purseAction => $purseCopy) : ?>
                            <form
                                class="gmrc-adventurer-purse__form"
                                method="post"
                                action="<?php echo esc_url(
                                    $appRequestUrl
                                ); ?>"
                            >
                                <input
                                    type="hidden"
                                    name="action"
                                    value="gmrc_app_request"
                                >
                                <input
                                    type="hidden"
                                    name="gmrc_route"
                                    value="<?php echo esc_attr(
                                        'characters/'
                                        . rawurlencode(
                                            $characterId
                                        )
                                        . '/purse/'
                                        . $purseAction
                                    ); ?>"
                                >

                                <?php wp_nonce_field(
                                    'gmrc_character_purse_'
                                    . $characterId,
                                    'gmrc_nonce'
                                ); ?>

                                <h4>
                                    <?php echo esc_html(
                                        $purseCopy['label']
                                    ); ?>
                                </h4>

                                <div class="gmrc-adventurer-purse__coins">
                                    <?php foreach ([
                                        'gold' => 'GP',
                                        'silver' => 'SP',
                                        'copper' => 'CP',
                                    ] as $coin => $coinLabel) : ?>
                                        <label>
                                            <span>
                                                <?php echo esc_html(
                                                    $coinLabel
                                                ); ?>
                                            </span>
                                            <input
                                                type="number"
                                                name="<?php echo esc_attr(
                                                    $coin
                                                ); ?>"
                                                value="0"
                                                min="0"
                                                <?php echo $coin === 'gold'
                                                    ? 'max="999999"'
                                                    : 'max="9"'; ?>
                                                inputmode="numeric"
                                                required
                                            >
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <button
                                    class="gmrc-button <?php echo esc_attr(
                                        $purseAction === 'deposit'
                                            ? 'gmrc-button--secondary'
                                            : 'gmrc-button--quiet'
                                    ); ?>"
                                    type="submit"
                                >
                                    <?php echo esc_html(
                                        $purseCopy['button']
                                    ); ?>
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>

                    <aside class="gmrc-adventurer-purse__auby">
                        <span aria-hidden="true">🍆</span>
                        <p>
                            “Personal purse on the left, Fellowship coffers
                            on the right. Mixing them without paperwork is
                            how Quartermasters start twitching.”
                        </p>
                    </aside>
                </section>

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
                                    <button type="button" class="gmrc-guild-roll-trigger" data-guild-roll="d20" data-roll-kind="attack" data-roll-target-mode="<?php echo esc_attr((string) ($attack['target_mode'] ?? 'none')); ?>" data-roll-default-target-kind="<?php echo esc_attr((string) ($attack['default_target_kind'] ?? '')); ?>" data-roll-source="<?php echo esc_attr($attack['label']); ?>" data-roll-ability="<?php echo esc_attr((string) $attack['ability']); ?>" data-roll-proficiency="proficient" data-roll-label="<?php echo esc_attr($attack['label'] . ' — Attack'); ?>" data-roll-modifier="<?php echo esc_attr((string) $attack['attack_bonus']); ?>" data-roll-result-suffix="to hit" data-roll-critical-formula="<?php echo esc_attr((string) $attack['critical_damage_die']); ?>" data-roll-critical-modifier="<?php echo esc_attr((string) $attack['damage_modifier']); ?>" data-roll-critical-damage-type="<?php echo esc_attr((string) $attack['damage_type']); ?>">
                                        <span aria-hidden="true">⚔</span> Roll Attack
                                    </button>
                                    <button type="button" class="gmrc-guild-roll-trigger gmrc-guild-roll-trigger--damage" data-guild-roll="damage" data-roll-kind="damage" data-roll-target-mode="<?php echo esc_attr((string) ($attack['target_mode'] ?? 'none')); ?>" data-roll-default-target-kind="<?php echo esc_attr((string) ($attack['default_target_kind'] ?? '')); ?>" data-roll-source="<?php echo esc_attr($attack['label']); ?>" data-roll-ability="<?php echo esc_attr((string) $attack['ability']); ?>" data-roll-proficiency="proficient" data-roll-label="<?php echo esc_attr($attack['label'] . ' — Damage'); ?>" data-roll-formula="<?php echo esc_attr($attack['damage_die']); ?>" data-roll-modifier="<?php echo esc_attr((string) $attack['damage_modifier']); ?>" data-roll-damage-type="<?php echo esc_attr($attack['damage_type']); ?>">
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

            <?php if (
                ! empty($sacredRegister['supported'])
            ) : ?>
                <section
                    class="gmrc-sacred-register"
                    aria-labelledby="gmrc-sacred-register-title"
                    data-sacred-register
                >
                    <header class="gmrc-sacred-register__heading">
                        <div>
                            <p class="gmrc-eyebrow">
                                Paladin Field Record
                            </p>
                            <h3 id="gmrc-sacred-register-title">
                                The Sacred Register
                            </h3>
                            <p>
                                Certified sacred capability at Paladin Level
                                <?php echo esc_html(
                                    (string) (
                                        $sacredRegister['level']
                                        ?? $level
                                    )
                                ); ?>.
                            </p>
                        </div>

                        <span class="gmrc-sacred-register__pool">
                            <strong>
                                <?php echo esc_html(
                                    sprintf(
                                        '%d/%d',
                                        (int) (
                                            $sacredRegister[
                                                'lay_on_hands'
                                            ]['remaining']
                                            ?? 0
                                        ),
                                        (int) (
                                            $sacredRegister[
                                                'lay_on_hands'
                                            ]['maximum']
                                            ?? 0
                                        )
                                    )
                                ); ?>
                            </strong>
                            <small>
                                Lay on Hands remaining
                            </small>
                        </span>
                    </header>

                    <div class="gmrc-sacred-register__summary">
                        <article>
                            <small>
                                Divine Sense
                            </small>
                            <strong>
                                <?php echo esc_html(
                                    sprintf(
                                        '%d/%d',
                                        (int) (
                                            $sacredRegister[
                                                'divine_sense'
                                            ]['remaining']
                                            ?? 0
                                        ),
                                        (int) (
                                            $sacredRegister[
                                                'divine_sense'
                                            ]['maximum']
                                            ?? 0
                                        )
                                    )
                                ); ?>
                                uses
                            </strong>
                            <span>Long rest</span>
                        </article>

                        <article>
                            <small>
                                Sacred Save DC
                            </small>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $sacredRegister[
                                            'sacred_save_dc'
                                        ]
                                        ?? 0
                                    )
                                ); ?>
                            </strong>
                            <span>
                                Charisma based
                            </span>
                        </article>

                        <article>
                            <small>
                                Sacred Aura
                            </small>
                            <strong>
                                <?php if (
                                    ! empty(
                                        $sacredRegister[
                                            'aura'
                                        ]['unlocked']
                                    )
                                ) : ?>
                                    <?php echo esc_html(
                                        (string) (
                                            $sacredRegister[
                                                'aura'
                                            ]['range_feet']
                                            ?? 0
                                        )
                                    ); ?> ft
                                <?php else : ?>
                                    Not yet
                                <?php endif; ?>
                            </strong>
                            <span>
                                Opens at Level 6
                            </span>
                        </article>

                        <article>
                            <small>
                                Cleansing Touch
                            </small>
                            <strong>
                                <?php if (
                                    ! empty(
                                        $sacredRegister[
                                            'cleansing_touch'
                                        ]['unlocked']
                                    )
                                ) : ?>
                                    <?php echo esc_html(
                                        sprintf(
                                            '%d/%d',
                                            (int) (
                                                $sacredRegister[
                                                    'cleansing_touch'
                                                ]['remaining']
                                                ?? 0
                                            ),
                                            (int) (
                                                $sacredRegister[
                                                    'cleansing_touch'
                                                ]['maximum']
                                                ?? 0
                                            )
                                        )
                                    ); ?>
                                    uses
                                <?php else : ?>
                                    Not yet
                                <?php endif; ?>
                            </strong>
                            <span>
                                Opens at Level 14
                            </span>
                        </article>
                    </div>

                    <?php
                    $sacredActions = is_array(
                        $sacredRegister['actions']
                        ?? null
                    )
                        ? $sacredRegister['actions']
                        : [];
                    ?>

                    <section
                        class="gmrc-sacred-actions"
                        aria-labelledby="gmrc-sacred-actions-title"
                        data-sacred-actions
                    >
                        <header>
                            <div>
                                <p class="gmrc-eyebrow">
                                    Active Play
                                </p>
                                <h4 id="gmrc-sacred-actions-title">
                                    Sacred Actions
                                </h4>
                            </div>
                            <span>
                                Paladin field tools
                            </span>
                        </header>

                        <div class="gmrc-sacred-actions__grid">
                            <article>
                                <small>
                                    Action · Lay on Hands
                                </small>
                                <strong>
                                    Restore from the sacred pool
                                </strong>
                                <p>
                                    Choose how many Lay on Hands points to spend.
                                    Heal this Paladin directly, or record the spend
                                    when healing another creature at the table.
                                </p>

                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                    class="gmrc-sacred-action-form"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="gmrc_app_request"
                                    >
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/sacred/action'
                                        ); ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="sacred_action"
                                        value="lay-on-hands"
                                    >

                                    <label>
                                        <span>Points</span>
                                        <input
                                            type="number"
                                            name="amount"
                                            value="1"
                                            min="1"
                                            max="<?php echo esc_attr(
                                                (string) max(
                                                    1,
                                                    (int) (
                                                        $sacredRegister[
                                                            'lay_on_hands'
                                                        ]['remaining']
                                                        ?? 0
                                                    )
                                                )
                                            ); ?>"
                                        >
                                    </label>

                                    <label>
                                        <span>Recipient</span>
                                        <select name="target">
                                            <option value="self">
                                                Heal this Paladin
                                            </option>
                                            <option value="other">
                                                Record spend for another creature
                                            </option>
                                        </select>
                                    </label>

                                    <?php wp_nonce_field(
                                        'gmrc_character_sacred_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>

                                    <button
                                        type="submit"
                                        class="gmrc-button"
                                        data-sacred-spend
                                        data-sacred-action="lay-on-hands"
                                        <?php echo empty(
                                            $sacredActions[
                                                'lay_on_hands'
                                            ]['available']
                                        )
                                            ? 'disabled'
                                            : ''; ?>
                                    >
                                        Use Lay on Hands
                                    </button>
                                </form>
                            </article>

                            <article>
                                <small>
                                    Action · Divine Sense
                                </small>
                                <strong>
                                    Open sacred awareness
                                </strong>
                                <p>
                                    Spend one Divine Sense use when the Paladin
                                    opens their awareness to a qualifying presence.
                                </p>

                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="gmrc_app_request"
                                    >
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/sacred/action'
                                        ); ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="sacred_action"
                                        value="divine-sense"
                                    >
                                    <?php wp_nonce_field(
                                        'gmrc_character_sacred_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>
                                    <button
                                        type="submit"
                                        class="gmrc-button"
                                        data-sacred-spend
                                        data-sacred-action="divine-sense"
                                        <?php echo empty(
                                            $sacredActions[
                                                'divine_sense'
                                            ]['available']
                                        )
                                            ? 'disabled'
                                            : ''; ?>
                                    >
                                        Activate Divine Sense
                                    </button>
                                </form>
                            </article>

                            <article class="<?php echo esc_attr(
                                ! empty(
                                    $sacredActions[
                                        'cleansing_touch'
                                    ]['unlocked']
                                )
                                    ? 'is-unlocked'
                                    : 'is-locked'
                            ); ?>">
                                <small>
                                    Level 14 · Cleansing Touch
                                </small>
                                <strong>
                                    Break a qualifying magical effect
                                </strong>
                                <p>
                                    The Companion records one use; the table
                                    confirms the effect being ended qualifies.
                                </p>

                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="gmrc_app_request"
                                    >
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/sacred/action'
                                        ); ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="sacred_action"
                                        value="cleansing-touch"
                                    >
                                    <?php wp_nonce_field(
                                        'gmrc_character_sacred_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>
                                    <button
                                        type="submit"
                                        class="gmrc-button"
                                        data-sacred-spend
                                        data-sacred-action="cleansing-touch"
                                        <?php echo empty(
                                            $sacredActions[
                                                'cleansing_touch'
                                            ]['available']
                                        )
                                            ? 'disabled'
                                            : ''; ?>
                                    >
                                        Use Cleansing Touch
                                    </button>
                                </form>
                            </article>

                            <article class="<?php echo esc_attr(
                                ! empty(
                                    $sacredActions[
                                        'divine_smite'
                                    ]['unlocked']
                                )
                                    ? 'is-unlocked'
                                    : 'is-locked'
                            ); ?>">
                                <small>
                                    Level 2 · Divine Smite
                                </small>
                                <strong>
                                    Commit a real spell slot
                                </strong>
                                <p>
                                    <?php echo esc_html(
                                        (string) (
                                            $sacredActions[
                                                'divine_smite'
                                            ]['qualification']
                                            ?? ''
                                        )
                                    ); ?>
                                </p>

                                <?php foreach (
                                    (
                                        $sacredActions[
                                            'divine_smite'
                                        ]['smite_options']
                                        ?? []
                                    )
                                    as $smite
                                ) : ?>
                                    <div class="gmrc-sacred-smite-option">
                                        <div>
                                            <strong>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $smite['label']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </strong>
                                            <small>
                                                <?php echo esc_html(
                                                    sprintf(
                                                        '%d/%d slots ready',
                                                        (int) (
                                                            $smite['remaining']
                                                            ?? 0
                                                        ),
                                                        (int) (
                                                            $smite['total']
                                                            ?? 0
                                                        )
                                                    )
                                                ); ?>
                                            </small>
                                        </div>

                                        <form
                                            action="<?php echo esc_url(
                                                $appRequestUrl
                                            ); ?>"
                                            method="post"
                                        >
                                            <input
                                                type="hidden"
                                                name="action"
                                                value="gmrc_app_request"
                                            >
                                            <input
                                                type="hidden"
                                                name="gmrc_route"
                                                value="<?php echo esc_attr(
                                                    'characters/'
                                                    . $characterId
                                                    . '/sacred/action'
                                                ); ?>"
                                            >
                                            <input
                                                type="hidden"
                                                name="sacred_action"
                                                value="divine-smite"
                                            >
                                            <input
                                                type="hidden"
                                                name="slot_level"
                                                value="<?php echo esc_attr(
                                                    (string) (
                                                        $smite['slot_level']
                                                        ?? 1
                                                    )
                                                ); ?>"
                                            >
                                            <?php wp_nonce_field(
                                                'gmrc_character_sacred_'
                                                . $characterId,
                                                'gmrc_nonce'
                                            ); ?>
                                            <button
                                                type="submit"
                                                class="gmrc-button"
                                                data-sacred-action="divine-smite"
                                                data-smite-slot="<?php echo esc_attr(
                                                    (string) (
                                                        $smite['slot_level']
                                                        ?? 1
                                                    )
                                                ); ?>"
                                                <?php echo empty(
                                                    $smite['available']
                                                )
                                                    ? 'disabled'
                                                    : ''; ?>
                                            >
                                                Commit Slot
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="
                                                gmrc-guild-roll-trigger
                                                gmrc-guild-roll-trigger--damage
                                            "
                                            data-guild-roll="damage"
                                            data-roll-kind="damage"
                                            data-roll-source="Divine Smite"
                                            data-roll-label="Divine Smite — Radiant Damage"
                                            data-roll-formula="<?php echo esc_attr(
                                                (string) (
                                                    $smite['formula']
                                                    ?? '2d8'
                                                )
                                            ); ?>"
                                            data-roll-modifier="0"
                                            data-roll-damage-type="radiant"
                                            <?php echo empty(
                                                $smite['available']
                                            )
                                                ? 'disabled'
                                                : ''; ?>
                                        >
                                            🎲 Roll Smite
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </article>
                        </div>

                        <form
                            action="<?php echo esc_url(
                                $appRequestUrl
                            ); ?>"
                            method="post"
                            class="gmrc-sacred-actions__rest"
                        >
                            <input
                                type="hidden"
                                name="action"
                                value="gmrc_app_request"
                            >
                            <input
                                type="hidden"
                                name="gmrc_route"
                                value="<?php echo esc_attr(
                                    'characters/'
                                    . $characterId
                                    . '/sacred/rest'
                                ); ?>"
                            >
                            <?php wp_nonce_field(
                                'gmrc_character_sacred_'
                                . $characterId,
                                'gmrc_nonce'
                            ); ?>
                            <button
                                type="submit"
                                class="gmrc-button gmrc-button--secondary"
                                data-sacred-rest
                            >
                                Take a Long Rest
                            </button>
                        </form>
                    </section>

                    <div class="gmrc-sacred-register__features">
                        <?php foreach (
                            ($sacredRegister['features'] ?? [])
                            as $feature
                        ) : ?>
                            <article class="<?php echo esc_attr(
                                ! empty($feature['unlocked'])
                                    ? 'is-unlocked'
                                    : 'is-locked'
                            ); ?>">
                                <small>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $feature['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        ! empty($feature['unlocked'])
                                            ? 'Certified'
                                            : 'Locked'
                                    ); ?>
                                </small>

                                <strong>
                                    <?php echo esc_html(
                                        (string) (
                                            $feature['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>

                                <p>
                                    <?php echo esc_html(
                                        (string) (
                                            $feature['detail']
                                            ?? ''
                                        )
                                    ); ?>
                                </p>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="gmrc-sacred-register__footer">
                        <div>
                            <span>
                                Sacred Oath
                            </span>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $sacredRegister[
                                            'oath'
                                        ]['label']
                                        ?? 'Awaiting Sacred Oath'
                                    )
                                ); ?>
                            </strong>
                        </div>

                        <?php if (
                            is_array(
                                $sacredRegister[
                                    'next_milestone'
                                ]
                                ?? null
                            )
                        ) : ?>
                            <div>
                                <span>
                                    Next sacred milestone
                                </span>
                                <strong>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $sacredRegister[
                                                'next_milestone'
                                            ]['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $sacredRegister[
                                                'next_milestone'
                                            ]['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>
                            </div>
                        <?php else : ?>
                            <div>
                                <span>
                                    Paladin progression
                                </span>
                                <strong>
                                    Sacred milestones certified
                                </strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (
                ! empty($disciplineRegister['supported'])
            ) : ?>
                <section
                    class="gmrc-discipline-register"
                    aria-labelledby="gmrc-discipline-register-title"
                    data-discipline-register
                >
                    <header class="gmrc-discipline-register__heading">
                        <div>
                            <p class="gmrc-eyebrow">Monk Field Record</p>
                            <h3 id="gmrc-discipline-register-title">
                                The Discipline Register
                            </h3>
                            <p>
                                Certified Monk capability at Level
                                <?php echo esc_html(
                                    (string) (
                                        $disciplineRegister['level']
                                        ?? $level
                                    )
                                ); ?>.
                            </p>
                        </div>
                        <span class="gmrc-discipline-register__pool">
                            <strong>
                                <?php echo esc_html(
                                    sprintf(
                                        '%d/%d',
                                        (int) (
                                            $disciplineRegister[
                                                'discipline'
                                            ]['remaining']
                                            ?? 0
                                        ),
                                        (int) (
                                            $disciplineRegister[
                                                'discipline'
                                            ]['maximum']
                                            ?? 0
                                        )
                                    )
                                ); ?>
                            </strong>
                            <small>Remaining Discipline</small>
                        </span>
                    </header>

                    <div class="gmrc-discipline-register__summary">
                        <article>
                            <small>Discipline Save DC</small>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $disciplineRegister[
                                            'discipline'
                                        ]['save_dc']
                                        ?? 0
                                    )
                                ); ?>
                            </strong>
                        </article>
                        <article>
                            <small>Unarmoured Movement</small>
                            <strong>
                                +<?php echo esc_html(
                                    (string) (
                                        $disciplineRegister[
                                            'movement'
                                        ]['bonus_feet']
                                        ?? 0
                                    )
                                ); ?> ft
                            </strong>
                        </article>
                    </div>

                    <?php if (
                        ! empty(
                            $disciplineRegister[
                                'discipline'
                            ]['unlocked']
                        )
                    ) : ?>
                        <div
                            class="gmrc-discipline-register__controls"
                            data-discipline-reserves
                        >
                            <form
                                action="<?php echo esc_url(
                                    $appRequestUrl
                                ); ?>"
                                method="post"
                            >
                                <input
                                    type="hidden"
                                    name="action"
                                    value="gmrc_app_request"
                                >
                                <input
                                    type="hidden"
                                    name="gmrc_route"
                                    value="<?php echo esc_attr(
                                        'characters/'
                                        . $characterId
                                        . '/discipline/rest'
                                    ); ?>"
                                >
                                <input
                                    type="hidden"
                                    name="rest"
                                    value="short"
                                >
                                <?php wp_nonce_field(
                                    'gmrc_character_discipline_'
                                    . $characterId,
                                    'gmrc_nonce'
                                ); ?>
                                <button
                                    type="submit"
                                    class="gmrc-button gmrc-button--secondary"
                                    data-discipline-rest="short"
                                >
                                    Take a Short Rest
                                </button>
                            </form>

                            <form
                                action="<?php echo esc_url(
                                    $appRequestUrl
                                ); ?>"
                                method="post"
                            >
                                <input
                                    type="hidden"
                                    name="action"
                                    value="gmrc_app_request"
                                >
                                <input
                                    type="hidden"
                                    name="gmrc_route"
                                    value="<?php echo esc_attr(
                                        'characters/'
                                        . $characterId
                                        . '/discipline/rest'
                                    ); ?>"
                                >
                                <input
                                    type="hidden"
                                    name="rest"
                                    value="long"
                                >
                                <?php wp_nonce_field(
                                    'gmrc_character_discipline_'
                                    . $characterId,
                                    'gmrc_nonce'
                                ); ?>
                                <button
                                    type="submit"
                                    class="gmrc-button gmrc-button--secondary"
                                    data-discipline-rest="long"
                                >
                                    Take a Long Rest
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php
                    $monkTechniques = is_array(
                        $disciplineRegister[
                            'martial_techniques'
                        ]['techniques']
                        ?? null
                    )
                        ? $disciplineRegister[
                            'martial_techniques'
                        ]['techniques']
                        : [];
                    ?>

                    <?php if (! empty($monkTechniques)) : ?>
                        <section
                            class="gmrc-monk-techniques"
                            aria-labelledby="gmrc-monk-techniques-title"
                            data-monk-techniques
                        >
                            <header>
                                <div>
                                    <p class="gmrc-eyebrow">
                                        Active Play
                                    </p>
                                    <h4 id="gmrc-monk-techniques-title">
                                        Martial Techniques
                                    </h4>
                                </div>
                                <span>
                                    <?php echo esc_html(
                                        sprintf(
                                            '%d Discipline ready',
                                            (int) (
                                                $disciplineRegister[
                                                    'discipline'
                                                ]['remaining']
                                                ?? 0
                                            )
                                        )
                                    ); ?>
                                </span>
                            </header>

                            <div class="gmrc-monk-techniques__grid">
                                <?php foreach (
                                    $monkTechniques
                                    as $technique
                                ) : ?>
                                    <article class="<?php echo esc_attr(
                                        ! empty(
                                            $technique['unlocked']
                                        )
                                            ? 'is-unlocked'
                                            : 'is-locked'
                                    ); ?>">
                                        <div class="gmrc-monk-techniques__title">
                                            <small>
                                                Level <?php echo esc_html(
                                                    (string) (
                                                        $technique['level']
                                                        ?? ''
                                                    )
                                                ); ?>
                                                ·
                                                <?php echo esc_html(
                                                    ! empty(
                                                        $technique['unlocked']
                                                    )
                                                        ? 'Certified'
                                                        : 'Locked'
                                                ); ?>
                                            </small>
                                            <strong>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $technique['label']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </strong>
                                            <span>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $technique['badge']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </span>
                                        </div>

                                        <p>
                                            <?php echo esc_html(
                                                (string) (
                                                    $technique['summary']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </p>
                                        <small>
                                            <?php echo esc_html(
                                                (string) (
                                                    $technique['detail']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </small>

                                        <?php if (
                                            is_array(
                                                $technique['roll']
                                                ?? null
                                            )
                                        ) : ?>
                                            <button
                                                type="button"
                                                class="
                                                    gmrc-guild-roll-trigger
                                                    gmrc-monk-technique__button
                                                "
                                                data-guild-roll="damage"
                                                data-roll-kind="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['kind']
                                                        ?? 'damage'
                                                    )
                                                ); ?>"
                                                data-roll-source="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['source']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-roll-label="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['label']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-roll-formula="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['formula']
                                                        ?? '1d10'
                                                    )
                                                ); ?>"
                                                data-roll-modifier="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['modifier']
                                                        ?? 0
                                                    )
                                                ); ?>"
                                                data-roll-result-suffix="<?php echo esc_attr(
                                                    (string) (
                                                        $technique['roll']['result_suffix']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                <?php echo empty(
                                                    $technique['unlocked']
                                                )
                                                    ? 'disabled'
                                                    : ''; ?>
                                            >
                                                🎲 Roll Reduction
                                            </button>
                                        <?php endif; ?>

                                        <?php if (
                                            ($technique['kind'] ?? '')
                                            === 'discipline-spend'
                                        ) : ?>
                                            <form
                                                action="<?php echo esc_url(
                                                    $appRequestUrl
                                                ); ?>"
                                                method="post"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="gmrc_app_request"
                                                >
                                                <input
                                                    type="hidden"
                                                    name="gmrc_route"
                                                    value="<?php echo esc_attr(
                                                        'characters/'
                                                        . $characterId
                                                        . '/discipline/spend'
                                                    ); ?>"
                                                >
                                                <input
                                                    type="hidden"
                                                    name="technique"
                                                    value="<?php echo esc_attr(
                                                        (string) (
                                                            $technique['key']
                                                            ?? ''
                                                        )
                                                    ); ?>"
                                                >
                                                <?php wp_nonce_field(
                                                    'gmrc_character_discipline_'
                                                    . $characterId,
                                                    'gmrc_nonce'
                                                ); ?>
                                                <button
                                                    type="submit"
                                                    class="gmrc-monk-technique__button"
                                                    data-discipline-spend
                                                    data-discipline-spend
                                                    data-discipline-technique="<?php echo esc_attr(
                                                        (string) (
                                                            $technique['key']
                                                            ?? ''
                                                        )
                                                    ); ?>"
                                                    <?php echo empty(
                                                        $technique['available']
                                                    )
                                                        ? 'disabled'
                                                        : ''; ?>
                                                >
                                                    Spend 1 Discipline
                                                    ·
                                                    <?php echo esc_html(
                                                        (string) (
                                                            $technique['label']
                                                            ?? ''
                                                        )
                                                    ); ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (
                                            is_array(
                                                $technique['follow_up']
                                                ?? null
                                            )
                                        ) : ?>
                                            <form
                                                action="<?php echo esc_url(
                                                    $appRequestUrl
                                                ); ?>"
                                                method="post"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="gmrc_app_request"
                                                >
                                                <input
                                                    type="hidden"
                                                    name="gmrc_route"
                                                    value="<?php echo esc_attr(
                                                        'characters/'
                                                        . $characterId
                                                        . '/discipline/spend'
                                                    ); ?>"
                                                >
                                                <input
                                                    type="hidden"
                                                    name="technique"
                                                    value="<?php echo esc_attr(
                                                        (string) (
                                                            $technique[
                                                                'follow_up'
                                                            ]['key']
                                                            ?? ''
                                                        )
                                                    ); ?>"
                                                >
                                                <?php wp_nonce_field(
                                                    'gmrc_character_discipline_'
                                                    . $characterId,
                                                    'gmrc_nonce'
                                                ); ?>
                                                <button
                                                    type="submit"
                                                    class="
                                                        gmrc-monk-technique__button
                                                        gmrc-monk-technique__button--quiet
                                                    "
                                                    data-discipline-technique="<?php echo esc_attr(
                                                        (string) (
                                                            $technique[
                                                                'follow_up'
                                                            ]['key']
                                                            ?? ''
                                                        )
                                                    ); ?>"
                                                    <?php echo empty(
                                                        $technique[
                                                            'follow_up'
                                                        ]['available']
                                                    )
                                                        ? 'disabled'
                                                        : ''; ?>
                                                >
                                                    Spend 1 Discipline
                                                    · Return Missile
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="gmrc-discipline-register__features">
                        <?php foreach (
                            ($disciplineRegister['features'] ?? [])
                            as $feature
                        ) : ?>
                            <article class="<?php echo esc_attr(
                                ! empty($feature['unlocked'])
                                    ? 'is-unlocked'
                                    : 'is-locked'
                            ); ?>">
                                <small>
                                    Level <?php echo esc_html(
                                        (string) ($feature['level'] ?? '')
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        ! empty($feature['unlocked'])
                                            ? 'Certified'
                                            : 'Locked'
                                    ); ?>
                                </small>
                                <strong>
                                    <?php echo esc_html(
                                        (string) ($feature['label'] ?? '')
                                    ); ?>
                                </strong>
                                <p>
                                    <?php echo esc_html(
                                        (string) ($feature['detail'] ?? '')
                                    ); ?>
                                </p>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="gmrc-discipline-register__footer">
                        <div>
                            <span>Monastic Way</span>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $disciplineRegister['way']['label']
                                        ?? 'Awaiting Monastic Way'
                                    )
                                ); ?>
                            </strong>
                        </div>
                        <?php if (
                            is_array(
                                $disciplineRegister['next_milestone']
                                ?? null
                            )
                        ) : ?>
                            <div>
                                <span>Next discipline milestone</span>
                                <strong>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $disciplineRegister[
                                                'next_milestone'
                                            ]['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $disciplineRegister[
                                                'next_milestone'
                                            ]['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>
                            </div>
                        <?php else : ?>
                            <div>
                                <span>Monk progression</span>
                                <strong>Mastered to Level 20</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (
                ! empty($cunningRegister['supported'])
            ) : ?>
                <section
                    class="gmrc-cunning-register"
                    aria-labelledby="gmrc-cunning-register-title"
                    data-cunning-register
                >
                    <header class="gmrc-cunning-register__heading">
                        <div>
                            <p class="gmrc-eyebrow">
                                Rogue Field Record
                            </p>
                            <h3 id="gmrc-cunning-register-title">
                                The Cunning Register
                            </h3>
                            <p>
                                Certified Rogue capability at Level
                                <?php echo esc_html(
                                    (string) (
                                        $cunningRegister['level']
                                        ?? $level
                                    )
                                ); ?>.
                            </p>
                        </div>

                        <span
                            class="gmrc-cunning-register__sneak"
                            aria-label="<?php echo esc_attr(
                                sprintf(
                                    'Sneak Attack %s, once per turn',
                                    (string) (
                                        $cunningRegister[
                                            'sneak_attack'
                                        ]['dice']
                                        ?? '1d6'
                                    )
                                )
                            ); ?>"
                        >
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $cunningRegister[
                                            'sneak_attack'
                                        ]['dice']
                                        ?? '1d6'
                                    )
                                ); ?>
                            </strong>
                            <small>Sneak Attack</small>
                        </span>
                    </header>

                    <div class="gmrc-cunning-register__summary">
                        <article>
                            <span aria-hidden="true">🗡️</span>
                            <div>
                                <small>Precision Damage</small>
                                <h4>Sneak Attack</h4>
                                <p>
                                    <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'sneak_attack'
                                            ]['frequency']
                                            ?? 'Once per turn'
                                        )
                                    ); ?>.
                                    Contextual attack handling arrives in
                                    Phase III.12.4D.
                                </p>
                            </div>
                        </article>

                        <article
                            class="<?php echo esc_attr(
                                ! empty(
                                    $cunningRegister[
                                        'cunning_action'
                                    ]['unlocked']
                                )
                                    ? 'is-unlocked'
                                    : 'is-locked'
                            ); ?>"
                        >
                            <span aria-hidden="true">◇</span>
                            <div>
                                <small>
                                    <?php echo esc_html(
                                        ! empty(
                                            $cunningRegister[
                                                'cunning_action'
                                            ]['unlocked']
                                        )
                                            ? 'Certified at Level 2'
                                            : 'Unlocks at Level 2'
                                    ); ?>
                                </small>
                                <h4>Cunning Action</h4>
                                <p>
                                    <?php echo esc_html(
                                        implode(
                                            ' · ',
                                            (array) (
                                                $cunningRegister[
                                                    'cunning_action'
                                                ]['options']
                                                ?? []
                                            )
                                        )
                                    ); ?>
                                </p>
                            </div>
                        </article>
                    </div>

                    <?php if (
                        ! empty(
                            $cunningRegister[
                                'cunning_action'
                            ]['actions']
                        )
                    ) : ?>
                        <section
                            class="gmrc-cunning-actions"
                            aria-labelledby="gmrc-cunning-actions-title"
                            data-cunning-actions
                        >
                            <header>
                                <div>
                                    <p class="gmrc-eyebrow">
                                        Active Play
                                    </p>
                                    <h4 id="gmrc-cunning-actions-title">
                                        Cunning Actions
                                    </h4>
                                </div>
                                <span>
                                    <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'cunning_action'
                                            ]['cost']
                                            ?? 'Bonus action'
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'cunning_action'
                                            ]['refresh']
                                            ?? 'Every turn'
                                        )
                                    ); ?>
                                </span>
                            </header>

                            <div class="gmrc-cunning-actions__grid">
                                <?php foreach (
                                    $cunningRegister[
                                        'cunning_action'
                                    ]['actions']
                                    as $cunningAction
                                ) : ?>
                                    <?php
                                    $cunningRoll = is_array(
                                        $cunningAction['roll']
                                        ?? null
                                    )
                                        ? $cunningAction['roll']
                                        : null;

                                    $cunningUnlocked = ! empty(
                                        $cunningAction['unlocked']
                                    );
                                    ?>
                                    <article
                                        class="<?php echo esc_attr(
                                            $cunningUnlocked
                                                ? 'is-unlocked'
                                                : 'is-locked'
                                        ); ?>"
                                    >
                                        <div class="gmrc-cunning-actions__title">
                                            <span aria-hidden="true">
                                                <?php echo esc_html(
                                                    (string) (
                                                        $cunningAction['icon']
                                                        ?? '◇'
                                                    )
                                                ); ?>
                                            </span>
                                            <div>
                                                <small>
                                                    <?php echo esc_html(
                                                        $cunningUnlocked
                                                            ? 'Ready every turn'
                                                            : 'Unlocks at Level 2'
                                                    ); ?>
                                                </small>
                                                <strong>
                                                    <?php echo esc_html(
                                                        (string) (
                                                            $cunningAction['label']
                                                            ?? ''
                                                        )
                                                    ); ?>
                                                </strong>
                                            </div>
                                        </div>

                                        <p>
                                            <?php echo esc_html(
                                                (string) (
                                                    $cunningAction['summary']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </p>

                                        <small>
                                            <?php echo esc_html(
                                                (string) (
                                                    $cunningAction['detail']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </small>

                                        <?php if (
                                            is_array($cunningRoll)
                                        ) : ?>
                                            <button
                                                type="button"
                                                class="
                                                    gmrc-guild-roll-trigger
                                                    gmrc-cunning-action-button
                                                "
                                                data-guild-roll="d20"
                                                data-roll-kind="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['kind']
                                                        ?? 'ability-check'
                                                    )
                                                ); ?>"
                                                data-roll-source="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['source']
                                                        ?? 'Cunning Action'
                                                    )
                                                ); ?>"
                                                data-roll-ability="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['ability']
                                                        ?? 'dexterity'
                                                    )
                                                ); ?>"
                                                data-roll-proficiency="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['proficiency']
                                                        ?? 'none'
                                                    )
                                                ); ?>"
                                                data-roll-label="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['label']
                                                        ?? 'Cunning Action'
                                                    )
                                                ); ?>"
                                                data-roll-modifier="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['modifier']
                                                        ?? 0
                                                    )
                                                ); ?>"
                                                data-roll-result-suffix="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['result_suffix']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-roll-default-mode="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningRoll['default_mode']
                                                        ?? 'normal'
                                                    )
                                                ); ?>"
                                                <?php echo
                                                    ! $cunningUnlocked
                                                        ? 'disabled'
                                                        : ''; ?>
                                            >
                                                <span aria-hidden="true">🎲</span>
                                                Roll Hide
                                            </button>
                                        <?php else : ?>
                                            <button
                                                type="button"
                                                class="gmrc-cunning-action-button"
                                                data-cunning-declare="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningAction['key']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-cunning-label="<?php echo esc_attr(
                                                    (string) (
                                                        $cunningAction['label']
                                                        ?? 'Cunning Action'
                                                    )
                                                ); ?>"
                                                <?php echo
                                                    ! $cunningUnlocked
                                                        ? 'disabled'
                                                        : ''; ?>
                                            >
                                                Use <?php echo esc_html(
                                                    (string) (
                                                        $cunningAction['label']
                                                        ?? 'Action'
                                                    )
                                                ); ?>
                                            </button>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>

                            <p
                                class="gmrc-cunning-actions__status"
                                data-cunning-status
                                role="status"
                                aria-live="polite"
                            >
                                Choose a Cunning Action when it is this Rogue’s turn.
                            </p>
                        </section>
                    <?php endif; ?>

                    <?php
                    $precisionReactions = is_array(
                        $cunningRegister[
                            'precision_reactions'
                        ]
                        ?? null
                    )
                        ? $cunningRegister[
                            'precision_reactions'
                        ]
                        : [];

                    $sneakAttack = is_array(
                        $precisionReactions[
                            'sneak_attack'
                        ]
                        ?? null
                    )
                        ? $precisionReactions[
                            'sneak_attack'
                        ]
                        : [];

                    $uncannyDodge = is_array(
                        $precisionReactions[
                            'uncanny_dodge'
                        ]
                        ?? null
                    )
                        ? $precisionReactions[
                            'uncanny_dodge'
                        ]
                        : [];

                    $evasion = is_array(
                        $precisionReactions[
                            'evasion'
                        ]
                        ?? null
                    )
                        ? $precisionReactions[
                            'evasion'
                        ]
                        : [];
                    ?>

                    <?php if (
                        ! empty(
                            $precisionReactions[
                                'supported'
                            ]
                        )
                    ) : ?>
                        <section
                            class="gmrc-rogue-precision"
                            aria-labelledby="gmrc-rogue-precision-title"
                            data-rogue-precision
                        >
                            <header>
                                <div>
                                    <p class="gmrc-eyebrow">
                                        Precision & Reactions
                                    </p>
                                    <h4 id="gmrc-rogue-precision-title">
                                        Rogue Turn Record
                                    </h4>
                                </div>
                                <button
                                    type="button"
                                    class="gmrc-rogue-turn-reset"
                                    data-rogue-new-turn
                                >
                                    Start New Turn
                                </button>
                            </header>

                            <div class="gmrc-rogue-precision__grid">
                                <article class="gmrc-rogue-precision__sneak">
                                    <div>
                                        <small>
                                            Once per turn · Certified
                                        </small>
                                        <h5>
                                            Sneak Attack
                                            <?php echo esc_html(
                                                (string) (
                                                    $sneakAttack[
                                                        'dice'
                                                    ]
                                                    ?? '1d6'
                                                )
                                            ); ?>
                                        </h5>
                                    </div>

                                    <p>
                                        Apply this precision damage only
                                        after a qualifying attack hits.
                                    </p>

                                    <?php if (
                                        ! empty(
                                            $sneakAttack[
                                                'qualification'
                                            ]
                                        )
                                    ) : ?>
                                        <details>
                                            <summary>
                                                Check qualification guidance
                                            </summary>
                                            <ul>
                                                <?php foreach (
                                                    $sneakAttack[
                                                        'qualification'
                                                    ]
                                                    as $guidance
                                                ) : ?>
                                                    <li>
                                                        <?php echo esc_html(
                                                            (string) $guidance
                                                        ); ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </details>
                                    <?php endif; ?>

                                    <button
                                        type="button"
                                        class="
                                            gmrc-guild-roll-trigger
                                            gmrc-rogue-sneak-roll
                                        "
                                        data-guild-roll="damage"
                                        data-roll-kind="damage"
                                        data-roll-source="Sneak Attack"
                                        data-roll-ability=""
                                        data-roll-proficiency="none"
                                        data-roll-label="<?php echo esc_attr(
                                            (string) (
                                                $sneakAttack[
                                                    'damage_roll'
                                                ]['label']
                                                ?? 'Sneak Attack — Precision Damage'
                                            )
                                        ); ?>"
                                        data-roll-formula="<?php echo esc_attr(
                                            (string) (
                                                $sneakAttack[
                                                    'damage_roll'
                                                ]['formula']
                                                ?? '1d6'
                                            )
                                        ); ?>"
                                        data-roll-modifier="0"
                                        data-roll-result-suffix="<?php echo esc_attr(
                                            (string) (
                                                $sneakAttack[
                                                    'damage_roll'
                                                ]['result_suffix']
                                                ?? 'Sneak Attack damage'
                                            )
                                        ); ?>"
                                        data-sneak-attack-roll
                                    >
                                        <span aria-hidden="true">🎲</span>
                                        Roll Sneak Attack
                                    </button>

                                    <button
                                        type="button"
                                        class="gmrc-rogue-mark-button"
                                        data-sneak-attack-used
                                    >
                                        Mark Sneak Attack Used
                                    </button>
                                </article>

                                <article
                                    class="<?php echo esc_attr(
                                        ! empty(
                                            $uncannyDodge[
                                                'unlocked'
                                            ]
                                        )
                                            ? 'is-unlocked'
                                            : 'is-locked'
                                    ); ?>"
                                >
                                    <div>
                                        <small>
                                            <?php echo esc_html(
                                                ! empty(
                                                    $uncannyDodge[
                                                        'unlocked'
                                                    ]
                                                )
                                                    ? 'Level 5 · Reaction'
                                                    : 'Unlocks at Level 5'
                                            ); ?>
                                        </small>
                                        <h5>Uncanny Dodge</h5>
                                    </div>

                                    <p>
                                        <?php echo esc_html(
                                            (string) (
                                                $uncannyDodge[
                                                    'summary'
                                                ]
                                                ?? ''
                                            )
                                        ); ?>
                                    </p>

                                    <small>
                                        <?php echo esc_html(
                                            (string) (
                                                $uncannyDodge[
                                                    'guidance'
                                                ]
                                                ?? ''
                                            )
                                        ); ?>
                                    </small>

                                    <button
                                        type="button"
                                        class="gmrc-rogue-mark-button"
                                        data-uncanny-dodge-used
                                        <?php echo
                                            empty(
                                                $uncannyDodge[
                                                    'unlocked'
                                                ]
                                            )
                                                ? 'disabled'
                                                : ''; ?>
                                    >
                                        Declare Uncanny Dodge
                                    </button>
                                </article>

                                <article
                                    class="<?php echo esc_attr(
                                        ! empty(
                                            $evasion[
                                                'unlocked'
                                            ]
                                        )
                                            ? 'is-unlocked'
                                            : 'is-locked'
                                    ); ?>"
                                >
                                    <div>
                                        <small>
                                            <?php echo esc_html(
                                                ! empty(
                                                    $evasion[
                                                        'unlocked'
                                                    ]
                                                )
                                                    ? 'Level 7 · Passive'
                                                    : 'Unlocks at Level 7'
                                            ); ?>
                                        </small>
                                        <h5>Evasion</h5>
                                    </div>

                                    <p>
                                        <?php echo esc_html(
                                            (string) (
                                                $evasion[
                                                    'summary'
                                                ]
                                                ?? ''
                                            )
                                        ); ?>
                                    </p>

                                    <small>
                                        <?php echo esc_html(
                                            (string) (
                                                $evasion[
                                                    'guidance'
                                                ]
                                                ?? ''
                                            )
                                        ); ?>
                                    </small>
                                </article>
                            </div>

                            <p
                                class="gmrc-rogue-precision__status"
                                data-rogue-precision-status
                                role="status"
                                aria-live="polite"
                            >
                                Sneak Attack is ready for this turn.
                            </p>
                        </section>
                    <?php endif; ?>

                    <div class="gmrc-cunning-register__features">
                        <?php foreach (
                            ($cunningRegister['features'] ?? [])
                            as $feature
                        ) : ?>
                            <article
                                class="<?php echo esc_attr(
                                    ! empty(
                                        $feature['unlocked']
                                    )
                                        ? 'is-unlocked'
                                        : 'is-locked'
                                ); ?>"
                            >
                                <span aria-hidden="true">
                                    <?php echo
                                        ! empty(
                                            $feature['unlocked']
                                        )
                                            ? '◆'
                                            : '◇'; ?>
                                </span>
                                <div>
                                    <small>
                                        Level <?php echo esc_html(
                                            (string) (
                                                $feature['level']
                                                ?? ''
                                            )
                                        ); ?>
                                        ·
                                        <?php echo esc_html(
                                            ! empty(
                                                $feature['unlocked']
                                            )
                                                ? 'Certified'
                                                : 'Locked'
                                        ); ?>
                                    </small>
                                    <strong>
                                        <?php echo esc_html(
                                            (string) (
                                                $feature['label']
                                                ?? ''
                                            )
                                        ); ?>
                                    </strong>
                                    <p>
                                        <?php echo esc_html(
                                            (string) (
                                                $feature['detail']
                                                ?? ''
                                            )
                                        ); ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (
                        ! empty(
                            $cunningRegister[
                                'archetype'
                            ]['gifts']
                        )
                    ) : ?>
                        <div class="gmrc-cunning-register__path-gifts">
                            <div>
                                <small>Specialist Folio</small>
                                <h4>Certified Rogue Archetype Gifts</h4>
                            </div>

                            <div class="gmrc-cunning-register__gift-grid">
                                <?php foreach (
                                    $cunningRegister[
                                        'archetype'
                                    ]['gifts']
                                    as $gift
                                ) : ?>
                                    <article>
                                        <small>
                                            Level <?php echo esc_html(
                                                (string) (
                                                    $gift['level']
                                                    ?? ''
                                                )
                                            ); ?>
                                            · Certified
                                        </small>
                                        <strong>
                                            <?php echo esc_html(
                                                (string) (
                                                    $gift['label']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </strong>
                                        <p>
                                            <?php echo esc_html(
                                                (string) (
                                                    $gift['summary']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </p>
                                        <?php if (
                                            ! empty(
                                                $gift['detail']
                                            )
                                        ) : ?>
                                            <small>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $gift['detail']
                                                    )
                                                ); ?>
                                            </small>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="gmrc-cunning-register__footer">
                        <div>
                            <span>Rogue Archetype</span>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $cunningRegister[
                                            'archetype'
                                        ]['label']
                                        ?? 'Awaiting Rogue Archetype'
                                    )
                                ); ?>
                            </strong>
                        </div>

                        <?php if (
                            is_array(
                                $cunningRegister[
                                    'next_milestone'
                                ] ?? null
                            )
                        ) : ?>
                            <div>
                                <span>Next cunning milestone</span>
                                <strong>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'next_milestone'
                                            ]['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'next_milestone'
                                            ]['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>
                                <small>
                                    <?php echo esc_html(
                                        (string) (
                                            $cunningRegister[
                                                'next_milestone'
                                            ]['detail']
                                            ?? ''
                                        )
                                    ); ?>
                                </small>
                            </div>
                        <?php else : ?>
                            <div>
                                <span>Rogue progression</span>
                                <strong>Mastered to Level 20</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (
                ! empty($rageRegister['supported'])
            ) : ?>
                <section
                    class="gmrc-rage-register"
                    aria-labelledby="gmrc-rage-register-title"
                    data-rage-register
                >
                    <header class="gmrc-rage-register__heading">
                        <div>
                            <p class="gmrc-eyebrow">
                                Barbarian Field Record
                            </p>
                            <h3 id="gmrc-rage-register-title">
                                The Rage Register
                            </h3>
                            <p>
                                Certified primal capability at Barbarian
                                Level <?php echo esc_html(
                                    (string) (
                                        $rageRegister['level']
                                        ?? $level
                                    )
                                ); ?>.
                            </p>
                        </div>

                        <span
                            class="gmrc-rage-register__badge"
                            aria-label="<?php echo esc_attr(
                                ! empty(
                                    $rageRegister[
                                        'rage'
                                    ]['unlimited']
                                )
                                    ? 'Unlimited Rages'
                                    : sprintf(
                                        '%d of %d Rages remaining',
                                        (int) (
                                            $rageRegister[
                                                'rage'
                                            ]['remaining']
                                            ?? 0
                                        ),
                                        (int) (
                                            $rageRegister[
                                                'rage'
                                            ]['maximum']
                                            ?? 0
                                        )
                                    )
                            ); ?>"
                        >
                            <strong>
                                <?php echo esc_html(
                                    ! empty(
                                        $rageRegister[
                                            'rage'
                                        ]['unlimited']
                                    )
                                        ? '∞'
                                        : sprintf(
                                            '%d/%d',
                                            (int) (
                                                $rageRegister[
                                                    'rage'
                                                ]['remaining']
                                                ?? 0
                                            ),
                                            (int) (
                                                $rageRegister[
                                                    'rage'
                                                ]['maximum']
                                                ?? 0
                                            )
                                        )
                                ); ?>
                            </strong>
                            <small>Rages remaining</small>
                        </span>
                    </header>

                    <div class="gmrc-rage-register__rage">
                        <article>
                            <span aria-hidden="true">🔥</span>
                            <div>
                                <small>Primal State</small>
                                <h4>Rage</h4>
                                <p>
                                    Bonus action ·
                                    <?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'rage'
                                            ]['duration']
                                            ?? '1 minute'
                                        )
                                    ); ?>
                                    · refreshes on a
                                    <?php echo esc_html(
                                        strtolower(
                                            (string) (
                                                $rageRegister[
                                                    'rage'
                                                ]['refresh']
                                                ?? 'Long rest'
                                            )
                                        )
                                    ); ?>.
                                </p>
                            </div>
                        </article>

                        <dl>
                            <div>
                                <dt>Rage Damage</dt>
                                <dd>
                                    +<?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'rage'
                                            ]['damage_bonus']
                                            ?? 2
                                        )
                                    ); ?>
                                </dd>
                            </div>
                            <div>
                                <dt>Attack action</dt>
                                <dd>
                                    <?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'attacks_per_action'
                                            ] ?? 1
                                        )
                                    ); ?>
                                </dd>
                            </div>
                            <div>
                                <dt>Fast Movement</dt>
                                <dd>
                                    <?php echo esc_html(
                                        (int) (
                                            $rageRegister[
                                                'speed_bonus'
                                            ] ?? 0
                                        ) > 0
                                            ? '+'
                                                . (string) (
                                                    $rageRegister[
                                                        'speed_bonus'
                                                    ]
                                                )
                                                . ' ft'
                                            : 'Not yet'
                                    ); ?>
                                </dd>
                            </div>
                            <div>
                                <dt>Brutal Critical</dt>
                                <dd>
                                    <?php echo esc_html(
                                        (int) (
                                            $rageRegister[
                                                'brutal_critical_dice'
                                            ] ?? 0
                                        ) > 0
                                            ? '+'
                                                . (string) (
                                                    $rageRegister[
                                                        'brutal_critical_dice'
                                                    ]
                                                )
                                                . ' die'
                                            : 'Not yet'
                                    ); ?>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <section
                        class="gmrc-rage-register__controls <?php echo
                            ! empty(
                                $rageRegister[
                                    'rage'
                                ]['active']
                            )
                                ? 'is-raging'
                                : ''; ?>"
                        aria-labelledby="gmrc-rage-reserves-title"
                        data-rage-active="<?php echo
                            ! empty(
                                $rageRegister[
                                    'rage'
                                ]['active']
                            )
                                ? 'true'
                                : 'false'; ?>"
                    >
                        <div>
                            <p class="gmrc-eyebrow">
                                Active Play
                            </p>
                            <h4 id="gmrc-rage-reserves-title">
                                Rage Reserves
                            </h4>
                            <p>
                                <?php if (
                                    ! empty(
                                        $rageRegister[
                                            'rage'
                                        ]['active']
                                    )
                                ) : ?>
                                    🔥 <strong>RAGING</strong> —
                                    Rage damage is currently
                                    +<?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'rage'
                                            ]['damage_bonus']
                                            ?? 2
                                        )
                                    ); ?>.
                                <?php else : ?>
                                    Rage is currently dormant.
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="gmrc-rage-register__actions">
                            <?php if (
                                ! empty(
                                    $rageRegister[
                                        'rage'
                                    ]['active']
                                )
                            ) : ?>
                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                >
                                    <input type="hidden" name="action" value="gmrc_app_request">
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/rage/end'
                                        ); ?>"
                                    >
                                    <?php wp_nonce_field(
                                        'gmrc_character_rage_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>
                                    <button
                                        type="submit"
                                        class="gmrc-rage-action-button gmrc-rage-action-button--quiet"
                                    >
                                        End Rage
                                    </button>
                                </form>
                            <?php else : ?>
                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                >
                                    <input type="hidden" name="action" value="gmrc_app_request">
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/rage/enter'
                                        ); ?>"
                                    >
                                    <?php wp_nonce_field(
                                        'gmrc_character_rage_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>
                                    <button
                                        type="submit"
                                        class="gmrc-rage-action-button"
                                        <?php echo (
                                            empty(
                                                $rageRegister[
                                                    'rage'
                                                ]['unlimited']
                                            )
                                            && (int) (
                                                $rageRegister[
                                                    'rage'
                                                ]['remaining']
                                                ?? 0
                                            ) < 1
                                        )
                                            ? 'disabled'
                                            : ''; ?>
                                    >
                                        🔥 Enter Rage
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form
                                action="<?php echo esc_url(
                                    $appRequestUrl
                                ); ?>"
                                method="post"
                            >
                                <input type="hidden" name="action" value="gmrc_app_request">
                                <input
                                    type="hidden"
                                    name="gmrc_route"
                                    value="<?php echo esc_attr(
                                        'characters/'
                                        . $characterId
                                        . '/rage/rest'
                                    ); ?>"
                                >
                                <?php wp_nonce_field(
                                    'gmrc_character_rage_'
                                    . $characterId,
                                    'gmrc_nonce'
                                ); ?>
                                <button
                                    type="submit"
                                    class="gmrc-rage-action-button gmrc-rage-action-button--rest"
                                >
                                    Take Long Rest
                                </button>
                            </form>
                        </div>
                    </section>

                    <?php if (
                        ! empty($rageRegister['actions'])
                        && is_array($rageRegister['actions'])
                    ) : ?>
                        <section
                            class="gmrc-primal-actions"
                            aria-labelledby="gmrc-primal-actions-title"
                        >
                            <header>
                                <p class="gmrc-eyebrow">
                                    Primal Actions
                                </p>
                                <h4 id="gmrc-primal-actions-title">
                                    Barbarian Battle Actions
                                </h4>
                                <p>
                                    Use certified Barbarian abilities without
                                    inventing rolls where none belong.
                                </p>
                            </header>

                            <div class="gmrc-primal-actions__grid">
                                <?php foreach (
                                    $rageRegister['actions']
                                    as $primalAction
                                ) : ?>
                                    <?php
                                    $primalRoll = is_array(
                                        $primalAction['roll']
                                        ?? null
                                    )
                                        ? $primalAction['roll']
                                        : null;

                                    $primalUnlocked = ! empty(
                                        $primalAction['unlocked']
                                    );

                                    $primalAvailable = ! empty(
                                        $primalAction['available']
                                    );
                                    ?>
                                    <article
                                        class="<?php echo esc_attr(
                                            $primalUnlocked
                                                ? (
                                                    $primalAvailable
                                                        ? 'is-available'
                                                        : 'is-unavailable'
                                                )
                                                : 'is-locked'
                                        ); ?>"
                                    >
                                        <div class="gmrc-primal-actions__title">
                                            <span aria-hidden="true">
                                                <?php echo
                                                    $primalUnlocked
                                                        ? '◆'
                                                        : '◇'; ?>
                                            </span>
                                            <div>
                                                <small>
                                                    Level <?php echo esc_html(
                                                        (string) (
                                                            $primalAction[
                                                                'level'
                                                            ]
                                                            ?? ''
                                                        )
                                                    ); ?>
                                                    ·
                                                    <?php echo esc_html(
                                                        $primalUnlocked
                                                            ? 'Certified'
                                                            : 'Locked'
                                                    ); ?>
                                                </small>
                                                <strong>
                                                    <?php echo esc_html(
                                                        (string) (
                                                            $primalAction[
                                                                'label'
                                                            ]
                                                            ?? ''
                                                        )
                                                    ); ?>
                                                </strong>
                                            </div>

                                            <?php if (
                                                ! empty(
                                                    $primalAction[
                                                        'badge'
                                                    ]
                                                )
                                            ) : ?>
                                                <span class="gmrc-primal-actions__badge">
                                                    <?php echo esc_html(
                                                        (string) $primalAction[
                                                            'badge'
                                                        ]
                                                    ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <p>
                                            <?php echo esc_html(
                                                (string) (
                                                    $primalAction['detail']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </p>

                                        <?php if (
                                            is_array($primalRoll)
                                            && $primalUnlocked
                                        ) : ?>
                                            <button
                                                type="button"
                                                class="
                                                    gmrc-guild-roll-trigger
                                                    gmrc-primal-action-roll
                                                "
                                                data-guild-roll="d20"
                                                data-roll-kind="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll['kind']
                                                        ?? 'check'
                                                    )
                                                ); ?>"
                                                data-roll-source="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll['source']
                                                        ?? 'Primal Action'
                                                    )
                                                ); ?>"
                                                data-roll-ability="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll['ability']
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-roll-proficiency="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll[
                                                            'proficiency'
                                                        ]
                                                        ?? 'none'
                                                    )
                                                ); ?>"
                                                data-roll-label="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll['label']
                                                        ?? 'Primal Action'
                                                    )
                                                ); ?>"
                                                data-roll-modifier="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll['modifier']
                                                        ?? 0
                                                    )
                                                ); ?>"
                                                data-roll-result-suffix="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll[
                                                            'result_suffix'
                                                        ]
                                                        ?? ''
                                                    )
                                                ); ?>"
                                                data-roll-default-mode="<?php echo esc_attr(
                                                    (string) (
                                                        $primalRoll[
                                                            'default_mode'
                                                        ]
                                                        ?? 'normal'
                                                    )
                                                ); ?>"
                                                <?php echo
                                                    ! $primalAvailable
                                                        ? 'disabled'
                                                        : ''; ?>
                                            >
                                                <span aria-hidden="true">🎲</span>
                                                Roll <?php echo esc_html(
                                                    (string) (
                                                        $primalAction['label']
                                                        ?? 'Primal Action'
                                                    )
                                                ); ?>
                                            </button>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="gmrc-rage-register__features">
                        <?php foreach (
                            ($rageRegister['features'] ?? [])
                            as $feature
                        ) : ?>
                            <article
                                class="<?php echo esc_attr(
                                    ! empty(
                                        $feature['unlocked']
                                    )
                                        ? 'is-unlocked'
                                        : 'is-locked'
                                ); ?>"
                            >
                                <span aria-hidden="true">
                                    <?php echo
                                        ! empty(
                                            $feature['unlocked']
                                        )
                                            ? '◆'
                                            : '◇'; ?>
                                </span>
                                <div>
                                    <small>
                                        Level <?php echo esc_html(
                                            (string) (
                                                $feature['level']
                                                ?? ''
                                            )
                                        ); ?>
                                        ·
                                        <?php echo esc_html(
                                            ! empty(
                                                $feature['unlocked']
                                            )
                                                ? 'Certified'
                                                : 'Locked'
                                        ); ?>
                                    </small>
                                    <strong>
                                        <?php echo esc_html(
                                            (string) (
                                                $feature['label']
                                                ?? ''
                                            )
                                        ); ?>
                                    </strong>
                                    <p>
                                        <?php echo esc_html(
                                            (string) (
                                                $feature['detail']
                                                ?? ''
                                            )
                                        ); ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (
                        ! empty($rageRegister['path_gifts'])
                        && is_array($rageRegister['path_gifts'])
                    ) : ?>
                        <section class="gmrc-rage-register__path-gifts">
                            <header>
                                <p class="gmrc-eyebrow">
                                    Certified Primal Path Gifts
                                </p>
                                <h4>
                                    <?php echo esc_html(
                                        (string) (
                                            $rageRegister['path']['label']
                                            ?? 'Primal Path'
                                        )
                                    ); ?>
                                </h4>
                            </header>
                            <div>
                                <?php foreach (
                                    $rageRegister['path_gifts']
                                    as $gift
                                ) : ?>
                                    <article>
                                        <span aria-hidden="true">🔥</span>
                                        <div>
                                            <small>
                                                Level <?php echo esc_html(
                                                    (string) ($gift['level'] ?? '')
                                                ); ?> Gift
                                            </small>
                                            <strong>
                                                <?php echo esc_html(
                                                    (string) ($gift['label'] ?? '')
                                                ); ?>
                                            </strong>
                                            <p>
                                                <?php echo esc_html(
                                                    (string) ($gift['summary'] ?? '')
                                                ); ?>
                                            </p>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="gmrc-rage-register__footer">
                        <div>
                            <span>Primal Path</span>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $rageRegister[
                                            'path'
                                        ]['label']
                                        ?? 'Awaiting Primal Path'
                                    )
                                ); ?>
                            </strong>
                        </div>

                        <?php if (
                            is_array(
                                $rageRegister[
                                    'next_milestone'
                                ] ?? null
                            )
                        ) : ?>
                            <div>
                                <span>Next primal milestone</span>
                                <strong>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'next_milestone'
                                            ]['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'next_milestone'
                                            ]['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>
                                <small>
                                    <?php echo esc_html(
                                        (string) (
                                            $rageRegister[
                                                'next_milestone'
                                            ]['detail']
                                            ?? ''
                                        )
                                    ); ?>
                                </small>
                            </div>
                        <?php else : ?>
                            <div>
                                <span>Primal progression</span>
                                <strong>Mastered to Level 20</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (
                ! empty($martialRegister['supported'])
            ) : ?>
                <section
                    class="gmrc-martial-register"
                    aria-labelledby="gmrc-martial-register-title"
                    data-martial-register
                >
                    <header class="gmrc-martial-register__heading">
                        <div>
                            <p class="gmrc-eyebrow">
                                Fighter Field Record
                            </p>
                            <h3 id="gmrc-martial-register-title">
                                The Martial Register
                            </h3>
                            <p>
                                Certified martial capability at Fighter
                                Level <?php echo esc_html(
                                    (string) (
                                        $martialRegister['level']
                                        ?? $level
                                    )
                                ); ?>.
                            </p>
                        </div>

                        <span
                            class="gmrc-martial-register__attacks"
                            aria-label="<?php echo esc_attr(
                                sprintf(
                                    '%d attack%s per Attack action',
                                    (int) (
                                        $martialRegister[
                                            'attacks_per_action'
                                        ] ?? 1
                                    ),
                                    (int) (
                                        $martialRegister[
                                            'attacks_per_action'
                                        ] ?? 1
                                    ) === 1
                                        ? ''
                                        : 's'
                                )
                            ); ?>"
                        >
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $martialRegister[
                                            'attacks_per_action'
                                        ] ?? 1
                                    )
                                ); ?>
                            </strong>
                            <small>Attack action</small>
                        </span>
                    </header>

                    <div class="gmrc-martial-register__resources">
                        <?php foreach (
                            ($martialRegister['resources'] ?? [])
                            as $resource
                        ) : ?>
                            <article
                                class="gmrc-martial-resource<?php echo
                                    ! empty($resource['unlocked'])
                                        ? ' is-unlocked'
                                        : ' is-locked'; ?>"
                            >
                                <header>
                                    <span aria-hidden="true">
                                        <?php echo
                                            ! empty($resource['unlocked'])
                                                ? '⚔'
                                                : '◇'; ?>
                                    </span>
                                    <div>
                                        <small>
                                            <?php echo esc_html(
                                                ! empty(
                                                    $resource['unlocked']
                                                )
                                                    ? 'Certified'
                                                    : 'Not yet unlocked'
                                            ); ?>
                                        </small>
                                        <h4>
                                            <?php echo esc_html(
                                                (string) (
                                                    $resource['label']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </h4>
                                    </div>
                                </header>

                                <?php if (
                                    ! empty($resource['unlocked'])
                                ) : ?>
                                    <dl>
                                        <div>
                                            <dt>Remaining</dt>
                                            <dd>
                                                <?php echo esc_html(
                                                    sprintf(
                                                        '%d / %d',
                                                        (int) (
                                                            $resource[
                                                                'remaining'
                                                            ] ?? 0
                                                        ),
                                                        (int) (
                                                            $resource[
                                                                'maximum'
                                                            ] ?? 0
                                                        )
                                                    )
                                                ); ?>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Refresh</dt>
                                            <dd>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $resource['refresh']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Use</dt>
                                            <dd>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $resource['activation']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </dd>
                                        </div>
                                    </dl>

                                    <p>
                                        <?php echo esc_html(
                                            (string) (
                                                $resource['effect']
                                                ?? ''
                                            )
                                        ); ?>
                                    </p>

                                    <?php
                                    $martialAction = is_array(
                                        $resource['action']
                                        ?? null
                                    )
                                        ? $resource['action']
                                        : [];

                                    $martialRoll = is_array(
                                        $martialAction['roll']
                                        ?? null
                                    )
                                        ? $martialAction['roll']
                                        : null;

                                    $martialRerolls = is_array(
                                        $martialAction['save_rerolls']
                                        ?? null
                                    )
                                        ? $martialAction['save_rerolls']
                                        : [];
                                    ?>

                                    <?php if (
                                        is_array($martialRoll)
                                    ) : ?>
                                        <button
                                            type="button"
                                            class="
                                                gmrc-guild-roll-trigger
                                                gmrc-martial-action-roll
                                            "
                                            data-guild-roll="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll['kind']
                                                    ?? 'healing'
                                                )
                                            ); ?>"
                                            data-roll-kind="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll['kind']
                                                    ?? 'healing'
                                                )
                                            ); ?>"
                                            data-roll-source="<?php echo esc_attr(
                                                (string) (
                                                    $resource['label']
                                                    ?? 'Martial Action'
                                                )
                                            ); ?>"
                                            data-roll-ability=""
                                            data-roll-proficiency="none"
                                            data-roll-label="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll['label']
                                                    ?? 'Martial Action'
                                                )
                                            ); ?>"
                                            data-roll-formula="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll['formula']
                                                    ?? ''
                                                )
                                            ); ?>"
                                            data-roll-modifier="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll['modifier']
                                                    ?? 0
                                                )
                                            ); ?>"
                                            data-roll-result-suffix="<?php echo esc_attr(
                                                (string) (
                                                    $martialRoll[
                                                        'result_suffix'
                                                    ]
                                                    ?? ''
                                                )
                                            ); ?>"
                                            <?php echo (
                                                (int) (
                                                    $resource['remaining']
                                                    ?? 0
                                                ) < 1
                                            )
                                                ? 'disabled'
                                                : ''; ?>
                                        >
                                            <span aria-hidden="true">✚</span>
                                            Roll <?php echo esc_html(
                                                (string) (
                                                    $resource['label']
                                                    ?? 'Action'
                                                )
                                            ); ?>
                                        </button>
                                    <?php endif; ?>

                                    <?php if (
                                        $martialRerolls !== []
                                    ) : ?>
                                        <div
                                            class="gmrc-martial-save-rerolls"
                                            aria-label="Indomitable saving throw rerolls"
                                        >
                                            <span>Reroll failed save</span>

                                            <div>
                                                <?php foreach (
                                                    $martialRerolls
                                                    as $reroll
                                                ) : ?>
                                                    <button
                                                        type="button"
                                                        class="
                                                            gmrc-guild-roll-trigger
                                                            gmrc-martial-save-reroll
                                                        "
                                                        data-guild-roll="d20"
                                                        data-roll-kind="saving-throw"
                                                        data-roll-source="Indomitable"
                                                        data-roll-ability="<?php echo esc_attr(
                                                            (string) (
                                                                $reroll[
                                                                    'ability'
                                                                ]
                                                                ?? ''
                                                            )
                                                        ); ?>"
                                                        data-roll-proficiency="<?php echo esc_attr(
                                                            ! empty(
                                                                $reroll[
                                                                    'proficient'
                                                                ]
                                                            )
                                                                ? 'proficient'
                                                                : 'none'
                                                        ); ?>"
                                                        data-roll-label="<?php echo esc_attr(
                                                            sprintf(
                                                                'Indomitable — %s Saving Throw',
                                                                (string) (
                                                                    $reroll[
                                                                        'label'
                                                                    ]
                                                                    ?? ''
                                                                )
                                                            )
                                                        ); ?>"
                                                        data-roll-modifier="<?php echo esc_attr(
                                                            (string) (
                                                                $reroll[
                                                                    'modifier'
                                                                ]
                                                                ?? 0
                                                            )
                                                        ); ?>"
                                                        data-roll-result-suffix="saving throw"
                                                        <?php echo (
                                                            (int) (
                                                                $resource[
                                                                    'remaining'
                                                                ]
                                                                ?? 0
                                                            ) < 1
                                                        )
                                                            ? 'disabled'
                                                            : ''; ?>
                                                    >
                                                        <?php echo esc_html(
                                                            (string) (
                                                                $reroll[
                                                                    'label'
                                                                ]
                                                                ?? ''
                                                            )
                                                        ); ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (
                                        ! empty(
                                            $martialAction['note']
                                        )
                                    ) : ?>
                                        <p class="gmrc-martial-action-note">
                                            <?php echo esc_html(
                                                (string) (
                                                    $martialAction['note']
                                                    ?? ''
                                                )
                                            ); ?>
                                        </p>
                                    <?php endif; ?>

                                    <form
                                        class="gmrc-martial-resource__spend"
                                        action="<?php echo esc_url(
                                            $appRequestUrl
                                        ); ?>"
                                        method="post"
                                    >
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="gmrc_app_request"
                                        >
                                        <input
                                            type="hidden"
                                            name="gmrc_route"
                                            value="<?php echo esc_attr(
                                                'characters/'
                                                . $characterId
                                                . '/resources/spend'
                                            ); ?>"
                                        >
                                        <input
                                            type="hidden"
                                            name="resource"
                                            value="<?php echo esc_attr(
                                                (string) (
                                                    $resource['key']
                                                    ?? ''
                                                )
                                            ); ?>"
                                        >

                                        <?php wp_nonce_field(
                                            'gmrc_character_resources_'
                                            . $characterId,
                                            'gmrc_nonce'
                                        ); ?>

                                        <button
                                            type="submit"
                                            class="gmrc-martial-resource__button"
                                            <?php echo (
                                                (int) (
                                                    $resource['remaining']
                                                    ?? 0
                                                ) < 1
                                            )
                                                ? 'disabled'
                                                : ''; ?>
                                        >
                                            <?php echo esc_html(
                                                (int) (
                                                    $resource['remaining']
                                                    ?? 0
                                                ) < 1
                                                    ? 'Reserve Spent'
                                                    : (
                                                        (string) (
                                                            $martialAction[
                                                                'button_label'
                                                            ]
                                                            ?? 'Spend 1 Use'
                                                        )
                                                    )
                                            ); ?>
                                        </button>
                                    </form>
                                <?php else : ?>
                                    <p>
                                        This entry will illuminate when the
                                        Fighter reaches its certified level.
                                    </p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <section
                        class="gmrc-martial-register__rests"
                        aria-labelledby="gmrc-battle-reserves-rest-title"
                    >
                        <div>
                            <p class="gmrc-eyebrow">
                                Active Play
                            </p>
                            <h4 id="gmrc-battle-reserves-rest-title">
                                Battle Reserve Refresh
                            </h4>
                            <p>
                                A short rest restores Second Wind and
                                Action Surge. A long rest restores every
                                Fighter Battle Reserve.
                            </p>
                        </div>

                        <div class="gmrc-martial-register__rest-actions">
                            <?php foreach ([
                                'short' => 'Take Short Rest',
                                'long' => 'Take Long Rest',
                            ] as $restKey => $restLabel) : ?>
                                <form
                                    action="<?php echo esc_url(
                                        $appRequestUrl
                                    ); ?>"
                                    method="post"
                                >
                                    <input
                                        type="hidden"
                                        name="action"
                                        value="gmrc_app_request"
                                    >
                                    <input
                                        type="hidden"
                                        name="gmrc_route"
                                        value="<?php echo esc_attr(
                                            'characters/'
                                            . $characterId
                                            . '/resources/refresh'
                                        ); ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="rest"
                                        value="<?php echo esc_attr(
                                            $restKey
                                        ); ?>"
                                    >

                                    <?php wp_nonce_field(
                                        'gmrc_character_resources_'
                                        . $characterId,
                                        'gmrc_nonce'
                                    ); ?>

                                    <button
                                        type="submit"
                                        class="gmrc-martial-rest-button <?php echo
                                            $restKey === 'long'
                                                ? 'gmrc-martial-rest-button--quiet'
                                                : ''; ?>"
                                    >
                                        <?php echo esc_html(
                                            $restLabel
                                        ); ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <?php if (
                        ! empty(
                            $martialRegister[
                                'path'
                            ]['gifts']
                        )
                        && is_array(
                            $martialRegister[
                                'path'
                            ]['gifts']
                        )
                    ) : ?>
                        <section
                            class="gmrc-martial-register__path-gifts"
                            aria-labelledby="gmrc-martial-path-gifts-title"
                        >
                            <header>
                                <p class="gmrc-eyebrow">
                                    Certified Martial Path Gifts
                                </p>
                                <h4 id="gmrc-martial-path-gifts-title">
                                    <?php echo esc_html(
                                        (string) (
                                            $martialRegister[
                                                'path'
                                            ]['label']
                                            ?? 'Martial Path'
                                        )
                                    ); ?>
                                </h4>
                            </header>

                            <div>
                                <?php foreach (
                                    $martialRegister[
                                        'path'
                                    ]['gifts']
                                    as $gift
                                ) : ?>
                                    <article>
                                        <span aria-hidden="true">✦</span>
                                        <div>
                                            <small>
                                                Level <?php echo esc_html(
                                                    (string) (
                                                        $gift['level']
                                                        ?? ''
                                                    )
                                                ); ?> Gift
                                            </small>
                                            <strong>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $gift['label']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </strong>
                                            <p>
                                                <?php echo esc_html(
                                                    (string) (
                                                        $gift['summary']
                                                        ?? ''
                                                    )
                                                ); ?>
                                            </p>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="gmrc-martial-register__footer">
                        <div>
                            <span>Martial Path</span>
                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $martialRegister[
                                            'path'
                                        ]['label']
                                        ?? 'Awaiting Martial Path'
                                    )
                                ); ?>
                            </strong>
                        </div>

                        <?php if (
                            is_array(
                                $martialRegister[
                                    'next_milestone'
                                ] ?? null
                            )
                        ) : ?>
                            <div>
                                <span>Next martial milestone</span>
                                <strong>
                                    Level <?php echo esc_html(
                                        (string) (
                                            $martialRegister[
                                                'next_milestone'
                                            ]['level']
                                            ?? ''
                                        )
                                    ); ?>
                                    ·
                                    <?php echo esc_html(
                                        (string) (
                                            $martialRegister[
                                                'next_milestone'
                                            ]['label']
                                            ?? ''
                                        )
                                    ); ?>
                                </strong>
                                <small>
                                    <?php echo esc_html(
                                        (string) (
                                            $martialRegister[
                                                'next_milestone'
                                            ]['detail']
                                            ?? ''
                                        )
                                    ); ?>
                                </small>
                            </div>
                        <?php else : ?>
                            <div>
                                <span>Martial progression</span>
                                <strong>Mastered to Level 20</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

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
                                    sprintf(
                                        '%d/%d',
                                        (int) (
                                            $slot['remaining']
                                            ?? $slot['total']
                                            ?? 0
                                        ),
                                        (int) (
                                            $slot['total']
                                            ?? 0
                                        )
                                    )
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

            <?php if ($arcana['entries'] === []) : ?>
                <div class="gmrc-arcane-card-list">
                    <article class="gmrc-arcane-empty">
                        <span aria-hidden="true">📜</span>
                        <h3>Pages Awaiting Discovery</h3>
                        <p>
                            No spells or class abilities have been entered for this calling yet.
                            The Guild Archivists have left plenty of room.
                        </p>
                    </article>
                </div>
            <?php else : ?>
                <div
                    class="gmrc-arcane-index"
                    data-arcane-index
                >
                    <div
                        class="gmrc-arcane-tabs"
                        role="tablist"
                        aria-label="Arcane Pantry shelves"
                    >
                        <?php foreach ($arcana['shelves'] as $shelfIndex => $shelf) : ?>
                            <?php
                            $shelfKey = sanitize_key(
                                (string) $shelf['key']
                            );
                            $tabId = 'gmrc-arcane-tab-' . $shelfKey;
                            $panelId = 'gmrc-arcane-panel-' . $shelfKey;
                            $selected = $shelfIndex === 0;
                            ?>
                            <button
                                id="<?php echo esc_attr($tabId); ?>"
                                type="button"
                                class="gmrc-arcane-tabs__tab<?php echo $selected ? ' is-active' : ''; ?>"
                                role="tab"
                                aria-selected="<?php echo $selected ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr($panelId); ?>"
                                tabindex="<?php echo $selected ? '0' : '-1'; ?>"
                                data-arcane-tab="<?php echo esc_attr($shelfKey); ?>"
                            >
                                <span><?php echo esc_html((string) $shelf['label']); ?></span>
                                <small><?php echo esc_html(
                                    (string) count($shelf['entries'])
                                ); ?></small>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="gmrc-arcane-shelves">
                        <?php foreach ($arcana['shelves'] as $shelfIndex => $shelf) : ?>
                            <?php
                            $shelfKey = sanitize_key(
                                (string) $shelf['key']
                            );
                            $tabId = 'gmrc-arcane-tab-' . $shelfKey;
                            $panelId = 'gmrc-arcane-panel-' . $shelfKey;
                            $selected = $shelfIndex === 0;
                            ?>
                            <section
                                id="<?php echo esc_attr($panelId); ?>"
                                class="gmrc-arcane-shelf<?php echo $selected ? ' is-active' : ''; ?>"
                                role="tabpanel"
                                aria-labelledby="<?php echo esc_attr($tabId); ?>"
                                data-arcane-panel="<?php echo esc_attr($shelfKey); ?>"
                                <?php echo $selected ? '' : 'hidden'; ?>
                            >
                                <header class="gmrc-arcane-shelf__heading">
                                    <p class="gmrc-eyebrow">Indexed Shelf</p>
                                    <h3><?php echo esc_html((string) $shelf['label']); ?></h3>
                                    <span>
                                        <?php echo esc_html(
                                            sprintf(
                                                '%d entr%s',
                                                count($shelf['entries']),
                                                count($shelf['entries']) === 1
                                                    ? 'y'
                                                    : 'ies'
                                            )
                                        ); ?>
                                    </span>
                                </header>

                                <div class="gmrc-arcane-card-list">
                                    <?php foreach ($shelf['entries'] as $ability) : ?>
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

                                            <?php if (
                                                ! empty(
                                                    $ability['roll_scaling']['scalable']
                                                )
                                                && $ability['formula'] !== null
                                            ) : ?>
                                                <p class="gmrc-arcane-scaling">
                                                    <span>Current roll</span>
                                                    <strong><?php echo esc_html(
                                                        (string) $ability['formula']
                                                    ); ?></strong>
                                                    <small>
                                                        <?php echo esc_html(
                                                            ($ability['roll_scaling']['source'] ?? 'base')
                                                                === 'character-level'
                                                                ? 'Scaled by adventurer level'
                                                                : (
                                                                    ($ability['roll_scaling']['slot_options'] ?? [])
                                                                    !== []
                                                                        ? 'Prepared for higher-slot scaling'
                                                                        : 'PHP-resolved scaling'
                                                                )
                                                        ); ?>
                                                    </small>
                                                </p>
                                            <?php endif; ?>

                                            <div class="gmrc-arcane-card__rolls">
                                                <?php if ($ability['spell_attack'] !== null) : ?>
                                                    <button
                                                        type="button"
                                                        class="gmrc-guild-roll-trigger"
                                                        data-guild-roll="d20"
                                                        data-roll-kind="spell-attack"
                                                        data-roll-target-mode="<?php echo esc_attr((string) ($ability['target_mode'] ?? 'none')); ?>"
                                                        data-roll-default-target-kind="<?php echo esc_attr((string) ($ability['default_target_kind'] ?? '')); ?>"
                                                        data-roll-source="<?php echo esc_attr((string) $ability['label']); ?>"
                                                        data-roll-ability="<?php echo esc_attr((string) ($arcana['casting_ability'] ?? '')); ?>"
                                                        data-roll-proficiency="proficient"
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
                                                        data-roll-target-mode="<?php echo esc_attr((string) ($ability['target_mode'] ?? 'none')); ?>"
                                                        data-roll-default-target-kind="<?php echo esc_attr((string) ($ability['default_target_kind'] ?? '')); ?>"
                                                        data-roll-source="<?php echo esc_attr((string) $ability['label']); ?>"
                                                        data-roll-ability="<?php echo esc_attr((string) ($arcana['casting_ability'] ?? '')); ?>"
                                                        data-roll-proficiency="none"
                                                        data-roll-label="<?php echo esc_attr(
                                                            $ability['label']
                                                            . ' — '
                                                            . ucfirst((string) $ability['roll_kind'])
                                                        ); ?>"
                                                        data-roll-formula="<?php echo esc_attr($ability['formula']); ?>"
                                                        data-roll-base-formula="<?php echo esc_attr((string) ($ability['base_formula'] ?? '')); ?>"
                                                        data-roll-scaling-source="<?php echo esc_attr((string) ($ability['roll_scaling']['source'] ?? 'base')); ?>"
                                                        data-roll-scaling-at="<?php echo esc_attr((string) ($ability['roll_scaling']['resolved_at'] ?? '')); ?>"
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
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

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

                <section
                    class="gmrc-ledger-section gmrc-character-fellowships"
                    aria-labelledby="gmrc-character-fellowships-title"
                >
                    <header class="gmrc-ledger-section__heading">
                        <h3 id="gmrc-character-fellowships-title">
                            Fellowships
                        </h3>
                    </header>

                    <?php
                    $characterFellowships = is_array(
                        $fellowships ?? null
                    )
                        ? $fellowships
                        : [];
                    ?>

                    <?php if ($characterFellowships === []) : ?>
                        <div class="gmrc-character-fellowships__empty">
                            <span aria-hidden="true">⚑</span>
                            <div>
                                <strong>No Fellowship recorded yet</strong>
                                <p>
                                    This adventurer is not currently part of
                                    one of your registered Fellowships.
                                </p>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="gmrc-character-fellowships__grid">
                            <?php foreach (
                                $characterFellowships as $fellowship
                            ) : ?>
                                <?php
                                $fellowshipParty =
                                    $fellowship['party'] ?? null;
                                $fellowshipMembership =
                                    $fellowship['membership'] ?? null;

                                if (
                                    ! $fellowshipParty
                                        instanceof \GreatMarketrealmCompanion\Modules\Parties\Models\Party
                                    || ! $fellowshipMembership
                                        instanceof \GreatMarketrealmCompanion\Modules\Parties\Models\PartyMembership
                                ) {
                                    continue;
                                }

                                $fellowshipUrl = add_query_arg(
                                    'gmrc_route',
                                    'parties/'
                                        . rawurlencode(
                                            $fellowshipParty
                                                ->id()
                                                ->value()
                                        ),
                                    home_url('/companion/')
                                );
                                ?>
                                <article
                                    class="gmrc-character-fellowship-card"
                                    data-standard-palette="<?php echo esc_attr(
                                        $fellowshipParty
                                            ->standard()
                                            ->palette()
                                    ); ?>"
                                >
                                    <span
                                        class="gmrc-character-fellowship-card__seal"
                                        aria-hidden="true"
                                    >
                                        <?php echo esc_html(
                                            $fellowshipParty
                                                ->standard()
                                                ->emblemGlyph()
                                        ); ?>
                                    </span>

                                    <div>
                                        <p class="gmrc-eyebrow">
                                            <?php echo esc_html(
                                                $fellowshipMembership
                                                    ->role()
                                                    ->label()
                                            ); ?>
                                            <?php if (
                                                $fellowshipMembership
                                                    ->office()
                                                    ->isAssigned()
                                            ) : ?>
                                                ·
                                                <?php echo esc_html(
                                                    $fellowshipMembership
                                                        ->office()
                                                        ->label()
                                                ); ?>
                                            <?php endif; ?>
                                        </p>

                                        <h4>
                                            <?php echo esc_html(
                                                $fellowshipParty
                                                    ->name()
                                                    ->value()
                                            ); ?>
                                        </h4>

                                        <p>
                                            <?php echo esc_html(
                                                sprintf(
                                                    '%d registered adventurer%s',
                                                    $fellowshipParty
                                                        ->memberCount(),
                                                    $fellowshipParty
                                                        ->memberCount() === 1
                                                        ? ''
                                                        : 's'
                                                )
                                            ); ?>
                                        </p>

                                        <a
                                            class="gmrc-character-fellowship-card__link"
                                            href="<?php echo esc_url(
                                                $fellowshipUrl
                                            ); ?>"
                                        >
                                            Open Fellowship Hall
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
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

    <aside
        class="gmrc-guild-dice-tray"
        data-guild-dice-tray
        role="region"
        aria-labelledby="gmrc-guild-dice-title"
        aria-describedby="gmrc-guild-dice-accessibility-note"
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
        <p
            id="gmrc-guild-dice-accessibility-note"
            class="screen-reader-text"
        >
            Dice results are announced after each roll. Visual dice and confetti
            are decorative; critical and failure results are also announced in
            text.
        </p>
        <dl class="gmrc-guild-roll-context" data-guild-roll-context hidden>
            <div><dt>Roll</dt><dd data-guild-context-kind></dd></div>
            <div><dt>Source</dt><dd data-guild-context-source></dd></div>
            <div><dt>Ability</dt><dd data-guild-context-ability></dd></div>
            <div><dt>Training</dt><dd data-guild-context-proficiency></dd></div>
        </dl>

        <section
            class="gmrc-guild-targeting"
            data-guild-targeting
            aria-labelledby="gmrc-guild-targeting-title"
            hidden
        >
            <header>
                <div>
                    <p class="gmrc-eyebrow">Roll Recipient</p>
                    <h3 id="gmrc-guild-targeting-title">Target</h3>
                </div>
                <span data-guild-target-status>No target selected</span>
            </header>

            <label>
                <span>Target kind</span>
                <select data-guild-target-kind>
                    <option value="">No target selected</option>
                    <?php foreach (($rollTargets ?? []) as $target) : ?>
                        <option
                            value="<?php echo esc_attr((string) $target['kind']); ?>"
                            data-target-id="<?php echo esc_attr((string) ($target['id'] ?? '')); ?>"
                            data-target-label="<?php echo esc_attr((string) $target['target_label']); ?>"
                            data-target-resolved="<?php echo ! empty($target['resolved']) ? 'true' : 'false'; ?>"
                        >
                            <?php echo esc_html((string) $target['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label data-guild-target-name-row hidden>
                <span>Target name / label</span>
                <input
                    type="text"
                    maxlength="80"
                    autocomplete="off"
                    placeholder="e.g. Gravy Golem"
                    data-guild-target-name
                >
            </label>

            <p data-guild-target-note>
                Resolved targets may support Vital Application after the roll; reference-only targets remain non-mutating.
            </p>
        </section>

        <button
            type="button"
            class="gmrc-guild-favourite-toggle"
            data-guild-favourite-toggle
            hidden
        >
            <span aria-hidden="true" data-guild-favourite-symbol>☆</span>
            <span data-guild-favourite-label>Add to Quick Rolls</span>
        </button>

        <details
            class="gmrc-guild-situational"
            data-guild-situational-panel
        >
            <summary>
                <span>Situational Adjustment</span>
                <small data-guild-situational-summary>Next roll only</small>
            </summary>

            <div class="gmrc-guild-situational__controls">
                <label>
                    <span>Flat adjustment</span>
                    <input
                        type="number"
                        min="-20"
                        max="20"
                        step="1"
                        value="0"
                        inputmode="numeric"
                        data-guild-situational-flat
                    >
                </label>

                <label>
                    <span>Bonus die</span>
                    <select data-guild-situational-die>
                        <option value="0" selected>None</option>
                        <option value="4">d4</option>
                        <option value="6">d6</option>
                        <option value="8">d8</option>
                        <option value="10">d10</option>
                        <option value="12">d12</option>
                    </select>
                </label>

                <div
                    class="gmrc-guild-situational__shortcuts"
                    aria-label="Common situational adjustments"
                >
                    <button type="button" data-guild-situational-shortcut="-2">−2</button>
                    <button type="button" data-guild-situational-shortcut="-1">−1</button>
                    <button type="button" data-guild-situational-shortcut="1">+1</button>
                    <button type="button" data-guild-situational-shortcut="2">+2</button>
                </div>

                <p>
                    Applied to the next roll only, then cleared automatically.
                    This never changes the adventurer’s certified modifier.
                </p>
            </div>
        </details>

        <section
            class="gmrc-guild-quick-rolls"
            data-guild-quick-rolls
            aria-labelledby="gmrc-guild-quick-rolls-title"
            hidden
        >
            <header class="gmrc-guild-quick-rolls__heading">
                <div>
                    <p class="gmrc-eyebrow">Pinned Favourites</p>
                    <h3 id="gmrc-guild-quick-rolls-title">Quick Rolls</h3>
                </div>
                <span data-guild-quick-roll-count></span>
            </header>
            <div
                class="gmrc-guild-quick-rolls__list"
                data-guild-quick-roll-list
            ></div>
        </section>

        <details class="gmrc-guild-free-roll" data-guild-free-roll-panel>
            <summary>Guild Free Roll</summary>
            <div class="gmrc-guild-free-roll__controls">
                <label>
                    <span>Quantity</span>
                    <input
                        type="number"
                        min="1"
                        max="20"
                        step="1"
                        value="1"
                        inputmode="numeric"
                        data-guild-free-quantity
                    >
                </label>

                <label>
                    <span>Die</span>
                    <select data-guild-free-die>
                        <option value="4">d4</option>
                        <option value="6" selected>d6</option>
                        <option value="8">d8</option>
                        <option value="10">d10</option>
                        <option value="12">d12</option>
                        <option value="20">d20</option>
                        <option value="100">d100</option>
                    </select>
                </label>

                <label>
                    <span>Modifier</span>
                    <input
                        type="number"
                        min="-99"
                        max="99"
                        step="1"
                        value="0"
                        inputmode="numeric"
                        data-guild-free-modifier
                    >
                </label>

                <button
                    type="button"
                    class="gmrc-guild-free-roll__button"
                    data-guild-free-roll
                >
                    Roll Dice
                </button>

                <button
                    type="button"
                    class="gmrc-guild-free-roll__pin"
                    data-guild-free-roll-pin
                >
                    <span aria-hidden="true">☆</span>
                    Save as Quick Roll
                </button>
            </div>
        </details>

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

            <div
                class="gmrc-guild-dice-stage"
                data-guild-dice-stage
                aria-hidden="true"
            ></div>

            <div
                class="gmrc-guild-dice-result__copy"
                data-guild-dice-result-focus
                tabindex="-1"
                aria-label="Guild Dice result"
            >
                <p class="gmrc-guild-dice-result__mode" data-guild-dice-mode></p>
                <p class="gmrc-guild-dice-result__math" data-guild-dice-math></p>
                <strong class="gmrc-guild-dice-result__total" data-guild-dice-total></strong>
                <p
                    class="gmrc-guild-dice-result__target"
                    data-guild-dice-target-result
                    hidden
                ></p>

                <div
                    class="gmrc-guild-vital-application"
                    data-guild-vital-application
                    hidden
                >
                    <p data-guild-vital-application-note></p>
                    <button
                        type="button"
                        data-guild-vital-apply
                        hidden
                    ></button>
                </div>

                <p class="gmrc-guild-dice-result__auby" data-guild-dice-auby hidden></p>

                <div
                    class="gmrc-guild-critical-follow-up"
                    data-guild-critical-follow-up
                    hidden
                >
                    <p>
                        <strong>Critical damage is ready.</strong>
                        Double the weapon dice; keep the flat modifier once.
                    </p>
                    <button
                        type="button"
                        data-guild-critical-damage
                    ></button>
                </div>

            </div>
        </div>

        <div class="gmrc-guild-dice-history" data-guild-dice-history hidden>
            <div class="gmrc-guild-dice-history__heading">
                <div>
                    <p class="gmrc-guild-dice-history__eyebrow">
                        The Dice Ledger
                    </p>
                    <h3>Recent Rolls</h3>
                </div>
                <button
                    type="button"
                    class="gmrc-guild-dice-history__clear"
                    data-guild-dice-history-clear
                >
                    Clear Ledger
                </button>
            </div>
            <p class="gmrc-guild-dice-history__note">
                Kept for this adventurer during this browser session.
            </p>
            <ol data-guild-dice-history-list></ol>
        </div>

        <p
            class="screen-reader-text"
            data-guild-dice-live
            role="status"
            aria-live="polite"
            aria-atomic="true"
        ></p>
    </aside>
</section>
