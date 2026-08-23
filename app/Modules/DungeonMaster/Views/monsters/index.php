<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$base = home_url('/companion/');
$createUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters/create', $base);
?>
<section class="gmrc-monster-ledger" aria-labelledby="gmrc-monster-ledger-title">
    <header class="gmrc-monster-ledger__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">Ledger V · Reusable adversaries</p>
            <h1 id="gmrc-monster-ledger-title">The Bestiary</h1>
            <p>Browse the canonical Marketrealm creatures beside your private Monster Ledger. Existing encounters preserve a snapshot, so later source or custom-stat changes do not rewrite past preparations.</p>
        </div>
        <a class="gmrc-monster-button" href="<?php echo esc_url($createUrl); ?>">Record Creature</a>
    </header>


    <section class="gmrc-canonical-bestiary" aria-labelledby="gmrc-canonical-bestiary-title">
        <header class="gmrc-canonical-bestiary__header">
            <div>
                <p class="gmrc-dm-desk__eyebrow">Phase III.15.6A · Dungeon Master Guide canon</p>
                <h2 id="gmrc-canonical-bestiary-title">Canonical Marketrealm Bestiary</h2>
                <p>Official creatures are read-only and shared by every Dungeon Master. Missing source statistics are shown as unknown rather than guessed.</p>
            </div>
            <span class="gmrc-canonical-bestiary__count"><?php echo esc_html((string) count($canonicalMonsters ?? [])); ?> canonical records</span>
        </header>
        <div class="gmrc-monster-grid">
            <?php foreach (($canonicalMonsters ?? []) as $monster) : ?>
                <?php $canonicalUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters/canonical/' . $monster->key(), $base); ?>
                <article class="gmrc-monster-card is-canonical">
                    <?php if ($monster->imageAttachmentId() > 0) : ?>
                        <a class="gmrc-canonical-bestiary__image" href="<?php echo esc_url($canonicalUrl); ?>" aria-label="Open <?php echo esc_attr($monster->name()); ?> Bestiary folio">
                            <?php echo wp_get_attachment_image($monster->imageAttachmentId(), 'medium', false, ['alt' => '']); ?>
                        </a>
                    <?php endif; ?>
                    <p class="gmrc-monster-card__status">Canonical · <?php echo $monster->encounterReady() ? 'Ready for encounters' : 'Reference only'; ?></p>
                    <h3><a href="<?php echo esc_url($canonicalUrl); ?>"><?php echo esc_html($monster->name()); ?></a></h3>
                    <p><?php echo esc_html(trim($monster->size() . ' ' . $monster->creatureType()) ?: 'Canonical creature'); ?></p>
                    <dl class="gmrc-monster-card__stats">
                        <div><dt>AC</dt><dd><?php echo esc_html($monster->armorClass() === null ? '—' : (string) $monster->armorClass()); ?></dd></div>
                        <div><dt>HP</dt><dd><?php echo esc_html($monster->maxHp() === null ? '—' : (string) $monster->maxHp()); ?></dd></div>
                        <div><dt>Init.</dt><dd><?php echo esc_html($monster->initiativeModifier() === null ? '—' : sprintf('%+d', $monster->initiativeModifier())); ?></dd></div>
                        <div><dt>CR</dt><dd><?php echo esc_html($monster->challenge() !== '' ? $monster->challenge() : '—'); ?></dd></div>
                    </dl>
                    <?php if ($monster->traits() !== '') : ?><p><strong>Traits:</strong> <?php echo esc_html($monster->traits()); ?></p><?php endif; ?>
                    <?php if ($monster->actions() !== '') : ?><p><strong>Actions:</strong> <?php echo esc_html($monster->actions()); ?></p><?php endif; ?>
                    <?php if ($monster->sourceIssue() !== '') : ?><p class="gmrc-canonical-bestiary__source-note"><strong>Source note:</strong> <?php echo esc_html($monster->sourceIssue()); ?></p><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (! empty($flash['success'])) : ?>
        <p class="gmrc-monster-notice" role="status"><?php echo esc_html((string) $flash['success']); ?></p>
    <?php endif; ?>

    <?php if (($monsters ?? []) === []) : ?>
        <section class="gmrc-monster-empty">
            <h2>The shelves are waiting</h2>
            <p>No reusable creatures have been recorded yet. Add your own Marketrealm monsters without inventing canonical statistics for creatures that have not yet been entered.</p>
            <a class="gmrc-monster-button" href="<?php echo esc_url($createUrl); ?>">Record first creature</a>
        </section>
    <?php else : ?>
        <div class="gmrc-monster-grid">
            <?php foreach ($monsters as $monster) :
                $url = add_query_arg('gmrc_route', 'dungeon-master/monsters/' . $monster->id(), $base);
                ?>
                <article class="gmrc-monster-card<?php echo $monster->isArchived() ? ' is-archived' : ''; ?>">
                    <p class="gmrc-monster-card__status"><?php echo esc_html($monster->isArchived() ? 'Archived' : 'Ready for encounters'); ?></p>
                    <h2><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($monster->name()); ?></a></h2>
                    <p><?php echo esc_html(trim($monster->size() . ' ' . $monster->creatureType()) ?: 'Unclassified creature'); ?></p>
                    <dl class="gmrc-monster-card__stats">
                        <div><dt>AC</dt><dd><?php echo esc_html((string) $monster->armorClass()); ?></dd></div>
                        <div><dt>HP</dt><dd><?php echo esc_html((string) $monster->maxHp()); ?></dd></div>
                        <div><dt>Init.</dt><dd><?php echo esc_html(sprintf('%+d', $monster->initiativeModifier())); ?></dd></div>
                        <div><dt>Threat</dt><dd><?php echo esc_html($monster->challenge() !== '' ? $monster->challenge() : '—'); ?></dd></div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
