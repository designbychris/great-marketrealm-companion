<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$members = is_array($members ?? null) ? $members : [];
$flash = is_array($flash ?? null) ? $flash : [];
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
    <section class="gmrc-player-roster__invite" aria-labelledby="gmrc-roster-add-title">
        <div>
            <p class="gmrc-player-roster__kicker">Guild Registry</p>
            <h2 id="gmrc-roster-add-title">Add a Player</h2>
            <p>Enter the exact Guild username or email of an existing Player account. Dungeon Master accounts are kept separate from the Player Roster.</p>
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
