<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$base = home_url('/companion/');
$editUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters/' . $monster->id() . '/edit', $base);
$registerUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters', $base);
?>
<section class="gmrc-monster-ledger gmrc-monster-sheet" aria-labelledby="gmrc-monster-title">
    <header class="gmrc-monster-ledger__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">Monster Ledger · <?php echo esc_html($monster->isArchived() ? 'Archived' : 'Ready'); ?></p>
            <h1 id="gmrc-monster-title"><?php echo esc_html($monster->name()); ?></h1>
            <p><?php echo esc_html(trim($monster->size() . ' ' . $monster->creatureType()) ?: 'Unclassified creature'); ?><?php echo $monster->challenge() !== '' ? ' · ' . esc_html($monster->challenge()) : ''; ?></p>
        </div>
        <div class="gmrc-monster-sheet__actions">
            <a class="gmrc-monster-button" href="<?php echo esc_url($registerUrl); ?>">Back to Ledger</a>
            <?php if (! $monster->isArchived()) : ?><a class="gmrc-monster-button" href="<?php echo esc_url($editUrl); ?>">Edit Stat Block</a><?php endif; ?>
        </div>
    </header>

    <?php if (! empty($flash['success'])) : ?><p class="gmrc-monster-notice" role="status"><?php echo esc_html((string) $flash['success']); ?></p><?php endif; ?>

    <section class="gmrc-monster-sheet__combat" aria-label="Core combat statistics">
        <div><span>Armor Class</span><strong><?php echo esc_html((string) $monster->armorClass()); ?></strong></div>
        <div><span>Hit Points</span><strong><?php echo esc_html((string) $monster->maxHp()); ?></strong></div>
        <div><span>Initiative</span><strong><?php echo esc_html(sprintf('%+d', $monster->initiativeModifier())); ?></strong></div>
        <div><span>Speed</span><strong><?php echo esc_html($monster->speed() !== '' ? $monster->speed() : '—'); ?></strong></div>
    </section>

    <section class="gmrc-monster-ability-line" aria-label="Ability scores">
        <?php foreach ([
            'STR' => $monster->strength(), 'DEX' => $monster->dexterity(), 'CON' => $monster->constitution(),
            'INT' => $monster->intelligence(), 'WIS' => $monster->wisdom(), 'CHA' => $monster->charisma(),
        ] as $label => $score) : ?>
            <div><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html((string) $score); ?></strong></div>
        <?php endforeach; ?>
    </section>

    <div class="gmrc-monster-sheet__details">
        <section><h2>Traits</h2><p><?php echo nl2br(esc_html($monster->traits() !== '' ? $monster->traits() : 'No traits recorded.')); ?></p></section>
        <section><h2>Actions</h2><p><?php echo nl2br(esc_html($monster->actions() !== '' ? $monster->actions() : 'No actions recorded.')); ?></p></section>
        <section><h2>DM Notes</h2><p><?php echo nl2br(esc_html($monster->notes() !== '' ? $monster->notes() : 'No private notes recorded.')); ?></p></section>
    </div>

    <?php if (! $monster->isArchived()) : ?>
        <form class="gmrc-monster-archive" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="gmrc_app_request">
            <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('dungeon-master/monsters/' . $monster->id() . '/archive'); ?>">
            <input type="hidden" name="_method" value="POST">
            <?php wp_nonce_field('gmrc_dm_monster_' . $monster->id(), 'gmrc_nonce'); ?>
            <button type="submit">Archive creature</button>
        </form>
    <?php endif; ?>
</section>
