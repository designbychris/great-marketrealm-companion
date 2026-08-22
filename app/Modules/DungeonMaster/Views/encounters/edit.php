<?php defined('ABSPATH') || exit; $route='dungeon-master/campaigns/'.$campaign->id().'/encounters/'.$encounter->id(); ?>
<section class="gmrc-encounter-board gmrc-encounter-form"><header><p class="gmrc-dm-desk__eyebrow">Encounter Board · <?php echo esc_html($campaign->name()); ?></p><h1>Edit <?php echo esc_html($encounter->title()); ?></h1></header><?php require __DIR__.'/_form.php'; ?></section>
