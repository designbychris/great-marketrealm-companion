<?php defined('ABSPATH') || exit; $base=home_url('/companion/'); $root='dungeon-master/campaigns/'.$campaign->id().'/sessions'; ?>
<section class="gmrc-session-ledger" aria-labelledby="gmrc-session-ledger-title">
<header class="gmrc-session-ledger__hero"><div><p class="gmrc-dm-desk__eyebrow">Ledger II · <?php echo esc_html($campaign->name()); ?></p><h1 id="gmrc-session-ledger-title">The Session Ledger</h1><p>Plan each gathering, keep the Dungeon Master’s notes, record who attended, and preserve what happened around the table.</p></div><?php if (! $campaign->isArchived()) : ?><a class="gmrc-session-button" href="<?php echo esc_url(add_query_arg('gmrc_route',$root.'/create',$base)); ?>">Record a Session</a><?php endif; ?></header>
<?php if (! empty($flash['success'])) : ?><p class="gmrc-session-flash" role="status"><?php echo esc_html($flash['success']); ?></p><?php endif; ?>
<?php if ($campaign->isArchived()) : ?><p class="gmrc-session-notice">This campaign is archived. Its Session Ledger is preserved as read-only history.</p><?php endif; ?>
<div class="gmrc-session-grid">
<?php if (($sessions ?? []) === []) : ?><article class="gmrc-session-card"><p class="gmrc-session-card__status">No entries yet</p><h2>The pages are waiting</h2><p>Your first planned or played session will appear here.</p></article><?php endif; ?>
<?php foreach (($sessions ?? []) as $entry) : ?><article class="gmrc-session-card gmrc-session-card--<?php echo esc_attr($entry->status()); ?>"><p class="gmrc-session-card__status">Session <?php echo esc_html((string)$entry->number()); ?> · <?php echo esc_html(ucfirst($entry->status())); ?></p><h2><?php echo esc_html($entry->title()); ?></h2><p><?php echo esc_html($entry->scheduledDate() !== '' ? $entry->scheduledDate() : 'Date not yet set'); ?></p><a href="<?php echo esc_url(add_query_arg('gmrc_route',$root.'/'.$entry->id(),$base)); ?>">Open Ledger Entry →</a></article><?php endforeach; ?>
</div>
<p class="gmrc-session-back"><a href="<?php echo esc_url(add_query_arg('gmrc_route','dungeon-master/campaigns/'.$campaign->id(),$base)); ?>">← Back to Campaign Chronicle</a></p>
</section>
