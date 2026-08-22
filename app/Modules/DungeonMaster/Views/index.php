<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$baseUrl = home_url('/companion/');
$campaignUrl = add_query_arg(
    'gmrc_route',
    'dungeon-master/campaigns',
    $baseUrl
);
?>

<section class="gmrc-dm-desk" aria-labelledby="gmrc-dm-desk-title">
    <header class="gmrc-dm-desk__hero">
        <div class="gmrc-dm-desk__hero-copy">
            <p class="gmrc-dm-desk__eyebrow">The Dungeon Master’s private workspace</p>
            <h1 id="gmrc-dm-desk-title">Dungeon Master’s Desk</h1>
            <p class="gmrc-dm-desk__tagline">Plan adventures. Guide legends. Shape the Marketrealm.</p>
            <p class="gmrc-dm-desk__welcome">
                Welcome, <?php echo esc_html($displayName ?? 'Dungeon Master'); ?>.
                Your command centre for campaigns, sessions, encounters and the heroes
                who will shape the stories yet to be told.
            </p>
            <a class="gmrc-dm-desk__primary-action" href="<?php echo esc_url($campaignUrl); ?>">
                Open Campaign Register <span aria-hidden="true">→</span>
            </a>
        </div>
    </header>

    <section class="gmrc-dm-desk__workspace" aria-labelledby="gmrc-dm-workspace-title">
        <div class="gmrc-dm-desk__ornament-heading">
            <span aria-hidden="true">◆</span>
            <h2 id="gmrc-dm-workspace-title">DM Workspace</h2>
            <span aria-hidden="true">◆</span>
        </div>

        <div class="gmrc-dm-desk__grid" aria-label="Dungeon Master ledgers">
            <article class="gmrc-dm-ledger gmrc-dm-ledger--campaign">
                <div class="gmrc-dm-ledger__icon" aria-hidden="true">📜</div>
                <div>
                    <p class="gmrc-dm-ledger__status">Ledger I · Open</p>
                    <h3>Campaign Register</h3>
                    <p>Create and manage your campaigns. Build worlds worth remembering.</p>
                </div>
                <a class="gmrc-dm-ledger__action" href="<?php echo esc_url($campaignUrl); ?>">
                    Open Register <span aria-hidden="true">→</span>
                </a>
            </article>

            <article class="gmrc-dm-ledger gmrc-dm-ledger--session">
                <div class="gmrc-dm-ledger__icon" aria-hidden="true">📖</div>
                <div>
                    <p class="gmrc-dm-ledger__status">Ledger II · Planned</p>
                    <h3>Session Ledger</h3>
                    <p>Plan, run and record your sessions. Track milestones and memorable moments.</p>
                </div>
                <span class="gmrc-dm-ledger__action is-disabled" aria-disabled="true">Coming soon</span>
            </article>

            <article class="gmrc-dm-ledger gmrc-dm-ledger--encounter">
                <div class="gmrc-dm-ledger__icon" aria-hidden="true">⚔️</div>
                <div>
                    <p class="gmrc-dm-ledger__status">Ledger III · Planned</p>
                    <h3>Encounter Board</h3>
                    <p>Design encounters, marshal adversaries and prepare the challenges ahead.</p>
                </div>
                <span class="gmrc-dm-ledger__action is-disabled" aria-disabled="true">Coming soon</span>
            </article>

            <article class="gmrc-dm-ledger gmrc-dm-ledger--roster">
                <div class="gmrc-dm-ledger__icon" aria-hidden="true">👥</div>
                <div>
                    <p class="gmrc-dm-ledger__status">Ledger IV · Open</p>
                    <h3>Player Roster</h3>
                    <p>Gather registered Guild Players and attach their adventurers inside each campaign.</p>
                </div>
                <a class="gmrc-dm-ledger__action" href="<?php echo esc_url($campaignUrl); ?>">Choose Campaign <span aria-hidden="true">→</span></a>
            </article>
        </div>
    </section>

    <section class="gmrc-dm-desk__quick" aria-labelledby="gmrc-dm-quick-title">
        <div class="gmrc-dm-desk__section-heading">
            <p class="gmrc-dm-desk__eyebrow">Quick Access</p>
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
                    <span class="gmrc-dm-quick-link__mark" aria-hidden="true">✦</span>
                    <span class="gmrc-dm-quick-link__copy">
                        <strong><?php echo esc_html((string) $link['label']); ?></strong>
                        <small><?php echo esc_html((string) $link['description']); ?></small>
                    </span>
                    <span class="gmrc-dm-quick-link__arrow" aria-hidden="true">→</span>
                </a>
            <?php endforeach; ?>
        </nav>
    </section>
</section>
