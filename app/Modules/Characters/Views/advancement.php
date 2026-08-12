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
?>

<section
    class="gmrc-advancement-ledger"
    data-advancement-ledger
    aria-labelledby="gmrc-advancement-ledger-title"
>
    <header class="gmrc-advancement-ledger__hero">
        <div>
            <p class="gmrc-eyebrow">The Ascending Register · Phase III.8.1</p>
            <h1 id="gmrc-advancement-ledger-title">The Advancement Ledger</h1>
            <p>
                The Registrar has opened a temporary advancement folio for
                <?php echo esc_html($character->name()->value()); ?>.
                Nothing is committed until the Guild seals the completed
                advancement.
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
                    Class-specific features, spells and subclass decisions
                    are deliberately not guessed in this foundation pass.
                    They will be supplied by the Class Progression Catalogue
                    in later Ascending Register phases.
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

        <section class="gmrc-advancement-ledger__sealed">
            <span aria-hidden="true">🔒</span>
            <div>
                <strong>Advancement commit is intentionally locked.</strong>
                <p>
                    Phase III.8.1 establishes eligibility and the temporary
                    ledger. HP, class and spell choices will be added before
                    the Guild can certify the new level.
                </p>
            </div>
        </section>
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
