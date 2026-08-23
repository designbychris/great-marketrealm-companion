<?php

defined('ABSPATH') || exit;

$domains = $domains ?? [];

$spellbookUrl = add_query_arg(
    'gmrc_route',
    'library/spells',
    home_url('/companion/')
);

$backgroundRegisterUrl = add_query_arg(
    'gmrc_route',
    'library/backgrounds',
    home_url('/companion/')
);

$armouryUrl = add_query_arg(
    'gmrc_route',
    'library/armoury',
    home_url('/companion/')
);

$relicsUrl = add_query_arg(
    'gmrc_route',
    'library/relics',
    home_url('/companion/')
);

$fieldGuideUrl = add_query_arg(
    'gmrc_route',
    'library/field-guide',
    home_url('/companion/')
);
?>

<section
    class="gmrc-guild-library"
    aria-labelledby="gmrc-guild-library-title"
>
    <header class="gmrc-guild-library__hero">
        <p class="gmrc-eyebrow">
            The Guild Library
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
            The Great Marketrealm - Players Handbook · Steward-approved creature field notes
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
                    ($domain['status'] ?? '') === 'registered'
                    && in_array(
                        ($domain['key'] ?? ''),
                        ['spells', 'backgrounds', 'armoury', 'relics', 'field-guide'],
                        true
                    )
                ) : ?>
                    <a
                        class="gmrc-guild-library-card__open"
                        href="<?php echo esc_url(
                            match ($domain['key'] ?? '') {
                                'spells' => $spellbookUrl,
                                'backgrounds' => $backgroundRegisterUrl,
                                'armoury' => $armouryUrl,
                                'relics' => $relicsUrl,
                                default => $fieldGuideUrl,
                            }
                        ); ?>"
                    >
                        <?php echo esc_html(
                            match ($domain['key'] ?? '') {
                                'spells' => 'Open Sage’s Spellbook',
                                'backgrounds' => 'Open Background Register',
                                'armoury' => 'Open Marketrealm Armoury',
                                'relics' => 'Open Relic Register',
                                default => 'Open Guild Field Guide',
                            }
                        ); ?>
                    </a>
                <?php else : ?>
                    <p class="gmrc-guild-library-card__note">
                        This collection remains sealed until its dedicated Guild Library work is ready.
                    </p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
