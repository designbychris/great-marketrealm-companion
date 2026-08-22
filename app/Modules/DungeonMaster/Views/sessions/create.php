<?php defined('ABSPATH') || exit; $route='dungeon-master/campaigns/'.$campaign->id().'/sessions'; ?>
<section class="gmrc-session-ledger gmrc-session-form"><p class="gmrc-dm-desk__eyebrow">Session Ledger · <?php echo esc_html($campaign->name()); ?></p><h1>Record a Session</h1><p>Prepare the next gathering or enter a session already played.</p><?php include __DIR__ . '/_form.php'; ?></section>
