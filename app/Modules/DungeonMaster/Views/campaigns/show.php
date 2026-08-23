<?php
defined('ABSPATH') || exit;
$base = home_url('/companion/');
$route = static fn (string $path): string => add_query_arg('gmrc_route', $path, $base);
$campaignPath = 'dungeon-master/campaigns/' . $campaign->id();
$nextSession = $commandCentre['nextSession'];
$recentSession = $commandCentre['recentSession'];
$liveEncounter = $commandCentre['liveEncounter'];
$preparedEncounter = $commandCentre['preparedEncounter'];
?>
<section class="gmrc-command-centre" aria-labelledby="gmrc-command-centre-title">
    <header class="gmrc-command-centre__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">Campaign Command Centre · <?php echo esc_html(ucfirst($campaign->status())); ?></p>
            <h1 id="gmrc-command-centre-title"><?php echo esc_html($campaign->name()); ?></h1>
            <p><?php echo nl2br(esc_html($campaign->description() ?: 'No chronicle summary has been written yet.')); ?></p>
        </div>
        <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/edit')); ?>">Edit campaign</a>
    </header>

    <div class="gmrc-command-centre__stats" aria-label="Campaign overview">
        <a href="<?php echo esc_url($route($campaignPath . '/players')); ?>"><strong><?php echo esc_html((string) $commandCentre['playerCount']); ?></strong><span>Players</span></a>
        <a href="<?php echo esc_url($route($campaignPath . '/players')); ?>"><strong><?php echo esc_html((string) $commandCentre['characterCount']); ?></strong><span>Characters</span></a>
        <a href="<?php echo esc_url($route($campaignPath . '/sessions')); ?>"><strong><?php echo esc_html((string) $commandCentre['sessionCount']); ?></strong><span>Sessions</span></a>
        <a href="<?php echo esc_url($route($campaignPath . '/encounters')); ?>"><strong><?php echo esc_html((string) $commandCentre['encounterCount']); ?></strong><span>Encounters</span></a>
        <a href="<?php echo esc_url($route($campaignPath . '/journal')); ?>"><strong><?php echo esc_html((string) $commandCentre['journalCount']); ?></strong><span>Journal pages</span></a>
    </div>

    <div class="gmrc-command-centre__grid">
        <article class="gmrc-command-card gmrc-command-card--session">
            <p class="gmrc-dm-desk__eyebrow">At the table</p>
            <h2><?php echo $nextSession ? 'Next Session' : 'Session Ledger'; ?></h2>
            <?php if ($nextSession) : ?>
                <h3><?php echo esc_html('Session ' . $nextSession->number() . ' · ' . $nextSession->title()); ?></h3>
                <p><?php echo esc_html($nextSession->scheduledDate() ?: 'Date not yet set'); ?></p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/sessions/' . $nextSession->id())); ?>">Open session</a>
            <?php elseif ($recentSession) : ?>
                <p>Your latest played session is <strong><?php echo esc_html($recentSession->title()); ?></strong>. The next page of the chronicle is ready to plan.</p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/sessions/create')); ?>">Plan next session</a>
            <?php else : ?>
                <p>No session has been planned yet.</p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/sessions/create')); ?>">Plan first session</a>
            <?php endif; ?>
        </article>

        <article class="gmrc-command-card gmrc-command-card--combat">
            <p class="gmrc-dm-desk__eyebrow">Combat readiness</p>
            <?php if ($liveEncounter) : ?>
                <h2>Encounter in progress</h2><h3><?php echo esc_html($liveEncounter->title()); ?></h3>
                <p><?php echo esc_html(ucfirst($liveEncounter->threat()) . ' threat · ' . ($liveEncounter->location() ?: 'Location unrecorded')); ?></p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/encounters/' . $liveEncounter->id() . '/initiative')); ?>">Continue combat</a>
            <?php elseif ($preparedEncounter) : ?>
                <h2>Prepared Encounter</h2><h3><?php echo esc_html($preparedEncounter->title()); ?></h3>
                <p><?php echo esc_html(ucfirst($preparedEncounter->threat()) . ' threat · ready when the party arrives.'); ?></p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/encounters/' . $preparedEncounter->id() . '/initiative')); ?>">Run encounter</a>
            <?php else : ?>
                <h2>Encounter Board</h2><p>No encounter is currently waiting in the wings.</p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/encounters/create')); ?>">Prepare encounter</a>
            <?php endif; ?>
        </article>

        <article class="gmrc-command-card gmrc-command-card--journal">
            <p class="gmrc-dm-desk__eyebrow">Pinned intelligence</p><h2>Campaign Journal</h2>
            <?php if ($commandCentre['pinnedJournal']) : ?>
                <ul class="gmrc-command-centre__notes">
                    <?php foreach ($commandCentre['pinnedJournal'] as $entry) : ?>
                        <li><a href="<?php echo esc_url($route($campaignPath . '/journal/' . $entry->id())); ?>"><span><?php echo esc_html($entry->categoryLabel()); ?></span><strong><?php echo esc_html($entry->title()); ?></strong></a></li>
                    <?php endforeach; ?>
                </ul>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/journal')); ?>">Open journal</a>
            <?php else : ?>
                <p>Pin important NPCs, secrets, locations or plot threads and they will surface here.</p>
                <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/journal/create')); ?>">Write campaign note</a>
            <?php endif; ?>
        </article>

        <article class="gmrc-command-card gmrc-command-card--roster">
            <p class="gmrc-dm-desk__eyebrow">Party muster</p><h2>Player Roster</h2>
            <p><strong><?php echo esc_html((string) $commandCentre['playerCount']); ?></strong> players and <strong><?php echo esc_html((string) $commandCentre['characterCount']); ?></strong> attached characters are recorded for this campaign.</p>
            <a class="gmrc-campaign-button" href="<?php echo esc_url($route($campaignPath . '/players')); ?>"><?php echo $commandCentre['playerCount'] ? 'Review roster' : 'Add players'; ?></a>
        </article>
    </div>

    <nav class="gmrc-command-centre__tools" aria-label="Campaign ledgers">
        <a href="<?php echo esc_url($route($campaignPath . '/players')); ?>">Open Player Roster</a>
        <a href="<?php echo esc_url($route($campaignPath . '/sessions')); ?>">Open Session Ledger</a>
        <a href="<?php echo esc_url($route($campaignPath . '/encounters')); ?>">Open Encounter Board</a>
        <a href="<?php echo esc_url($route($campaignPath . '/journal')); ?>">Open Campaign Journal</a>
        <a href="<?php echo esc_url($route('dungeon-master/monsters')); ?>">Bestiary</a>
    </nav>
</section>
