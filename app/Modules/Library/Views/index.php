<?php

defined('ABSPATH') || exit;

$domains = $domains ?? [];

$spellbookUrl = add_query_arg(
    'gmrc_route',
    'library/spells',
    home_url('/companion/')
);
?>

<section
    class="gmrc-guild-library"
    aria-labelledby="gmrc-guild-library-title"
>
    <header class="gmrc-guild-library__hero">
        <p class="gmrc-eyebrow">
            Phase III.13 · The Post-Calling Expansion
        </p>

        <h1 id="gmrc-guild-library-title">
            The Guild Library
        </h1>

        <p>
            The Callings are certified. Now the Companion begins gathering
            the wider knowledge adventurers carry into the Marketrealm:
            spells, backgrounds and the tools of the trade.
        </p>
    </header>

    <div
        class="gmrc-guild-library__source"
        role="note"
        aria-label="Canonical source"
    >
        <strong>Canonical source</strong>
        <span>
            The Great Marketrealm - Players Handbook
        </span>
    </div>

    <div class="gmrc-guild-library__grid">
        <?php foreach ($domains as $domain) : ?>
            <article
                class="gmrc-guild-library-card"
                data-library-domain="<?php echo esc_attr(
                    (string) (
                        $domain['key']
                        ?? ''
                    )
                ); ?>"
            >
                <span class="gmrc-guild-library-card__phase">
                    <?php echo esc_html(
                        (string) (
                            $domain['phase']
                            ?? ''
                        )
                    ); ?>
                </span>

                <h2>
                    <?php echo esc_html(
                        (string) (
                            $domain['label']
                            ?? ''
                        )
                    ); ?>
                </h2>

                <p>
                    <?php echo esc_html(
                        (string) (
                            $domain['description']
                            ?? ''
                        )
                    ); ?>
                </p>

                <dl>
                    <div>
                        <dt>Status</dt>
                        <dd>
                            <?php echo esc_html(
                                ($domain['status'] ?? 'foundation')
                                === 'registered'
                                    ? 'Canonical register ready'
                                    : 'Foundation registered'
                            ); ?>
                        </dd>
                    </div>
                    <div>
                        <dt>Records</dt>
                        <dd>
                            <?php echo esc_html(
                                (string) (
                                    $domain['entry_count']
                                    ?? 0
                                )
                            ); ?>
                            imported
                        </dd>
                    </div>
                </dl>

                <?php if (
                    ($domain['key'] ?? '') === 'spells'
                    && ($domain['status'] ?? '') === 'registered'
                ) : ?>
                    <a
                        class="gmrc-guild-library-card__open"
                        href="<?php echo esc_url($spellbookUrl); ?>"
                    >
                        Open Sage’s Spellbook
                    </a>
                <?php else : ?>
                    <p class="gmrc-guild-library-card__note">
                        Records remain intentionally untouched until this
                        library receives its dedicated III.13.x phase.
                    </p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
