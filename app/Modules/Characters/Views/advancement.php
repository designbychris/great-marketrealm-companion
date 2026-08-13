<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

if (! isset($character) || ! $character instanceof Character) {
    return;
}

$advancement = isset($advancement) && is_array($advancement)
    ? $advancement
    : [];

$characterId = $character->id()->value();

$ledgerUrl = add_query_arg(
    [
        'gmrc_route' => 'characters/' . rawurlencode($characterId),
        'gmrc_ledger_tab' => 'progression',
    ],
    home_url('/companion/')
);

$appRequestUrl = admin_url(
    'admin-post.php'
);

$flash = isset($flash) && is_array($flash)
    ? $flash
    : [];

$advancementSeal = isset($advancementSeal)
    && is_array($advancementSeal)
        ? $advancementSeal
        : [];
?>

<section
    class="gmrc-advancement-ledger"
    data-advancement-ledger
    aria-labelledby="gmrc-advancement-ledger-title"
>
    <header class="gmrc-advancement-ledger__hero">
        <div>
            <p class="gmrc-eyebrow">The Ascending Register · Phase III.8.7</p>
            <h1 id="gmrc-advancement-ledger-title">The Advancement Ledger</h1>
            <p>
                The Registrar has opened a pending advancement folio for
                <?php echo esc_html($character->name()->value()); ?>.
                Your choices are retained with this adventurer, but nothing changes on the Character until later Guild certification.
            </p>
        </div>

        <div
            class="gmrc-advancement-ledger__levels"
            aria-label="Advancement level summary"
        >
            <span>
                Current
                <strong><?php echo esc_html(
                    (string) ($advancement['current_level'] ?? 1)
                ); ?></strong>
            </span>
            <i aria-hidden="true">→</i>
            <span>
                Target
                <strong><?php echo esc_html(
                    (string) ($advancement['target_level'] ?? 1)
                ); ?></strong>
            </span>
        </div>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div
            class="gmrc-advancement-ledger__flash is-success"
            role="status"
        >
            <?php echo esc_html(
                (string) $flash['success']
            ); ?>
        </div>
    <?php endif; ?>

    <?php if (! empty($flash['error'])) : ?>
        <div
            class="gmrc-advancement-ledger__flash is-error"
            role="alert"
        >
            <?php echo esc_html(
                (string) $flash['error']
            ); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($advancement['eligible'])) : ?>
        <section class="gmrc-advancement-ledger__notice">
            <span aria-hidden="true">✦</span>
            <div>
                <h2>Not yet ready for advancement</h2>
                <p>
                    More experience must be entered into the Rising Register
                    before another Guild certification can begin.
                </p>
            </div>
        </section>
    <?php else : ?>
        <section class="gmrc-advancement-ledger__notice is-ready">
            <span aria-hidden="true">✦</span>
            <div>
                <h2>
                    Level <?php echo esc_html(
                        (string) $advancement['target_level']
                    ); ?> is unlocked
                </h2>
                <p>
                    <?php echo esc_html(
                        number_format_i18n(
                            (int) $advancement['experience']
                        )
                    ); ?> XP has been recorded.
                    <?php if ((int) $advancement['levels_waiting'] > 1) : ?>
                        There are
                        <?php echo esc_html(
                            (string) $advancement['levels_waiting']
                        ); ?> level certifications waiting, but the Guild
                        processes them one at a time.
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <section
            class="gmrc-rising-folios"
            aria-labelledby="gmrc-rising-folios-title"
            data-rising-folios
        >
            <header class="gmrc-rising-folios__header">
                <div>
                    <p class="gmrc-eyebrow">
                        Phase III.8.2 · The Rising Folios
                    </p>

                    <h2 id="gmrc-rising-folios-title">
                        What changes at this level?
                    </h2>

                    <p>
                        Each folio represents one part of the advancement.
                        Ready folios need no decision; attention folios require
                        the adventurer’s choice before certification.
                    </p>
                </div>

                <strong class="gmrc-rising-folios__count">
                    <?php echo esc_html(
                        (string) (
                            $advancement['folio_ready_count']
                            ?? 0
                        )
                    ); ?>
                    /
                    <?php echo esc_html(
                        (string) (
                            $advancement['folio_total']
                            ?? 0
                        )
                    ); ?>
                    folios ready
                </strong>
            </header>

            <div class="gmrc-rising-folios__grid">
                <?php foreach (
                    ($advancement['folios'] ?? [])
                    as $folio
                ) : ?>
                    <article
                        class="gmrc-rising-folio gmrc-rising-folio--<?php echo esc_attr(
                            (string) ($folio['status'] ?? 'information')
                        ); ?>"
                        data-rising-folio="<?php echo esc_attr(
                            (string) ($folio['key'] ?? '')
                        ); ?>"
                    >
                        <header>
                            <span aria-hidden="true">
                                <?php echo ! empty($folio['ready'])
                                    ? '✓'
                                    : '!'; ?>
                            </span>

                            <div>
                                <p>
                                    <?php echo ! empty($folio['ready'])
                                        ? 'Ready'
                                        : 'Decision required'; ?>
                                </p>

                                <h3><?php echo esc_html(
                                    (string) ($folio['label'] ?? '')
                                ); ?></h3>
                            </div>
                        </header>

                        <p><?php echo esc_html(
                            (string) ($folio['summary'] ?? '')
                        ); ?></p>

                        <?php if (
                            ! empty($folio['facts'])
                            && is_array($folio['facts'])
                        ) : ?>
                            <dl>
                                <?php foreach (
                                    $folio['facts']
                                    as $factKey => $factValue
                                ) : ?>
                                    <?php if (
                                        is_scalar($factValue)
                                    ) : ?>
                                        <div>
                                            <dt><?php echo esc_html(
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        (string) $factKey
                                                    )
                                                )
                                            ); ?></dt>

                                            <dd><?php echo esc_html(
                                                is_bool($factValue)
                                                    ? (
                                                        $factValue
                                                            ? 'Yes'
                                                            : 'No'
                                                    )
                                                    : (string) $factValue
                                            ); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </dl>
                        <?php endif; ?>

                        <?php if (
                            ! empty($folio['delegated'])
                            && is_array($folio['delegated'])
                        ) : ?>
                            <div class="gmrc-rising-folio__delegated">
                                <strong>
                                    Specialist folios identified
                                </strong>

                                <ul>
                                    <?php foreach (
                                        $folio['delegated']
                                        as $delegated
                                    ) : ?>
                                        <li>
                                            <span aria-hidden="true">↗</span>

                                            <div>
                                                <strong><?php echo esc_html(
                                                    (string) (
                                                        $delegated['label']
                                                        ?? ''
                                                    )
                                                ); ?></strong>

                                                <p><?php echo esc_html(
                                                    (string) (
                                                        $delegated['detail']
                                                        ?? ''
                                                    )
                                                ); ?></p>

                                                <?php if (
                                                    ! empty(
                                                        $delegated['phase']
                                                    )
                                                ) : ?>
                                                    <small>
                                                        Assigned to Phase
                                                        <?php echo esc_html(
                                                            (string) $delegated['phase']
                                                        ); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (
                            ! empty($folio['choices'])
                            && is_array($folio['choices'])
                        ) : ?>
                            <?php
                            $choiceKey = (string) (
                                $folio['facts']['choice_key']
                                ?? ''
                            );

                            $choiceMode = (string) (
                                $folio['facts']['choice_mode']
                                ?? 'single'
                            );

                            $selectedValues = is_array(
                                $folio['facts']['selected_values']
                                ?? null
                            )
                                ? $folio['facts']['selected_values']
                                : array_filter([
                                    (string) (
                                        $folio['facts']['selected']
                                        ?? ''
                                    ),
                                ]);

                            $choiceMinimum = (int) (
                                $folio['facts']['choice_minimum']
                                ?? 1
                            );
                            ?>

                            <form
                                class="gmrc-rising-folio__choices"
                                method="post"
                                action="<?php echo esc_url($appRequestUrl); ?>"
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
                                        . rawurlencode($characterId)
                                        . '/progression/advance/choice'
                                    ); ?>"
                                >

                                <input
                                    type="hidden"
                                    name="choice_key"
                                    value="<?php echo esc_attr($choiceKey); ?>"
                                >

                                <?php wp_nonce_field(
                                    'gmrc_character_advancement_'
                                    . $characterId,
                                    'gmrc_nonce'
                                ); ?>

                                <fieldset>
                                    <legend>
                                        <?php echo $choiceMinimum > 1
                                            ? esc_html(
                                                sprintf(
                                                    'Choose %d options',
                                                    $choiceMinimum
                                                )
                                            )
                                            : 'Choose one option'; ?>
                                    </legend>

                                    <?php foreach (
                                        $folio['choices']
                                        as $choice
                                    ) : ?>
                                        <?php
                                        $choiceValue = (string) (
                                            $choice['key']
                                            ?? ''
                                        );
                                        ?>

                                        <label>
                                            <input
                                                type="<?php echo esc_attr(
                                                    $choiceMode === 'single'
                                                        ? 'radio'
                                                        : 'checkbox'
                                                ); ?>"
                                                name="<?php echo esc_attr(
                                                    $choiceMode === 'single'
                                                        ? 'choice'
                                                        : 'choice[]'
                                                ); ?>"
                                                value="<?php echo esc_attr(
                                                    $choiceValue
                                                ); ?>"
                                                <?php checked(
                                                    in_array(
                                                        $choiceValue,
                                                        $selectedValues,
                                                        true
                                                    )
                                                ); ?>
                                            >

                                            <span>
                                                <strong><?php echo esc_html(
                                                    (string) (
                                                        $choice['label']
                                                        ?? ''
                                                    )
                                                ); ?></strong>

                                                <?php if (
                                                    ! empty($choice['detail'])
                                                ) : ?>
                                                    <small>
                                                        <?php echo esc_html(
                                                            (string) $choice['detail']
                                                        ); ?>
                                                    </small>
                                                <?php endif; ?>

                                                <?php if (
                                                    isset($choice['spell_level'])
                                                    && (int) $choice['spell_level'] > 0
                                                ) : ?>
                                                    <small>
                                                        Level <?php echo esc_html(
                                                            (string) $choice['spell_level']
                                                        ); ?> spell
                                                    </small>
                                                <?php endif; ?>

                                                <?php if (
                                                    isset($choice['value'])
                                                ) : ?>
                                                    <small>
                                                        +<?php echo esc_html(
                                                            (string) $choice['value']
                                                        ); ?> maximum HP
                                                    </small>
                                                <?php elseif (
                                                    isset($choice['die'])
                                                ) : ?>
                                                    <small>
                                                        <?php echo esc_html(
                                                            (string) $choice['die']
                                                        ); ?>
                                                        <?php echo (int) (
                                                            $choice['modifier']
                                                            ?? 0
                                                        ) >= 0
                                                            ? '+'
                                                            : ''; ?>
                                                        <?php echo esc_html(
                                                            (string) (
                                                                $choice['modifier']
                                                                ?? 0
                                                            )
                                                        ); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </fieldset>

                                <button
                                    type="submit"
                                    class="gmrc-button gmrc-button--primary"
                                >
                                    <?php echo ! empty($folio['ready'])
                                        ? 'Update Choice'
                                        : 'Record Choice'; ?>
                                </button>

                                <?php if (! empty($folio['ready'])) : ?>
                                    <p class="gmrc-rising-folio__recorded">
                                        ✓ Choice saved in this pending
                                        advancement.
                                    </p>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (
            ! empty($advancementSeal['available'])
        ) : ?>
            <section
                class="gmrc-advancement-seal <?php echo ! empty(
                    $advancementSeal['ready']
                )
                    ? 'is-ready'
                    : 'is-pending'; ?>"
                data-advancement-seal
                <?php if (
                    ! empty($advancementSeal['ready'])
                ) : ?>
                    data-auby-seal-surface
                <?php endif; ?>
                aria-labelledby="gmrc-advancement-seal-title"
            >
                <?php if (
                    ! empty($advancementSeal['ready'])
                ) : ?>
                    <div class="gmrc-advancement-seal__mark">
                        <?php echo $this->component(
                            'components.auby.seal-of-approval',
                            [
                                'variant' => 'gold',
                                'context' => 'advancement',
                                'trigger' => 'visible',
                            ]
                        ); ?>
                    </div>
                <?php else : ?>
                    <div
                        class="gmrc-advancement-seal__pending-mark"
                        aria-hidden="true"
                    >
                        🔏
                    </div>
                <?php endif; ?>

                <div class="gmrc-advancement-seal__body">
                    <header>
                        <div>
                            <p class="gmrc-eyebrow">
                                Registrar’s Final Review
                            </p>

                            <h2 id="gmrc-advancement-seal-title">
                                <?php echo esc_html(
                                    (string) (
                                        $advancementSeal['title']
                                        ?? ''
                                    )
                                ); ?>
                            </h2>

                            <strong>
                                <?php echo esc_html(
                                    (string) (
                                        $advancementSeal['status']
                                        ?? ''
                                    )
                                ); ?>
                            </strong>

                            <p>
                                <?php echo esc_html(
                                    (string) (
                                        $advancementSeal['copy']
                                        ?? ''
                                    )
                                ); ?>
                            </p>
                        </div>

                        <span class="gmrc-advancement-seal__count">
                            <?php echo esc_html(
                                (string) (
                                    $advancementSeal[
                                        'folio_ready_count'
                                    ] ?? 0
                                )
                            ); ?>
                            /
                            <?php echo esc_html(
                                (string) (
                                    $advancementSeal[
                                        'folio_total'
                                    ] ?? 0
                                )
                            ); ?>
                            reviewed
                        </span>
                    </header>

                    <div class="gmrc-advancement-seal__review">
                        <?php foreach (
                            ($advancementSeal['review'] ?? [])
                            as $review
                        ) : ?>
                            <article class="<?php echo ! empty(
                                $review['ready']
                            )
                                ? 'is-ready'
                                : 'is-waiting'; ?>">
                                <span aria-hidden="true">
                                    <?php echo ! empty(
                                        $review['ready']
                                    )
                                        ? '✓'
                                        : '!'; ?>
                                </span>

                                <div>
                                    <strong><?php echo esc_html(
                                        (string) (
                                            $review['label']
                                            ?? ''
                                        )
                                    ); ?></strong>

                                    <p><?php echo esc_html(
                                        (string) (
                                            $review['detail']
                                            ?? ''
                                        )
                                    ); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <?php if (
                        ! empty($advancementSeal['ready'])
                    ) : ?>
                        <p class="gmrc-advancement-seal__certified">
                            Certified for final Guild processing by the
                            Registrar. No Character changes have been applied.
                        </p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <div class="gmrc-advancement-ledger__grid">
            <section>
                <p class="gmrc-eyebrow">Guild Calling</p>
                <h2><?php echo esc_html(
                    (string) $advancement['class_label']
                ); ?></h2>

                <dl>
                    <div>
                        <dt>Class Hit Die</dt>
                        <dd><?php echo esc_html(
                            (string) $advancement['hit_die']
                        ); ?></dd>
                    </div>
                    <div>
                        <dt>Suggested HP gain</dt>
                        <dd>+<?php echo esc_html(
                            (string) $advancement['suggested_hp_gain']
                        ); ?></dd>
                    </div>
                    <div>
                        <dt>Current proficiency</dt>
                        <dd><?php echo esc_html(
                            (string) $advancement['current_proficiency']
                        ); ?></dd>
                    </div>
                    <div>
                        <dt>Target proficiency</dt>
                        <dd><?php echo esc_html(
                            (string) $advancement['target_proficiency']
                        ); ?></dd>
                    </div>
                </dl>
            </section>

            <section>
                <p class="gmrc-eyebrow">Advancement Decisions</p>
                <h2>Choices waiting</h2>

                <ul class="gmrc-advancement-ledger__choices">
                    <?php foreach (
                        ($advancement['choices'] ?? [])
                        as $choice
                    ) : ?>
                        <li>
                            <strong><?php echo esc_html(
                                (string) ($choice['label'] ?? '')
                            ); ?></strong>
                            <span><?php echo esc_html(
                                (string) ($choice['detail'] ?? '')
                            ); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="gmrc-advancement-ledger__foundation">
                    The Calling catalogue now identifies level-specific
                    class progression and delegates specialist decisions to
                    their proper folios. Spellbook, Path and Measure-of-Growth
                    choices will join the same saved advancement draft.
                </p>
            </section>
        </div>

        <?php if (! empty($advancement['automatic'])) : ?>
            <section class="gmrc-advancement-ledger__automatic">
                <p class="gmrc-eyebrow">Automatic changes</p>
                <h2>Already known to the Registrar</h2>
                <ul>
                    <?php foreach (
                        $advancement['automatic']
                        as $automatic
                    ) : ?>
                        <li>
                            <strong><?php echo esc_html(
                                (string) ($automatic['label'] ?? '')
                            ); ?></strong>
                            <?php echo esc_html(
                                (string) ($automatic['detail'] ?? '')
                            ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (
            ! empty($advancementSeal['ready'])
        ) : ?>
            <section
                class="gmrc-guild-certification"
                data-guild-certification
                aria-labelledby="gmrc-guild-certification-title"
            >
                <div class="gmrc-guild-certification__mark" aria-hidden="true">
                    ✦
                </div>

                <div>
                    <p class="gmrc-eyebrow">
                        Final Guild Entry
                    </p>

                    <h2 id="gmrc-guild-certification-title">
                        Certify this Advancement
                    </h2>

                    <p>
                        This action enters the reviewed Level and Vitality
                        changes into the permanent Character record. The
                        completed certification cannot be applied twice.
                    </p>

                    <form
                        method="post"
                        action="<?php echo esc_url($appRequestUrl); ?>"
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
                                . rawurlencode($characterId)
                                . '/progression/advance/certify'
                            ); ?>"
                        >

                        <?php wp_nonce_field(
                            'gmrc_character_advancement_'
                            . $characterId,
                            'gmrc_nonce'
                        ); ?>

                        <button
                            type="submit"
                            class="gmrc-button gmrc-button--primary"
                        >
                            Certify Advancement
                        </button>
                    </form>

                    <small>
                        The Registrar will re-check every folio before
                        altering the Guild Record.
                    </small>
                </div>
            </section>
        <?php else : ?>
            <section class="gmrc-advancement-ledger__sealed">
                <span aria-hidden="true">🔒</span>
                <div>
                    <strong>
                        Guild Certification remains locked.
                    </strong>
                    <p>
                        Every required folio must be ready and the
                        Advancement Seal issued before permanent Character
                        changes can be certified.
                    </p>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>

    <footer class="gmrc-advancement-ledger__footer">
        <a
            class="gmrc-button gmrc-button--secondary"
            href="<?php echo esc_url($ledgerUrl); ?>"
        >
            Return to Rising Register
        </a>

        <blockquote>
            “No heroic transformation until I have checked the paperwork.”
            <footer>— Auby</footer>
        </blockquote>
    </footer>
</section>
