<?php defined('ABSPATH') || exit; $route='dungeon-master/campaigns/'.$campaign->id().'/sessions/'.$session->id(); ?>
<section class="gmrc-session-ledger gmrc-session-form"><p class="gmrc-dm-desk__eyebrow">Session <?php echo esc_html((string)$session->number()); ?> · <?php echo esc_html($campaign->name()); ?></p><h1>Edit Session Ledger Entry</h1><?php include __DIR__ . '/_form.php'; ?></section>
