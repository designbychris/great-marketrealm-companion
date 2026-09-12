<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

if (! isset($character) || ! $character instanceof Character || ! isset($portrait) || ! $portrait instanceof PortraitViewModel) {
    return;
}

$characterId = $character->id()->value();
$name = $character->name()->value();
$level = $character->level()->value();
$race = $character->race()->label();
$class = $character->characterClass()->label();
$background = $character->background()->label();
$path = $character->callingPath()->value();
$pathLabel = $path !== '' ? ucwords(str_replace('-', ' ', $path)) : '';
$abilityScores = $character->abilityScores();
$abilities = [
    'STR' => $abilityScores->strength(), 'DEX' => $abilityScores->dexterity(),
    'CON' => $abilityScores->constitution(), 'INT' => $abilityScores->intelligence(),
    'WIS' => $abilityScores->wisdom(), 'CHA' => $abilityScores->charisma(),
];
$saves = $character->savingThrows();
$skills = $character->skills();
$hp = $character->hitPoints();
$inventory = isset($inventory) && is_array($inventory) ? $inventory : ['rows' => []];
$attacks = isset($attacks) && is_array($attacks) ? $attacks : [];
$arcana = isset($arcana) && is_array($arcana) ? $arcana : ['entries'=>[], 'shelves'=>[], 'slots'=>[], 'has_spells'=>false];
$pathGifts = isset($pathGifts) && is_array($pathGifts) ? $pathGifts : ['gifts'=>[]];
$armourClass = isset($inventoryArmourClass) ? (int) $inventoryArmourClass : $character->armourClass()->value();
$companionUrl = home_url('/companion/');
$adventureUrl = add_query_arg('gmrc_route', 'characters/' . rawurlencode($characterId) . '/adventuring-sheet', $companionUrl);
$ledgerUrl = add_query_arg('gmrc_route', 'characters/' . rawurlencode($characterId), $companionUrl);
$skillLabels = ['acrobatics'=>__('Acrobatics', 'great-marketrealm-companion'),'animal-handling'=>__('Animal Handling', 'great-marketrealm-companion'),'arcana'=>__('Arcana', 'great-marketrealm-companion'),'athletics'=>__('Athletics', 'great-marketrealm-companion'),'deception'=>__('Deception', 'great-marketrealm-companion'),'history'=>__('History', 'great-marketrealm-companion'),'insight'=>__('Insight', 'great-marketrealm-companion'),'intimidation'=>__('Intimidation', 'great-marketrealm-companion'),'investigation'=>__('Investigation', 'great-marketrealm-companion'),'medicine'=>__('Medicine', 'great-marketrealm-companion'),'nature'=>__('Nature', 'great-marketrealm-companion'),'perception'=>__('Perception', 'great-marketrealm-companion'),'performance'=>__('Performance', 'great-marketrealm-companion'),'persuasion'=>__('Persuasion', 'great-marketrealm-companion'),'religion'=>__('Religion', 'great-marketrealm-companion'),'sleight-of-hand'=>__('Sleight of Hand', 'great-marketrealm-companion'),'stealth'=>__('Stealth', 'great-marketrealm-companion'),'survival'=>__('Survival', 'great-marketrealm-companion')];
$saveLabels = ['strength'=>'STR','dexterity'=>'DEX','constitution'=>'CON','intelligence'=>'INT','wisdom'=>'WIS','charisma'=>'CHA'];
$featureEntries = array_values(array_filter($arcana['entries'] ?? [], static fn(array $entry): bool => ($entry['kind'] ?? '') === 'feature'));
$purse = $character->purse()->formatted();
$customPortraitUrl = $portrait->attachmentUrl();
$isCustomPortrait = $portrait->isCustom() && is_string($customPortraitUrl) && $customPortraitUrl !== '';
$generatedPortraitSvg = trim($portrait->svg());
?>
<section class="gmrc-printable-sheet" data-printable-sheet data-print-character-name="<?php echo esc_attr($name); ?>">
    <header class="gmrc-printable-sheet__print-brand" aria-hidden="true">
        <div>
            <strong><?php esc_html_e('The Great MarketRealm Companion', 'great-marketrealm-companion'); ?></strong>
            <span><?php esc_html_e('Offline Adventurer Record', 'great-marketrealm-companion'); ?></span>
        </div>
        <img src="<?php echo esc_url(GMRC_URL . 'assets/images/auby/seals/seal-of-approval-one-colour.svg'); ?>" alt="">
    </header>
    <div class="gmrc-printable-sheet__toolbar" data-print-toolbar>
        <div>
            <p class="gmrc-eyebrow"><?php esc_html_e('Offline Character Sheet', 'great-marketrealm-companion'); ?></p>
            <strong><?php esc_html_e('Print this sheet, or choose “Save as PDF” in your browser’s print dialog.', 'great-marketrealm-companion'); ?></strong>
        </div>
        <div>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($adventureUrl); ?>"><?php esc_html_e('Back to Live Play', 'great-marketrealm-companion'); ?></a>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($ledgerUrl); ?>"><?php esc_html_e('Full Ledger', 'great-marketrealm-companion'); ?></a>
            <button class="gmrc-button" type="button" data-print-character-sheet><?php esc_html_e('Print / Save PDF', 'great-marketrealm-companion'); ?></button>
        </div>
    </div>

    <article class="gmrc-print-page gmrc-print-page--core">
        <header class="gmrc-printable-sheet__header">
            <div class="gmrc-printable-sheet__portrait" aria-hidden="true">
                <?php if ($isCustomPortrait): ?><img src="<?php echo esc_url((string)$customPortraitUrl); ?>" alt=""><?php elseif ($generatedPortraitSvg !== ''): ?><?php echo $generatedPortraitSvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?>
            </div>
            <div class="gmrc-printable-sheet__identity">
                <p><?php esc_html_e('The Great MarketRealm · Adventurer Record', 'great-marketrealm-companion'); ?></p>
                <h1><?php echo esc_html($name); ?></h1>
                <strong><?php esc_html_e('Level', 'great-marketrealm-companion'); ?> <?php echo esc_html((string)$level); ?> <?php echo esc_html($race); ?> <?php echo esc_html($class); ?><?php echo $pathLabel !== '' ? ' · ' . esc_html($pathLabel) : ''; ?></strong>
                <span><?php echo esc_html($background); ?></span>
            </div>
            <dl class="gmrc-printable-sheet__quick">
                <div><dt>AC</dt><dd><?php echo esc_html((string)$armourClass); ?></dd></div>
                <div><dt><?php esc_html_e('Initiative', 'great-marketrealm-companion'); ?></dt><dd><?php echo esc_html($character->initiative()->signed()); ?></dd></div>
                <div><dt><?php esc_html_e('Speed', 'great-marketrealm-companion'); ?></dt><dd><?php echo esc_html($character->speed()->formatted()); ?></dd></div>
                <div><dt><?php esc_html_e('Prof.', 'great-marketrealm-companion'); ?></dt><dd><?php echo esc_html($character->proficiencyBonus()->signed()); ?></dd></div>
                <div><dt><?php esc_html_e('Passive', 'great-marketrealm-companion'); ?></dt><dd><?php echo esc_html((string)$character->passivePerception()->value()); ?></dd></div>
                <div><dt><?php esc_html_e('Coin', 'great-marketrealm-companion'); ?></dt><dd><?php echo esc_html($purse); ?></dd></div>
            </dl>
        </header>

        <div class="gmrc-printable-sheet__columns">
            <section class="gmrc-print-block gmrc-print-block--abilities">
                <h2><?php esc_html_e('Abilities', 'great-marketrealm-companion'); ?></h2>
                <div class="gmrc-printable-sheet__abilities"><?php foreach ($abilities as $label=>$score): ?><div><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html((string)$score->value()); ?></strong><b><?php echo esc_html(sprintf('%+d',$score->modifier())); ?></b></div><?php endforeach; ?></div>
            </section>
            <section class="gmrc-print-block gmrc-print-block--hp">
                <h2><?php esc_html_e('Hit Points', 'great-marketrealm-companion'); ?></h2>
                <div class="gmrc-printable-sheet__hp-lines">
                    <label><?php esc_html_e('Current HP', 'great-marketrealm-companion'); ?> <span class="gmrc-printable-sheet__writebox"><?php echo esc_html((string)$hp->current()); ?></span></label>
                    <label><?php esc_html_e('Temporary HP', 'great-marketrealm-companion'); ?> <span class="gmrc-printable-sheet__writebox"><?php echo esc_html((string)$hp->temporary()); ?></span></label>
                    <div><span><?php esc_html_e('Maximum HP', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html((string)$hp->maximum()); ?></strong></div>
                </div>
                <p class="gmrc-printable-sheet__pencil-note"><?php esc_html_e('Current and Temporary HP boxes are intentionally spacious for pencil updates during offline play.', 'great-marketrealm-companion'); ?></p>
            </section>
            <section class="gmrc-print-block"><h2><?php esc_html_e('Saving Throws', 'great-marketrealm-companion'); ?></h2><ul class="gmrc-print-list"><?php foreach ($saveLabels as $key=>$label): $save=$saves->get($key); ?><li><span><?php echo $save->isProficient()?'●':'○'; ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($save->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-print-block gmrc-print-block--skills"><h2><?php esc_html_e('Skills', 'great-marketrealm-companion'); ?></h2><ul class="gmrc-print-list"><?php foreach ($skillLabels as $key=>$label): $skill=$skills->get($key); ?><li><span><?php echo $skill->hasExpertise()?'◆':($skill->isProficient()?'●':'○'); ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($skill->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-print-block gmrc-print-block--attacks"><h2><?php esc_html_e('Attacks', 'great-marketrealm-companion'); ?></h2><?php if ($attacks === []): ?><p>—</p><?php else: ?><table><thead><tr><th><?php esc_html_e('Attack', 'great-marketrealm-companion'); ?></th><th><?php esc_html_e('To Hit', 'great-marketrealm-companion'); ?></th><th><?php esc_html_e('Damage', 'great-marketrealm-companion'); ?></th><th><?php esc_html_e('Range', 'great-marketrealm-companion'); ?></th></tr></thead><tbody><?php foreach ($attacks as $attack): ?><tr><td><?php echo esc_html((string)$attack['label']); ?></td><td><?php echo esc_html(sprintf('%+d',(int)$attack['attack_bonus'])); ?></td><td><?php echo esc_html((string)$attack['damage_die']); ?><?php $dm=(int)$attack['damage_modifier']; echo $dm!==0 ? esc_html(sprintf(' %+d',$dm)) : ''; ?> <?php echo esc_html((string)$attack['damage_type']); ?></td><td><?php echo esc_html((string)$attack['range']); ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        </div>
        <footer class="gmrc-printable-sheet__page-footer"><span><?php echo esc_html($name); ?></span><span><?php esc_html_e('Adventurer Record · Core Play Sheet', 'great-marketrealm-companion'); ?></span></footer>
    </article>

    <article class="gmrc-print-page gmrc-print-page--details">
        <div class="gmrc-printable-sheet__details-grid">
            <section class="gmrc-print-block"><h2><?php esc_html_e('Equipment', 'great-marketrealm-companion'); ?></h2><div class="gmrc-printable-sheet__equipment"><?php foreach (($inventory['rows'] ?? []) as $item): ?><div><strong><?php echo esc_html((string)$item['label']); ?></strong><span>×<?php echo esc_html((string)$item['quantity']); ?><?php echo !empty($item['equipped']) ? ' · ' . __('Equipped', 'great-marketrealm-companion') : ''; ?></span></div><?php endforeach; ?><?php if (($inventory['rows'] ?? [])===[]): ?><p>—</p><?php endif; ?></div></section>
            <section class="gmrc-print-block"><h2><?php esc_html_e('Features & Gifts', 'great-marketrealm-companion'); ?></h2><?php foreach (($pathGifts['gifts'] ?? []) as $gift): ?><article class="gmrc-printable-sheet__feature"><strong><?php echo esc_html((string)($gift['label'] ?? $gift['name'] ?? __('Path Gift', 'great-marketrealm-companion'))); ?></strong><p><?php echo esc_html((string)($gift['detail'] ?? $gift['description'] ?? '')); ?></p></article><?php endforeach; ?><?php foreach ($featureEntries as $feature): ?><article class="gmrc-printable-sheet__feature"><strong><?php echo esc_html((string)$feature['label']); ?></strong><p><?php echo esc_html((string)$feature['description']); ?></p></article><?php endforeach; ?></section>
            <section class="gmrc-print-block"><h2><?php esc_html_e('Proficiencies', 'great-marketrealm-companion'); ?></h2><p><strong><?php esc_html_e('Languages:', 'great-marketrealm-companion'); ?></strong> <?php echo esc_html(implode(', ', array_map(static fn($l)=>(string)$l,$character->languages()->all()))); ?></p><p><strong><?php esc_html_e('Tools:', 'great-marketrealm-companion'); ?></strong> <?php echo esc_html(implode(', ', array_map(static fn($t)=>(string)$t,$character->toolProficiencies()->all()))); ?></p></section>
            <?php if (! empty($arcana['has_spells'])): ?><section class="gmrc-print-block gmrc-print-block--spells"><h2><?php esc_html_e('Spellcasting', 'great-marketrealm-companion'); ?></h2><p><strong><?php echo esc_html((string)($arcana['casting_ability'] ?? '')); ?></strong> · <?php esc_html_e('Save DC', 'great-marketrealm-companion'); ?> <?php echo esc_html((string)($arcana['save_dc'] ?? '—')); ?> · <?php esc_html_e('Attack', 'great-marketrealm-companion'); ?> <?php echo esc_html(isset($arcana['spell_attack']) ? sprintf('%+d',(int)$arcana['spell_attack']) : '—'); ?></p><?php if (! empty($arcana['slots'])): ?><p class="gmrc-printable-sheet__slots"><?php foreach ($arcana['slots'] as $slot): ?>Lv <?php echo esc_html((string)($slot['level']??'')); ?> <?php echo esc_html((string)($slot['remaining'] ?? $slot['total'] ?? 0)); ?>/<?php echo esc_html((string)($slot['total']??0)); ?>&nbsp;&nbsp;<?php endforeach; ?></p><?php endif; ?><?php foreach (($arcana['shelves'] ?? []) as $shelf): if (($shelf['kind'] ?? '') === 'feature') continue; ?><div class="gmrc-printable-sheet__spell-group"><h3><?php echo esc_html((string)$shelf['label']); ?></h3><?php foreach ($shelf['entries'] as $spell): ?><p><strong><?php echo esc_html((string)$spell['label']); ?></strong> — <?php echo esc_html((string)$spell['activation']); ?> · <?php echo esc_html((string)$spell['range']); ?> · <?php echo esc_html((string)$spell['duration']); ?></p><?php endforeach; ?></div><?php endforeach; ?></section><?php endif; ?>
            <section class="gmrc-print-block gmrc-print-block--notes"><h2><?php esc_html_e('Offline Notes', 'great-marketrealm-companion'); ?></h2><div></div><div></div><div></div><div></div><div></div></section>
        </div>
        <footer class="gmrc-printable-sheet__page-footer"><span><?php echo esc_html($name); ?></span><span><?php esc_html_e('Adventurer Record · Equipment, Features & Arcana', 'great-marketrealm-companion'); ?></span></footer>
    </article>
</section>
