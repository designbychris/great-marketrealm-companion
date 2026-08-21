<?php

defined('ABSPATH') || exit;

$filters = $filters ?? [];
$results = $results ?? [];
$levels = $levels ?? [];
$schools = $schools ?? [];
$accessLabels = $access_labels ?? [];

$spellbookUrl = add_query_arg(
    'gmrc_route',
    'library/spells',
    home_url('/companion/')
);
$libraryUrl = add_query_arg(
    'gmrc_route',
    'library',
    home_url('/companion/')
);
?>

<section
    class="gmrc-spellbook"
    aria-labelledby="gmrc-spellbook-title"
>
    <header class="gmrc-spellbook__hero">
        <div class="gmrc-spellbook__keeper" aria-hidden="true">
            <span>S</span>
            <small>Keeper of Knowledge</small>
        </div>

        <div>
            <p class="gmrc-eyebrow">
                Phase III.13.1B · The Guild Library
            </p>
            <h1 id="gmrc-spellbook-title">
                Sage’s Spellbook
            </h1>
            <p>
                “Knowledge is best kept where everyone can find it.
                Preferably alphabetised.”
            </p>
            <p>
                Sage records the spell names, mechanics and uncertainties
                exactly as the Guild’s canonical Player’s Handbook supplies
                them. Nothing missing from the source is silently invented.
            </p>
        </div>
    </header>

    <nav
        class="gmrc-spellbook__breadcrumb"
        aria-label="Guild Library breadcrumb"
    >
        <a href="<?php echo esc_url($libraryUrl); ?>">
            Guild Library
        </a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Sage’s Spellbook</span>
    </nav>

    <div
        class="gmrc-spellbook__summary"
        aria-label="Spell Register summary"
    >
        <span>
            <strong><?php echo esc_html((string) ($total_count ?? 0)); ?></strong>
            spells
        </span>
        <span>
            <strong><?php echo esc_html((string) ($renamed_count ?? 0)); ?></strong>
            Marketrealm renames
        </span>
        <span>
            <strong><?php echo esc_html((string) ($original_count ?? 0)); ?></strong>
            original spells
        </span>
        <span>
            <strong><?php echo esc_html((string) ($source_issue_count ?? 0)); ?></strong>
            source notes
        </span>
    </div>

    <form
        class="gmrc-spellbook-filters"
        action="<?php echo esc_url($spellbookUrl); ?>"
        method="get"
        role="search"
    >
        <input
            type="hidden"
            name="gmrc_route"
            value="library/spells"
        >

        <label class="gmrc-spellbook-filters__search">
            <span>Search Sage’s shelves</span>
            <input
                type="search"
                name="q"
                value="<?php echo esc_attr((string) ($filters['q'] ?? '')); ?>"
                placeholder="Spell name, original spell, mechanic…"
            >
        </label>

        <label>
            <span>Kind</span>
            <select name="kind">
                <option value="">All magic</option>
                <option
                    value="renamed"
                    <?php selected(($filters['kind'] ?? ''), 'renamed'); ?>
                >
                    Marketrealm renames
                </option>
                <option
                    value="marketrealm-original"
                    <?php selected(
                        ($filters['kind'] ?? ''),
                        'marketrealm-original'
                    ); ?>
                >
                    Marketrealm originals
                </option>
            </select>
        </label>

        <label>
            <span>Level</span>
            <select name="level">
                <option value="">All stated levels</option>
                <?php foreach ($levels as $level) : ?>
                    <option
                        value="<?php echo esc_attr((string) $level); ?>"
                        <?php selected(
                            (string) ($filters['level'] ?? ''),
                            (string) $level
                        ); ?>
                    >
                        <?php echo esc_html(
                            (int) $level === 0
                                ? 'Cantrip'
                                : 'Level ' . $level
                        ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <span>School</span>
            <select name="school">
                <option value="">All stated schools</option>
                <?php foreach ($schools as $school) : ?>
                    <option
                        value="<?php echo esc_attr((string) $school); ?>"
                        <?php selected(
                            (string) ($filters['school'] ?? ''),
                            (string) $school
                        ); ?>
                    >
                        <?php echo esc_html(
                            ucwords(
                                str_replace(
                                    '-',
                                    ' ',
                                    (string) $school
                                )
                            )
                        ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <?php if ($accessLabels !== []) : ?>
            <label>
                <span>Calling access</span>
                <select name="access">
                    <option value="">All stated access</option>
                    <?php foreach ($accessLabels as $label) : ?>
                        <option
                            value="<?php echo esc_attr((string) $label); ?>"
                            <?php selected(
                                (string) ($filters['access'] ?? ''),
                                (string) $label
                            ); ?>
                        >
                            <?php echo esc_html((string) $label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>

        <div class="gmrc-spellbook-filters__actions">
            <button type="submit">
                Search the Spellbook
            </button>
            <a href="<?php echo esc_url($spellbookUrl); ?>">
                Clear filters
            </a>
        </div>
    </form>

    <div
        class="gmrc-spellbook__results-heading"
        aria-live="polite"
    >
        <h2>
            <?php echo esc_html(
                sprintf(
                    '%d spell%s found',
                    (int) ($result_count ?? 0),
                    (int) ($result_count ?? 0) === 1 ? '' : 's'
                )
            ); ?>
        </h2>
    </div>

    <?php if ($results === []) : ?>
        <section class="gmrc-spellbook__empty">
            <h3>Sage cannot find that entry.</h3>
            <p>
                Try a broader name or clear one of the filters.
                No spell records have been changed.
            </p>
        </section>
    <?php else : ?>
        <div class="gmrc-spellbook__grid">
            <?php foreach ($results as $spell) : ?>
                <article
                    class="gmrc-spell-card"
                    data-spell-kind="<?php echo esc_attr(
                        (string) ($spell['kind'] ?? '')
                    ); ?>"
                >
                    <header class="gmrc-spell-card__heading">
                        <div>
                            <p class="gmrc-spell-card__meta">
                                <?php echo esc_html(
                                    (string) ($spell['level_label'] ?? '')
                                ); ?>
                                ·
                                <?php echo esc_html(
                                    (string) ($spell['school_label'] ?? '')
                                ); ?>
                            </p>
                            <h3>
                                <?php echo esc_html(
                                    (string) ($spell['name'] ?? '')
                                ); ?>
                            </h3>
                        </div>

                        <span class="gmrc-spell-card__kind">
                            <?php echo esc_html(
                                (string) ($spell['kind_label'] ?? '')
                            ); ?>
                        </span>
                    </header>

                    <?php if (! empty($spell['original_spell'])) : ?>
                        <p class="gmrc-spell-card__original">
                            <strong>Known outside the Marketrealm as:</strong>
                            <?php echo esc_html(
                                (string) $spell['original_spell']
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (! empty($spell['access_labels'])) : ?>
                        <p>
                            <strong>Handbook access:</strong>
                            <?php echo esc_html(
                                implode(
                                    ', ',
                                    $spell['access_labels']
                                )
                            ); ?>
                        </p>
                    <?php else : ?>
                        <p class="gmrc-spell-card__unknown">
                            Calling access is not stated in the handbook entry.
                        </p>
                    <?php endif; ?>

                    <?php if (! empty($spell['source_issues'])) : ?>
                        <aside
                            class="gmrc-spell-card__source-note"
                            aria-label="Canonical source note"
                        >
                            <strong>Sage’s source note</strong>
                            <span>
                                This entry contains
                                <?php echo esc_html(
                                    (string) count($spell['source_issues'])
                                ); ?>
                                handbook uncertainty marker(s).
                            </span>
                        </aside>
                    <?php endif; ?>

                    <?php foreach (
                        ($spell['variants'] ?? [])
                        as $variant
                    ) : ?>
                        <details class="gmrc-spell-card__detail">
                            <summary>
                                <?php echo esc_html(
                                    (int) ($spell['variant_count'] ?? 1) > 1
                                        ? 'Read handbook variant '
                                            . (string) (
                                                $variant['source_variant']
                                                ?? ''
                                            )
                                        : 'Read spell entry'
                                ); ?>
                            </summary>

                            <div>
                                <?php if (
                                    isset($variant['level'])
                                    && $variant['level'] !== null
                                ) : ?>
                                    <p>
                                        <strong>Variant level:</strong>
                                        <?php echo esc_html(
                                            (string) $variant['level']
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (! empty($variant['school'])) : ?>
                                    <p>
                                        <strong>Variant school:</strong>
                                        <?php echo esc_html(
                                            ucwords(
                                                (string) $variant['school']
                                            )
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                                <p class="gmrc-spell-card__source-text">
                                    <?php echo nl2br(
                                        esc_html(
                                            (string) (
                                                $variant['source_text']
                                                ?? ''
                                            )
                                        )
                                    ); ?>
                                </p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
