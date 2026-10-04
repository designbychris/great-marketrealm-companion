<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$baseUrl = home_url('/companion/');
$route = static fn (string $path): string => add_query_arg('gmrc_route', $path, $baseUrl);
$campaignUrl = $route('dungeon-master/campaigns');
$campaigns = array_values($campaigns ?? []);
$activeCampaigns = array_values(array_filter($campaigns, static fn ($campaign): bool => ! $campaign->isArchived()));
$archivedCampaigns = array_values(array_filter($campaigns, static fn ($campaign): bool => $campaign->isArchived()));
?>

<section class="gmrc-dm-desk" aria-labelledby="gmrc-dm-desk-title">
    <header class="gmrc-dm-desk__hero">
        <div class="gmrc-dm-desk__hero-copy">
            <p class="gmrc-dm-desk__eyebrow">The Dungeon Master’s private workspace</p>
            <h1 id="gmrc-dm-desk-title">Dungeon Master’s Desk</h1>
            <p class="gmrc-dm-desk__tagline">Choose the campaign. Then take the chair.</p>
            <p class="gmrc-dm-desk__welcome">Welcome, <?php echo esc_html($displayName ?? 'Dungeon Master'); ?>. Start a new campaign or open one of your existing Campaign Command Centres.</p>
            <div class="gmrc-dm-desk__hero-actions">
                <a class="gmrc-dm-desk__primary-action" href="<?php echo esc_url($route('dungeon-master/campaigns/create')); ?>">Start New Campaign <span aria-hidden="true">→</span></a>
                <a class="gmrc-dm-desk__secondary-action" href="<?php echo esc_url($campaignUrl); ?>">Open Campaign Register</a>
            </div>
        </div>
    </header>

    <section class="gmrc-dm-desk__workspace" aria-labelledby="gmrc-dm-campaigns-title">
        <div class="gmrc-dm-desk__section-heading">
            <div><p class="gmrc-dm-desk__eyebrow">Campaign Register</p><h2 id="gmrc-dm-campaigns-title">Your Campaigns</h2></div>
            <p class="gmrc-dm-desk__workshop-intro">Every working ledger and Keeper instrument now lives inside the campaign that owns it.</p>
        </div>

        <?php if ($activeCampaigns === []) : ?>
            <article class="gmrc-dm-campaign-empty"><div aria-hidden="true">📜</div><h3>The first page is waiting</h3><p>Create a campaign to open its Command Centre, player roster, sessions, encounters and Keeper records.</p><a class="gmrc-dm-ledger__action" href="<?php echo esc_url($route('dungeon-master/campaigns/create')); ?>">Start New Campaign →</a></article>
        <?php else : ?>
            <div class="gmrc-dm-campaign-grid" aria-label="Active campaigns">
                <?php foreach ($activeCampaigns as $campaign) : ?>
                    <article class="gmrc-dm-campaign-card">
                        <div class="gmrc-dm-campaign-card__seal" aria-hidden="true">📜</div>
                        <div><p class="gmrc-dm-ledger__status">Campaign · Active</p><h3><?php echo esc_html($campaign->name()); ?></h3><p><?php echo esc_html($campaign->description() !== '' ? $campaign->description() : 'No campaign summary has been written yet.'); ?></p></div>
                        <a class="gmrc-dm-ledger__action" href="<?php echo esc_url($route('dungeon-master/campaigns/' . $campaign->id())); ?>">Open Campaign <span aria-hidden="true">→</span></a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="gmrc-dm-register-row">
            <a class="gmrc-dm-register-card" href="<?php echo esc_url($campaignUrl); ?>"><span aria-hidden="true">📚</span><span><strong>Campaign Register</strong><small>Manage active and archived campaigns.</small></span><span aria-hidden="true">→</span></a>
            <a class="gmrc-dm-register-card" href="<?php echo esc_url($route('dungeon-master/campaigns/create')); ?>"><span aria-hidden="true">✦</span><span><strong>Start New Campaign</strong><small>Open a fresh chronicle in the Register.</small></span><span aria-hidden="true">→</span></a>
        </div>

        <?php if ($archivedCampaigns !== []) : ?><p class="gmrc-dm-desk__archive-note"><?php echo esc_html((string) count($archivedCampaigns)); ?> archived campaign<?php echo count($archivedCampaigns) === 1 ? '' : 's'; ?> remain preserved in the Campaign Register.</p><?php endif; ?>
    </section>

    <section class="gmrc-dm-desk__quick" aria-labelledby="gmrc-dm-quick-title">
        <div class="gmrc-dm-desk__section-heading"><p class="gmrc-dm-desk__eyebrow">Guild Records</p><h2 id="gmrc-dm-quick-title">Outside the campaign</h2></div>
        <nav class="gmrc-dm-quick-links" aria-label="Dungeon Master quick links">
            <?php foreach (($quickLinks ?? []) as $link) : ?><a class="gmrc-dm-quick-link" href="<?php echo esc_url($route((string) $link['route'])); ?>"><span class="gmrc-dm-quick-link__mark" aria-hidden="true">✦</span><span class="gmrc-dm-quick-link__copy"><strong><?php echo esc_html((string) $link['label']); ?></strong><small><?php echo esc_html((string) $link['description']); ?></small></span><span class="gmrc-dm-quick-link__arrow" aria-hidden="true">→</span></a><?php endforeach; ?>
        </nav>
    </section>
</section>
