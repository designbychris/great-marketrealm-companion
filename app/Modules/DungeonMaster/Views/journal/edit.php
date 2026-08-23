<?php defined('ABSPATH') || exit;$route='dungeon-master/campaigns/'.$campaign->id().'/journal/'.$entry->id(); ?>
<section class="gmrc-campaign-journal gmrc-journal-form"><header class="gmrc-campaign-journal__hero"><div><p class="gmrc-dm-desk__eyebrow">Campaign Journal · Edit</p><h1><?php echo esc_html($entry->title()); ?></h1></div></header><?php require __DIR__.'/_form.php'; ?></section>
