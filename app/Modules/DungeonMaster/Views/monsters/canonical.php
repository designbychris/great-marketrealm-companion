<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$base = home_url('/companion/');
$registerUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters', $base);
$imageId = $monster->imageAttachmentId();
$subtitle = trim(implode(' · ', array_filter([
    trim($monster->size() . ' ' . $monster->creatureType()),
    $monster->alignment(),
    $monster->challenge() !== '' ? 'CR ' . $monster->challenge() : '',
])));
?>
<section class="gmrc-monster-ledger gmrc-monster-sheet gmrc-canonical-folio" aria-labelledby="gmrc-monster-title">
    <header class="gmrc-monster-ledger__hero">
        <div><p class="gmrc-dm-desk__eyebrow">Canonical Marketrealm Bestiary · Field Folio</p><h1 id="gmrc-monster-title"><?php echo esc_html($monster->name()); ?></h1><p><?php echo esc_html($subtitle !== '' ? $subtitle : 'Canonical creature'); ?></p></div>
        <a class="gmrc-monster-button" href="<?php echo esc_url($registerUrl); ?>">Back to Bestiary</a>
    </header>

    <div class="gmrc-canonical-folio__page">
        <aside class="gmrc-canonical-folio__portrait">
            <?php if ($imageId > 0) : ?>
                <?php echo wp_get_attachment_image($imageId, 'large', false, ['alt' => '']); ?>
            <?php else : ?>
                <div class="gmrc-canonical-folio__placeholder" aria-label="Illustration not yet assigned"><span aria-hidden="true">📖</span><strong>Illustration awaiting the Steward</strong></div>
            <?php endif; ?>
        </aside>

        <div class="gmrc-canonical-folio__record">
            <section class="gmrc-monster-sheet__combat" aria-label="Core combat statistics">
                <div><span>Armor Class</span><strong><?php echo esc_html($monster->armorClass() === null ? '—' : (string) $monster->armorClass()); ?></strong><?php if ($monster->armorDescription() !== '') : ?><small><?php echo esc_html($monster->armorDescription()); ?></small><?php endif; ?></div>
                <div><span>Hit Points</span><strong><?php echo esc_html($monster->maxHp() === null ? '—' : (string) $monster->maxHp()); ?></strong><?php if ($monster->hpFormula() !== '') : ?><small><?php echo esc_html('(' . $monster->hpFormula() . ')'); ?></small><?php endif; ?></div>
                <div><span>Initiative</span><strong><?php echo esc_html($monster->initiativeModifier() === null ? '—' : sprintf('%+d', $monster->initiativeModifier())); ?></strong></div>
                <div><span>Speed</span><strong><?php echo esc_html($monster->speed() !== '' ? $monster->speed() : '—'); ?></strong></div>
            </section>

            <section class="gmrc-monster-ability-line" aria-label="Ability scores">
                <?php foreach (['STR' => $monster->strength(), 'DEX' => $monster->dexterity(), 'CON' => $monster->constitution(), 'INT' => $monster->intelligence(), 'WIS' => $monster->wisdom(), 'CHA' => $monster->charisma()] as $label => $score) : ?>
                    <div><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($score === null ? '—' : (string) $score); ?></strong></div>
                <?php endforeach; ?>
            </section>

            <div class="gmrc-canonical-folio__details">
                <section><h2>Special Traits</h2><p><?php echo nl2br(esc_html($monster->traits() !== '' ? $monster->traits() : 'No special traits are recorded in the canonical source.')); ?></p></section>
                <section><h2>Actions</h2><p><?php echo nl2br(esc_html($monster->actions() !== '' ? $monster->actions() : 'No actions are recorded in the canonical source.')); ?></p></section>
                <?php if ($monster->notes() !== '') : ?><section><h2>Lore & Steward Notes</h2><p><?php echo nl2br(esc_html($monster->notes())); ?></p></section><?php endif; ?>
                <?php if ($monster->sourceIssue() !== '') : ?><section class="gmrc-canonical-folio__source"><h2>Canonical Source Note</h2><p><?php echo esc_html($monster->sourceIssue()); ?></p></section><?php endif; ?>
            </div>
        </div>
    </div>
</section>
