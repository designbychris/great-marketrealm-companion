<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$campaigns = is_array($campaigns ?? null) ? $campaigns : [];
$flash = is_array($flash ?? null) ? $flash : [];
$companionUrl = home_url('/companion/');
$marketPassUrl = add_query_arg('gmrc_route', 'market-pass', $companionUrl);
$dashboardUrl = add_query_arg('gmrc_route', 'dashboard', $companionUrl);
?>
<section class="gmrc-active-campaigns" aria-labelledby="gmrc-active-campaigns-title">
    <header class="gmrc-active-campaigns__hero">
        <p class="gmrc-active-campaigns__eyebrow">Your Adventuring Tables</p>
        <h1 id="gmrc-active-campaigns-title">Active Campaigns</h1>
        <p>Campaigns you have joined with a Market Pass live here. Your Dungeon Master keeps the private campaign records; this page shows only your place at the table.</p>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-active-campaigns__notice is-success" role="status"><?php echo esc_html((string) $flash['success']); ?></div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-active-campaigns__notice is-error" role="alert"><?php echo esc_html((string) $flash['error']); ?></div>
    <?php endif; ?>

    <?php if ($campaigns === []) : ?>
        <div class="gmrc-active-campaigns__empty">
            <span aria-hidden="true">🎟</span>
            <h2>No Campaigns joined yet</h2>
            <p>When a Dungeon Master gives you a Market Pass, redeem it and your Campaign will appear here.</p>
            <a href="<?php echo esc_url($marketPassUrl); ?>">Redeem a Market Pass →</a>
        </div>
    <?php else : ?>
        <div class="gmrc-active-campaigns__grid">
            <?php foreach ($campaigns as $campaign) : ?>
                <article class="gmrc-active-campaign" aria-labelledby="campaign-<?php echo esc_attr((string) $campaign['id']); ?>">
                    <div class="gmrc-active-campaign__status <?php echo ! empty($campaign['is_archived']) ? 'is-closed' : 'is-active'; ?>">
                        <?php echo ! empty($campaign['is_archived']) ? 'Campaign closed' : 'At the table'; ?>
                    </div>
                    <p class="gmrc-active-campaign__eyebrow">Dungeon Master: <?php echo esc_html((string) $campaign['dungeon_master']); ?></p>
                    <h2 id="campaign-<?php echo esc_attr((string) $campaign['id']); ?>"><?php echo esc_html((string) $campaign['name']); ?></h2>

                    <?php if ((string) $campaign['description'] !== '') : ?>
                        <p class="gmrc-active-campaign__description"><?php echo esc_html((string) $campaign['description']); ?></p>
                    <?php endif; ?>

                    <div class="gmrc-active-campaign__adventurer">
                        <span aria-hidden="true">⚔</span>
                        <div>
                            <strong>Adventurer assignment</strong>
                            <?php if ((int) $campaign['character_count'] > 0) : ?>
                                <p>An adventurer is already recorded with this Campaign. Character assignment will move into the new nomination workflow next.</p>
                            <?php else : ?>
                                <p>No adventurer nominated yet. You are safely joined as a Player; choosing who you bring to the table comes next.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <nav class="gmrc-active-campaigns__actions" aria-label="Campaign actions">
        <a href="<?php echo esc_url($marketPassUrl); ?>">Redeem another Market Pass</a>
        <a href="<?php echo esc_url($dashboardUrl); ?>">← Return to the Guild Hall</a>
    </nav>
</section>
