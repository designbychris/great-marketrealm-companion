<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$members = is_array($members ?? null) ? $members : [];
$flash = is_array($flash ?? null) ? $flash : [];
$availableFellowships = is_array($availableFellowships ?? null) ? $availableFellowships : [];
$campaignFellowship = $campaignFellowship ?? null;
$base = home_url('/companion/');
$action = admin_url('admin-post.php');
$campaignId = $campaign->id();
$campaignUrl = add_query_arg(
    'gmrc_route',
    'dungeon-master/campaigns/' . $campaignId,
    $base
);
?>
<section class="gmrc-player-roster" aria-labelledby="gmrc-player-roster-title">
    <header class="gmrc-player-roster__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">Campaign Ledger · Player Roster</p>
            <h1 id="gmrc-player-roster-title"><?php echo esc_html($campaign->name()); ?></h1>
            <p>Gather registered Guild Players around this campaign and attach the adventurers who will carry its story.</p>
        </div>
        <a class="gmrc-campaign-button" href="<?php echo esc_url($campaignUrl); ?>">Back to campaign</a>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-player-roster__notice is-success" role="status"><?php echo esc_html((string) $flash['success']); ?></div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-player-roster__notice is-error" role="alert"><?php echo esc_html((string) $flash['error']); ?></div>
    <?php endif; ?>

    <?php if (! $campaign->isArchived()) : ?>
    <section class="gmrc-market-pass-admin" aria-labelledby="gmrc-market-pass-admin-title">
        <p class="gmrc-player-roster__kicker">Market Pass</p>
        <h2 id="gmrc-market-pass-admin-title">Invite Players by Code</h2>
        <p>Issue a short-lived Market Pass and share it with your players. Redeeming the code joins their Guild account to this Campaign Roster; it does not expose usernames or email addresses.</p>

        <?php if ($marketPass instanceof \GreatMarketrealmCompanion\Modules\DungeonMaster\Models\MarketPass && $marketPass->isRedeemable()) : ?>
            <p><span class="gmrc-market-pass-admin__code" aria-label="Current Market Pass"><?php echo esc_html($marketPass->code()); ?></span></p>
            <p>Valid until <?php echo esc_html(wp_date('j M Y, H:i', $marketPass->expiresAt())); ?>. Rotating the code does not remove Players who have already joined.</p>
            <div class="gmrc-market-pass-admin__actions">
                <button type="button" data-market-pass-copy="<?php echo esc_attr($marketPass->code()); ?>">Copy code</button>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/market-pass">
                    <?php wp_nonce_field('gmrc_dm_market_pass_' . $campaignId, 'gmrc_nonce'); ?>
                    <button type="submit">Rotate Market Pass</button>
                </form>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/market-pass">
                    <?php wp_nonce_field('gmrc_dm_market_pass_' . $campaignId, 'gmrc_nonce'); ?>
                    <button type="submit">Revoke Market Pass</button>
                </form>
            </div>
        <?php else : ?>
            <p>No active Market Pass is currently open for this Campaign.</p>
            <form method="post" action="<?php echo esc_url($action); ?>">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/market-pass">
                <?php wp_nonce_field('gmrc_dm_market_pass_' . $campaignId, 'gmrc_nonce'); ?>
                <button class="gmrc-campaign-button" type="submit">Issue Market Pass</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="gmrc-player-roster__invite" aria-labelledby="gmrc-roster-add-title">
        <div>
            <p class="gmrc-player-roster__kicker">Guild Registry</p>
            <h2 id="gmrc-roster-add-title">Direct Player Add</h2>
            <p>Market Pass is the normal invitation route. This exact username/email form remains available as a Dungeon Master fallback.</p>
        </div>
        <form method="post" action="<?php echo esc_url($action); ?>">
            <input type="hidden" name="action" value="gmrc_app_request">
            <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/players">
            <?php wp_nonce_field('gmrc_dm_roster_' . $campaignId, 'gmrc_nonce'); ?>
            <label for="gmrc-roster-guild-identity">Guild username or email</label>
            <div class="gmrc-player-roster__invite-row">
                <input id="gmrc-roster-guild-identity" name="guild_identity" type="text" maxlength="100" autocomplete="off" required>
                <button class="gmrc-campaign-button" type="submit">Add to roster</button>
            </div>
        </form>
    </section>
    <?php else : ?>
        <div class="gmrc-player-roster__notice" role="status">This campaign is archived. Its Player Roster is preserved as a read-only Guild record.</div>
    <?php endif; ?>

    <section class="gmrc-campaign-fellowship" aria-labelledby="gmrc-campaign-fellowship-title">
        <div class="gmrc-player-roster__heading">
            <div>
                <p class="gmrc-player-roster__kicker">Adventuring Company</p>
                <h2 id="gmrc-campaign-fellowship-title">Campaign Fellowship</h2>
            </div>
        </div>

        <?php if ($campaignFellowship instanceof \GreatMarketrealmCompanion\Modules\Parties\Models\Party) : ?>
            <div class="gmrc-campaign-fellowship__linked">
                <div>
                    <strong><?php echo esc_html($campaignFellowship->name()->value()); ?></strong>
                    <p>This Fellowship is linked to the Campaign. Roster changes do not silently rewrite Fellowship membership.</p>
                </div>
                <a class="gmrc-campaign-button" href="<?php echo esc_url(add_query_arg('gmrc_route', 'parties/' . $campaignFellowship->id()->value(), $base)); ?>">View Fellowship</a>
                <?php if (! $campaign->isArchived()) : ?>
                    <form method="post" action="<?php echo esc_url($action); ?>">
                        <input type="hidden" name="action" value="gmrc_app_request">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/fellowship">
                        <?php wp_nonce_field('gmrc_dm_campaign_fellowship_' . $campaignId, 'gmrc_nonce'); ?>
                        <button type="submit">Release Fellowship link</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php elseif (! $campaign->isArchived()) : ?>
            <p>Once Players have nominated their Campaign adventurers, you can found a Fellowship from that company or link one already in your Fellowship Register.</p>
            <div class="gmrc-campaign-fellowship__choices">
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/fellowship">
                    <?php wp_nonce_field('gmrc_dm_campaign_fellowship_' . $campaignId, 'gmrc_nonce'); ?>
                    <button class="gmrc-campaign-button" type="submit">Found Fellowship from roster</button>
                </form>

                <?php if ($availableFellowships !== []) : ?>
                    <form method="post" action="<?php echo esc_url($action); ?>">
                        <input type="hidden" name="action" value="gmrc_app_request">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/fellowship">
                        <?php wp_nonce_field('gmrc_dm_campaign_fellowship_' . $campaignId, 'gmrc_nonce'); ?>
                        <label for="gmrc-campaign-fellowship-select">Link an existing Fellowship</label>
                        <div class="gmrc-campaign-fellowship__select-row">
                            <select id="gmrc-campaign-fellowship-select" name="party_id" required>
                                <option value="">Choose a Fellowship…</option>
                                <?php foreach ($availableFellowships as $fellowship) : ?>
                                    <option value="<?php echo esc_attr($fellowship->id()->value()); ?>"><?php echo esc_html($fellowship->name()->value()); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Link Fellowship</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <p class="gmrc-campaign-fellowship__note">Founding copies the currently nominated adventurers once. New Campaign Players are never added to the Fellowship automatically.</p>
        <?php else : ?>
            <p>No Fellowship was linked before this Campaign was closed.</p>
        <?php endif; ?>
    </section>

    <section class="gmrc-player-roster__ledger" aria-labelledby="gmrc-roster-ledger-title">
        <div class="gmrc-player-roster__heading">
            <div>
                <p class="gmrc-player-roster__kicker">Adventuring Company</p>
                <h2 id="gmrc-roster-ledger-title">Campaign Players</h2>
            </div>
            <span><?php echo esc_html((string) count($members)); ?> rostered</span>
        </div>

        <?php if ($members === []) : ?>
            <div class="gmrc-player-roster__empty">
                <strong>No Players have signed this campaign ledger yet.</strong>
                <p>Add an existing Guild Player above to begin forming the adventuring company.</p>
            </div>
        <?php else : ?>
            <div class="gmrc-player-roster__grid">
                <?php foreach ($members as $member) : ?>
                    <?php
                    $user = $member['user'];
                    $playerId = (int) $user->ID;
                    $portraitId = (int) $member['portrait_id'];
                    $portrait = $portraitId > 0
                        ? wp_get_attachment_image($portraitId, [96, 96], false, ['class' => 'gmrc-player-roster__portrait-image', 'alt' => ''])
                        : get_avatar($playerId, 96, '', '', ['class' => 'gmrc-player-roster__portrait-image']);
                    $linked = array_fill_keys($member['linked_character_ids'], true);
                    ?>
                    <article class="gmrc-player-roster__member">
                        <header class="gmrc-player-roster__member-header">
                            <div class="gmrc-player-roster__portrait" aria-hidden="true"><?php echo $portrait; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                            <div>
                                <p class="gmrc-player-roster__kicker">Player</p>
                                <h3><?php echo esc_html((string) $user->display_name); ?></h3>
                                <small>@<?php echo esc_html((string) $user->user_login); ?></small>
                            </div>
                        </header>

                        <?php if ($member['bio'] !== '') : ?>
                            <p class="gmrc-player-roster__bio"><?php echo esc_html((string) $member['bio']); ?></p>
                        <?php endif; ?>

                        <div class="gmrc-player-roster__characters">
                            <h4>Adventurers</h4>
                            <?php if ($member['characters'] === []) : ?>
                                <p>This Player has not registered a Character yet.</p>
                            <?php else : ?>
                                <ul>
                                    <?php foreach ($member['characters'] as $character) : ?>
                                        <?php
                                        $characterId = $character->id()->value();
                                        $isLinked = isset($linked[$characterId]);
                                        $characterRoute = 'dungeon-master/campaigns/' . $campaignId . '/players/' . $playerId . '/characters/' . $characterId;
                                        ?>
                                        <li class="<?php echo $isLinked ? 'is-linked' : ''; ?>">
                                            <div>
                                                <strong><?php echo esc_html($character->name()->value()); ?></strong>
                                                <span><?php echo esc_html($character->race()->label()); ?> · <?php echo esc_html($character->characterClass()->label()); ?> · Level <?php echo esc_html((string) $character->level()->value()); ?></span>
                                            </div>
                                            <?php if (! $campaign->isArchived()) : ?>
                                                <form method="post" action="<?php echo esc_url($action); ?>">
                                                    <input type="hidden" name="action" value="gmrc_app_request">
                                                    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($characterRoute); ?>">
                                                    <?php if ($isLinked) : ?><input type="hidden" name="_method" value="DELETE"><?php endif; ?>
                                                    <?php wp_nonce_field('gmrc_dm_roster_' . $campaignId, 'gmrc_nonce'); ?>
                                                    <button type="submit"><?php echo $isLinked ? 'Detach' : 'Attach'; ?></button>
                                                </form>
                                            <?php elseif ($isLinked) : ?>
                                                <span class="gmrc-player-roster__linked-label">Attached</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if (! $campaign->isArchived()) : ?>
                            <form class="gmrc-player-roster__remove" method="post" action="<?php echo esc_url($action); ?>">
                                <input type="hidden" name="action" value="gmrc_app_request">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="gmrc_route" value="dungeon-master/campaigns/<?php echo esc_attr($campaignId); ?>/players/<?php echo esc_attr((string) $playerId); ?>">
                                <?php wp_nonce_field('gmrc_dm_roster_' . $campaignId, 'gmrc_nonce'); ?>
                                <button type="submit">Remove Player from campaign</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
