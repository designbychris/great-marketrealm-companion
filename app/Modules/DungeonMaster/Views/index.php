<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$baseUrl = home_url('/companion/');
?>

<section class="gmrc-dm-desk" aria-labelledby="gmrc-dm-desk-title">
    <header class="gmrc-dm-desk__hero">
        <div class="gmrc-dm-desk__seal" aria-hidden="true">⚔</div>
        <div>
            <p class="gmrc-dm-desk__eyebrow">The Dungeon Master’s private workspace</p>
            <h1 id="gmrc-dm-desk-title">Dungeon Master’s Desk</h1>
            <p class="gmrc-dm-desk__welcome">
                Welcome, <?php echo esc_html($displayName ?? 'Dungeon Master'); ?>.
                The campaign ledgers are being laid out and the encounter maps are being unrolled.
            </p>
        </div>
    </header>

    <div class="gmrc-dm-desk__notice" role="note">
        <strong>Phase III.15 — Desk foundation</strong>
        <span>
            This room is available only to Dungeon Masters and WordPress administrators.
            Campaign, session, encounter, and player-management ledgers will be opened here in the next certified slices.
        </span>
    </div>

    <div class="gmrc-dm-desk__grid" aria-label="Dungeon Master ledgers">
        <article class="gmrc-dm-ledger gmrc-dm-ledger--featured">
            <span class="gmrc-dm-ledger__number" aria-hidden="true">I</span>
            <div>
                <p class="gmrc-dm-ledger__status">Next to be opened</p>
                <h2>Campaign Register</h2>
                <p>Create and oversee the campaigns that bind players, Fellowships, sessions, and adventures together.</p>
            </div>
            <span class="gmrc-dm-ledger__coming">Coming in Phase III.15.1</span>
        </article>

        <article class="gmrc-dm-ledger">
            <span class="gmrc-dm-ledger__number" aria-hidden="true">II</span>
            <div>
                <p class="gmrc-dm-ledger__status">Planned ledger</p>
                <h2>Session Ledger</h2>
                <p>Prepare sessions, record what happened at the table, and keep the campaign chronology together.</p>
            </div>
        </article>

        <article class="gmrc-dm-ledger">
            <span class="gmrc-dm-ledger__number" aria-hidden="true">III</span>
            <div>
                <p class="gmrc-dm-ledger__status">Planned ledger</p>
                <h2>Encounter Board</h2>
                <p>Stage encounters, keep adversaries and hazards to hand, and prepare the next delicious disaster.</p>
            </div>
        </article>

        <article class="gmrc-dm-ledger">
            <span class="gmrc-dm-ledger__number" aria-hidden="true">IV</span>
            <div>
                <p class="gmrc-dm-ledger__status">Planned ledger</p>
                <h2>Player Roster</h2>
                <p>Connect Guild members and their characters to the campaigns they are invited to join.</p>
            </div>
        </article>
    </div>

    <section class="gmrc-dm-desk__quick" aria-labelledby="gmrc-dm-quick-title">
        <div class="gmrc-dm-desk__section-heading">
            <p class="gmrc-dm-desk__eyebrow">Already on the desk</p>
            <h2 id="gmrc-dm-quick-title">Open existing Guild records</h2>
        </div>

        <nav class="gmrc-dm-quick-links" aria-label="Dungeon Master quick links">
            <?php foreach (($quickLinks ?? []) as $link) : ?>
                <a
                    class="gmrc-dm-quick-link"
                    href="<?php echo esc_url(
                        add_query_arg(
                            'gmrc_route',
                            (string) $link['route'],
                            $baseUrl
                        )
                    ); ?>"
                >
                    <strong><?php echo esc_html((string) $link['label']); ?></strong>
                    <span><?php echo esc_html((string) $link['description']); ?></span>
                    <span class="gmrc-dm-quick-link__arrow" aria-hidden="true">→</span>
                </a>
            <?php endforeach; ?>
        </nav>
    </section>
</section>
