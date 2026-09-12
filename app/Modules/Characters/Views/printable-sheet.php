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
$skillLabels = ['acrobatics'=>'Acrobatics','animal-handling'=>'Animal Handling','arcana'=>'Arcana','athletics'=>'Athletics','deception'=>'Deception','history'=>'History','insight'=>'Insight','intimidation'=>'Intimidation','investigation'=>'Investigation','medicine'=>'Medicine','nature'=>'Nature','perception'=>'Perception','performance'=>'Performance','persuasion'=>'Persuasion','religion'=>'Religion','sleight-of-hand'=>'Sleight of Hand','stealth'=>'Stealth','survival'=>'Survival'];
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
            <strong>The Great MarketRealm Companion</strong>
            <span>Offline Adventurer Record</span>
        </div>
        <img src="<?php echo esc_url(GMRC_URL . 'assets/images/auby/seals/seal-of-approval-one-colour.svg'); ?>" alt="">
    </header>
    <div class="gmrc-printable-sheet__toolbar" data-print-toolbar>
        <div>
            <p class="gmrc-eyebrow">Offline Character Sheet</p>
            <strong>Print this sheet, or choose “Save as PDF” in your browser’s print dialog.</strong>
        </div>
        <div>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($adventureUrl); ?>">Back to Live Play</a>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($ledgerUrl); ?>">Full Ledger</a>
            <button class="gmrc-button" type="button" data-print-character-sheet>Print / Save PDF</button>
        </div>
    </div>

    <article class="gmrc-print-page gmrc-print-page--core">
        <header class="gmrc-printable-sheet__header">
            <div class="gmrc-printable-sheet__portrait" aria-hidden="true">
                <?php if ($isCustomPortrait): ?><img src="<?php echo esc_url((string)$customPortraitUrl); ?>" alt=""><?php elseif ($generatedPortraitSvg !== ''): ?><?php echo $generatedPortraitSvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?>
            </div>
            <div class="gmrc-printable-sheet__identity">
                <p>The Great MarketRealm · Adventurer Record</p>
                <h1><?php echo esc_html($name); ?></h1>
                <strong>Level <?php echo esc_html((string)$level); ?> <?php echo esc_html($race); ?> <?php echo esc_html($class); ?><?php echo $pathLabel !== '' ? ' · ' . esc_html($pathLabel) : ''; ?></strong>
                <span><?php echo esc_html($background); ?></span>
            </div>
            <dl class="gmrc-printable-sheet__quick">
                <div><dt>AC</dt><dd><?php echo esc_html((string)$armourClass); ?></dd></div>
                <div><dt>Initiative</dt><dd><?php echo esc_html($character->initiative()->signed()); ?></dd></div>
                <div><dt>Speed</dt><dd><?php echo esc_html($character->speed()->formatted()); ?></dd></div>
                <div><dt>Prof.</dt><dd><?php echo esc_html($character->proficiencyBonus()->signed()); ?></dd></div>
                <div><dt>Passive</dt><dd><?php echo esc_html((string)$character->passivePerception()->value()); ?></dd></div>
                <div><dt>Coin</dt><dd><?php echo esc_html($purse); ?></dd></div>
            </dl>
        </header>

        <div class="gmrc-printable-sheet__columns">
            <section class="gmrc-print-block gmrc-print-block--abilities">
                <h2>Abilities</h2>
                <div class="gmrc-printable-sheet__abilities"><?php foreach ($abilities as $label=>$score): ?><div><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html((string)$score->value()); ?></strong><b><?php echo esc_html(sprintf('%+d',$score->modifier())); ?></b></div><?php endforeach; ?></div>
            </section>
            <section class="gmrc-print-block gmrc-print-block--hp">
                <h2>Hit Points</h2>
                <div class="gmrc-printable-sheet__hp-lines">
                    <label>Current HP <span class="gmrc-printable-sheet__writebox"><?php echo esc_html((string)$hp->current()); ?></span></label>
                    <label>Temporary HP <span class="gmrc-printable-sheet__writebox"><?php echo esc_html((string)$hp->temporary()); ?></span></label>
                    <div><span>Maximum HP</span><strong><?php echo esc_html((string)$hp->maximum()); ?></strong></div>
                </div>
                <p class="gmrc-printable-sheet__pencil-note">Current and Temporary HP boxes are intentionally spacious for pencil updates during offline play.</p>
            </section>
            <section class="gmrc-print-block"><h2>Saving Throws</h2><ul class="gmrc-print-list"><?php foreach ($saveLabels as $key=>$label): $save=$saves->get($key); ?><li><span><?php echo $save->isProficient()?'●':'○'; ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($save->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-print-block gmrc-print-block--skills"><h2>Skills</h2><ul class="gmrc-print-list"><?php foreach ($skillLabels as $key=>$label): $skill=$skills->get($key); ?><li><span><?php echo $skill->hasExpertise()?'◆':($skill->isProficient()?'●':'○'); ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($skill->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-print-block gmrc-print-block--attacks"><h2>Attacks</h2><?php if ($attacks === []): ?><p>—</p><?php else: ?><table><thead><tr><th>Attack</th><th>To Hit</th><th>Damage</th><th>Range</th></tr></thead><tbody><?php foreach ($attacks as $attack): ?><tr><td><?php echo esc_html((string)$attack['label']); ?></td><td><?php echo esc_html(sprintf('%+d',(int)$attack['attack_bonus'])); ?></td><td><?php echo esc_html((string)$attack['damage_die']); ?><?php $dm=(int)$attack['damage_modifier']; echo $dm!==0 ? esc_html(sprintf(' %+d',$dm)) : ''; ?> <?php echo esc_html((string)$attack['damage_type']); ?></td><td><?php echo esc_html((string)$attack['range']); ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section>
        </div>
        <footer class="gmrc-printable-sheet__page-footer"><span><?php echo esc_html($name); ?></span><span>Adventurer Record · Core Play Sheet</span></footer>
    </article>

    <article class="gmrc-print-page gmrc-print-page--details">
        <div class="gmrc-printable-sheet__details-grid">
            <section class="gmrc-print-block"><h2>Equipment</h2><div class="gmrc-printable-sheet__equipment"><?php foreach (($inventory['rows'] ?? []) as $item): ?><div><strong><?php echo esc_html((string)$item['label']); ?></strong><span>×<?php echo esc_html((string)$item['quantity']); ?><?php echo !empty($item['equipped']) ? ' · Equipped' : ''; ?></span></div><?php endforeach; ?><?php if (($inventory['rows'] ?? [])===[]): ?><p>—</p><?php endif; ?></div></section>
            <section class="gmrc-print-block"><h2>Features & Gifts</h2><?php foreach (($pathGifts['gifts'] ?? []) as $gift): ?><article class="gmrc-printable-sheet__feature"><strong><?php echo esc_html((string)($gift['label'] ?? $gift['name'] ?? 'Path Gift')); ?></strong><p><?php echo esc_html((string)($gift['detail'] ?? $gift['description'] ?? '')); ?></p></article><?php endforeach; ?><?php foreach ($featureEntries as $feature): ?><article class="gmrc-printable-sheet__feature"><strong><?php echo esc_html((string)$feature['label']); ?></strong><p><?php echo esc_html((string)$feature['description']); ?></p></article><?php endforeach; ?></section>
            <section class="gmrc-print-block"><h2>Proficiencies</h2><p><strong>Languages:</strong> <?php echo esc_html(implode(', ', array_map(static fn($l)=>(string)$l,$character->languages()->all()))); ?></p><p><strong>Tools:</strong> <?php echo esc_html(implode(', ', array_map(static fn($t)=>(string)$t,$character->toolProficiencies()->all()))); ?></p></section>
            <?php if (! empty($arcana['has_spells'])): ?><section class="gmrc-print-block gmrc-print-block--spells"><h2>Spellcasting</h2><p><strong><?php echo esc_html((string)($arcana['casting_ability'] ?? '')); ?></strong> · Save DC <?php echo esc_html((string)($arcana['save_dc'] ?? '—')); ?> · Attack <?php echo esc_html(isset($arcana['spell_attack']) ? sprintf('%+d',(int)$arcana['spell_attack']) : '—'); ?></p><?php if (! empty($arcana['slots'])): ?><p class="gmrc-printable-sheet__slots"><?php foreach ($arcana['slots'] as $slot): ?>Lv <?php echo esc_html((string)($slot['level']??'')); ?> <?php echo esc_html((string)($slot['remaining'] ?? $slot['total'] ?? 0)); ?>/<?php echo esc_html((string)($slot['total']??0)); ?>&nbsp;&nbsp;<?php endforeach; ?></p><?php endif; ?><?php foreach (($arcana['shelves'] ?? []) as $shelf): if (($shelf['kind'] ?? '') === 'feature') continue; ?><div class="gmrc-printable-sheet__spell-group"><h3><?php echo esc_html((string)$shelf['label']); ?></h3><?php foreach ($shelf['entries'] as $spell): ?><p><strong><?php echo esc_html((string)$spell['label']); ?></strong> — <?php echo esc_html((string)$spell['activation']); ?> · <?php echo esc_html((string)$spell['range']); ?> · <?php echo esc_html((string)$spell['duration']); ?></p><?php endforeach; ?></div><?php endforeach; ?></section><?php endif; ?>
            <section class="gmrc-print-block gmrc-print-block--notes"><h2>Offline Notes</h2><div></div><div></div><div></div><div></div><div></div></section>
        </div>
        <footer class="gmrc-printable-sheet__page-footer"><span><?php echo esc_html($name); ?></span><span>Adventurer Record · Equipment, Features & Arcana</span></footer>
    </article>
</section>
