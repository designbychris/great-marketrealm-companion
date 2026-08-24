<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$campaigns = is_array($campaigns ?? null) ? $campaigns : [];
$flash = is_array($flash ?? null) ? $flash : [];
$companionUrl = home_url('/companion/');
$marketPassUrl = add_query_arg('gmrc_route', 'market-pass', $companionUrl);
$dashboardUrl = add_query_arg('gmrc_route', 'dashboard', $companionUrl);
$action = admin_url('admin-post.php');
?>
<section class="gmrc-active-campaigns" aria-labelledby="gmrc-active-campaigns-title">
    <header class="gmrc-active-campaigns__hero">
        <p class="gmrc-active-campaigns__eyebrow">Your Adventuring Tables</p>
        <h1 id="gmrc-active-campaigns-title">Active Campaigns</h1>
        <p>Campaigns you have joined with a Market Pass live here. Choose the adventurer you are bringing to each table; your Dungeon Master keeps the private campaign records.</p>
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
                <?php
                $campaignId = (string) $campaign['id'];
                $characters = is_array($campaign['characters'] ?? null) ? $campaign['characters'] : [];
                $assigned = is_array($campaign['assigned_characters'] ?? null) ? $campaign['assigned_characters'] : [];
                $assignedIds = [];
                foreach ($assigned as $character) {
                    $assignedIds[$character->id()->value()] = true;
                }
                $assignmentRoute = 'active-campaigns/' . $campaignId . '/adventurer';
                ?>
                <article class="gmrc-active-campaign" aria-labelledby="campaign-<?php echo esc_attr($campaignId); ?>">
                    <div class="gmrc-active-campaign__status <?php echo ! empty($campaign['is_archived']) ? 'is-closed' : 'is-active'; ?>">
                        <?php echo ! empty($campaign['is_archived']) ? 'Campaign closed' : 'At the table'; ?>
                    </div>
                    <p class="gmrc-active-campaign__eyebrow">Dungeon Master: <?php echo esc_html((string) $campaign['dungeon_master']); ?></p>
                    <h2 id="campaign-<?php echo esc_attr($campaignId); ?>"><?php echo esc_html((string) $campaign['name']); ?></h2>

                    <?php if ((string) $campaign['description'] !== '') : ?>
                        <p class="gmrc-active-campaign__description"><?php echo esc_html((string) $campaign['description']); ?></p>
                    <?php endif; ?>

                    <?php if ((string) ($campaign['fellowship_name'] ?? '') !== '') : ?>
                        <div class="gmrc-active-campaign__fellowship">
                            <span aria-hidden="true">🤝</span>
                            <div>
                                <strong>Campaign Fellowship</strong>
                                <p><?php echo esc_html((string) $campaign['fellowship_name']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <section class="gmrc-active-campaign__adventurer" aria-labelledby="adventurer-<?php echo esc_attr($campaignId); ?>">
                        <span aria-hidden="true">⚔</span>
                        <div class="gmrc-active-campaign__adventurer-body">
                            <strong id="adventurer-<?php echo esc_attr($campaignId); ?>">Your Campaign Adventurer</strong>

                            <?php if ($assigned !== []) : ?>
                                <?php $current = $assigned[0]; ?>
                                <p class="gmrc-active-campaign__current-character">
                                    <strong><?php echo esc_html($current->name()->value()); ?></strong>
                                    <span><?php echo esc_html($current->race()->label()); ?> · <?php echo esc_html($current->characterClass()->label()); ?> · Level <?php echo esc_html((string) $current->level()->value()); ?></span>
                                </p>
                            <?php else : ?>
                                <p>No adventurer nominated yet. Choose one of your registered Characters below.</p>
                            <?php endif; ?>

                            <?php if (empty($campaign['is_archived'])) : ?>
                                <?php if ($characters === []) : ?>
                                    <p class="gmrc-active-campaign__assignment-note">You do not have a registered Character available yet. Create one first, then return here to nominate them.</p>
                                <?php else : ?>
                                    <form class="gmrc-active-campaign__assignment" method="post" action="<?php echo esc_url($action); ?>">
                                        <input type="hidden" name="action" value="gmrc_app_request">
                                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($assignmentRoute); ?>">
                                        <?php wp_nonce_field('gmrc_active_campaign_character_' . $campaignId, 'gmrc_nonce'); ?>
                                        <label for="gmrc-campaign-character-<?php echo esc_attr($campaignId); ?>">Choose your adventurer</label>
                                        <div class="gmrc-active-campaign__assignment-row">
                                            <select id="gmrc-campaign-character-<?php echo esc_attr($campaignId); ?>" name="character_id" required>
                                                <option value="">Select an adventurer…</option>
                                                <?php foreach ($characters as $character) : ?>
                                                    <?php $characterId = $character->id()->value(); ?>
                                                    <option value="<?php echo esc_attr($characterId); ?>" <?php selected(isset($assignedIds[$characterId])); ?>>
                                                        <?php echo esc_html($character->name()->value()); ?> — <?php echo esc_html($character->characterClass()->label()); ?>, Level <?php echo esc_html((string) $character->level()->value()); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit"><?php echo $assigned === [] ? 'Nominate adventurer' : 'Change adventurer'; ?></button>
                                        </div>
                                    </form>
                                <?php endif; ?>

                                <?php if ($assigned !== []) : ?>
                                    <form class="gmrc-active-campaign__clear" method="post" action="<?php echo esc_url($action); ?>">
                                        <input type="hidden" name="action" value="gmrc_app_request">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($assignmentRoute); ?>">
                                        <?php wp_nonce_field('gmrc_active_campaign_character_' . $campaignId, 'gmrc_nonce'); ?>
                                        <button type="submit">Clear nomination</button>
                                    </form>
                                <?php endif; ?>
                            <?php else : ?>
                                <p class="gmrc-active-campaign__assignment-note">This Campaign is closed, so its final adventurer assignment is preserved as history.</p>
                            <?php endif; ?>
                        </div>
                    </section>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <nav class="gmrc-active-campaigns__actions" aria-label="Campaign actions">
        <a href="<?php echo esc_url($marketPassUrl); ?>">Redeem another Market Pass</a>
        <a href="<?php echo esc_url($dashboardUrl); ?>">← Return to the Guild Hall</a>
    </nav>
</section>
