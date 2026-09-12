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
$ledgerUrl = add_query_arg('gmrc_route', 'characters/' . rawurlencode($characterId), $companionUrl);
$appRequestUrl = admin_url('admin-post.php');
$skillLabels = [
'acrobatics'=>'Acrobatics','animal-handling'=>'Animal Handling','arcana'=>'Arcana','athletics'=>'Athletics','deception'=>'Deception','history'=>'History','insight'=>'Insight','intimidation'=>'Intimidation','investigation'=>'Investigation','medicine'=>'Medicine','nature'=>'Nature','perception'=>'Perception','performance'=>'Performance','persuasion'=>'Persuasion','religion'=>'Religion','sleight-of-hand'=>'Sleight of Hand','stealth'=>'Stealth','survival'=>'Survival'];
$saveLabels = ['strength'=>'STR','dexterity'=>'DEX','constitution'=>'CON','intelligence'=>'INT','wisdom'=>'WIS','charisma'=>'CHA'];
$featureEntries = array_values(array_filter($arcana['entries'] ?? [], static fn(array $entry): bool => ($entry['kind'] ?? '') === 'feature'));
$purse = $character->purse()->formatted();
?>
<section class="gmrc-adventuring-sheet" data-adventuring-sheet>
    <header class="gmrc-adventuring-sheet__masthead">
        <div class="gmrc-adventuring-sheet__portrait">
            <?php echo $this->component('components.media.illuminated-portrait', ['portrait'=>$portrait,'portraitPersisted'=>true,'controlsEnabled'=>false]); ?>
        </div>
        <div class="gmrc-adventuring-sheet__identity">
            <p class="gmrc-eyebrow">Adventuring Sheet · Live Play</p>
            <h1><?php echo esc_html($name); ?></h1>
            <p class="gmrc-adventuring-sheet__byline">Level <?php echo esc_html((string)$level); ?> <?php echo esc_html($race); ?> <?php echo esc_html($class); ?><?php echo $pathLabel !== '' ? ' · ' . esc_html($pathLabel) : ''; ?></p>
            <p class="gmrc-adventuring-sheet__background"><?php echo esc_html($background); ?></p>
        </div>
        <div class="gmrc-adventuring-sheet__actions">
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($ledgerUrl); ?>">Open Full Ledger</a>
            <span class="gmrc-adventuring-sheet__mode">Advancement and archival detail stay in the Ledger.</span>
        </div>
    </header>

    <section class="gmrc-adventuring-sheet__measures" aria-label="Core adventuring measures">
        <div><span>AC</span><strong><?php echo esc_html((string)$armourClass); ?></strong></div>
        <div><span>Initiative</span><strong><?php echo esc_html($character->initiative()->signed()); ?></strong></div>
        <div><span>Speed</span><strong><?php echo esc_html($character->speed()->formatted()); ?></strong></div>
        <div><span>Proficiency</span><strong><?php echo esc_html($character->proficiencyBonus()->signed()); ?></strong></div>
        <div><span>Passive Perception</span><strong><?php echo esc_html((string)$character->passivePerception()->value()); ?></strong></div>
        <div><span>Coin Purse</span><strong><?php echo esc_html($purse); ?></strong></div>
    </section>

    <div class="gmrc-adventuring-sheet__grid">
        <main class="gmrc-adventuring-sheet__main">
            <section class="gmrc-play-card gmrc-play-card--abilities">
                <header><h2>Abilities</h2></header>
                <div class="gmrc-adventuring-sheet__abilities">
                    <?php foreach ($abilities as $label => $score): ?>
                        <div class="gmrc-ability-tile"><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html((string)$score->value()); ?></strong><em><?php echo esc_html(sprintf('%+d', $score->modifier())); ?></em></div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="gmrc-play-card gmrc-play-card--vitals" data-vital-measures data-maximum-hp="<?php echo esc_attr((string)$hp->maximum()); ?>">
                <header><h2>Hit Points</h2><span data-vital-consciousness><?php echo esc_html($character->isConscious() ? 'Conscious' : 'Unconscious'); ?></span></header>
                <form method="post" action="<?php echo esc_url($appRequestUrl); ?>" data-vital-measures-form>
                    <input type="hidden" name="action" value="gmrc_app_request"><input type="hidden" name="gmrc_route" value="<?php echo esc_attr('characters/' . rawurlencode($characterId) . '/vital-measures'); ?>">
                    <?php wp_nonce_field('gmrc_character_vitals_' . $characterId, 'gmrc_nonce'); ?>
                    <div class="gmrc-adventuring-sheet__hp">
                        <label>Current HP<input type="number" name="current_hp" min="0" max="<?php echo esc_attr((string)$hp->maximum()); ?>" value="<?php echo esc_attr((string)$hp->current()); ?>" data-vital-current required></label>
                        <div class="gmrc-adventuring-sheet__max-hp"><span>Maximum</span><strong><?php echo esc_html((string)$hp->maximum()); ?></strong><small>Guild certified</small></div>
                        <label>Temporary HP<input type="number" name="temporary_hp" min="0" max="999" value="<?php echo esc_attr((string)$hp->temporary()); ?>" data-vital-temporary required></label>
                        <button class="gmrc-button gmrc-button--secondary" type="submit">Save HP</button>
                    </div>
                </form>
            </section>

            <section class="gmrc-play-card">
                <header><h2>Attacks</h2></header>
                <?php if ($attacks === []): ?><p class="gmrc-play-card__empty">No equipped weapon attacks.</p><?php else: ?>
                <div class="gmrc-adventuring-sheet__attack-list">
                    <?php foreach ($attacks as $attack): ?><article><div><strong><?php echo esc_html((string)$attack['label']); ?></strong><small><?php echo esc_html((string)$attack['range']); ?></small></div><b><?php echo esc_html(sprintf('%+d', (int)$attack['attack_bonus'])); ?> to hit</b><span><?php echo esc_html((string)$attack['damage_die']); ?><?php $dm=(int)$attack['damage_modifier']; echo $dm!==0 ? esc_html(sprintf(' %+d',$dm)) : ''; ?> <?php echo esc_html((string)$attack['damage_type']); ?></span></article><?php endforeach; ?>
                </div><?php endif; ?>
            </section>

            <?php if (! empty($arcana['has_spells'])): ?>
            <section class="gmrc-play-card gmrc-play-card--spells">
                <header><h2>Spellcasting</h2><span><?php echo esc_html((string)($arcana['casting_ability'] ?? '')); ?> · DC <?php echo esc_html((string)($arcana['save_dc'] ?? '—')); ?> · Attack <?php echo esc_html(isset($arcana['spell_attack']) ? sprintf('%+d',(int)$arcana['spell_attack']) : '—'); ?></span></header>
                <?php if (! empty($arcana['slots'])): ?><div class="gmrc-adventuring-sheet__slots"><?php foreach ($arcana['slots'] as $slot): ?><span>Lv <?php echo esc_html((string)($slot['level']??'')); ?> <strong><?php echo esc_html((string)($slot['remaining'] ?? $slot['total'] ?? 0)); ?>/<?php echo esc_html((string)($slot['total']??0)); ?></strong></span><?php endforeach; ?></div><?php endif; ?>
                <?php foreach (($arcana['shelves'] ?? []) as $shelf): if (($shelf['kind'] ?? '') === 'feature') continue; ?><div class="gmrc-adventuring-sheet__spell-shelf"><h3><?php echo esc_html((string)$shelf['label']); ?></h3><div><?php foreach ($shelf['entries'] as $spell): ?><article><strong><?php echo esc_html((string)$spell['label']); ?></strong><small><?php echo esc_html((string)$spell['activation']); ?> · <?php echo esc_html((string)$spell['range']); ?> · <?php echo esc_html((string)$spell['duration']); ?></small><p><?php echo esc_html((string)$spell['description']); ?></p></article><?php endforeach; ?></div></div><?php endforeach; ?>
            </section>
            <?php endif; ?>

            <section class="gmrc-play-card">
                <header><h2>Features & Gifts</h2></header>
                <div class="gmrc-adventuring-sheet__feature-list">
                <?php foreach (($pathGifts['gifts'] ?? []) as $gift): ?><article><strong><?php echo esc_html((string)($gift['label'] ?? $gift['name'] ?? 'Path Gift')); ?></strong><p><?php echo esc_html((string)($gift['detail'] ?? $gift['description'] ?? '')); ?></p></article><?php endforeach; ?>
                <?php foreach ($featureEntries as $feature): ?><article><strong><?php echo esc_html((string)$feature['label']); ?></strong><p><?php echo esc_html((string)$feature['description']); ?></p></article><?php endforeach; ?>
                <?php if (($pathGifts['gifts'] ?? []) === [] && $featureEntries === []): ?><p class="gmrc-play-card__empty">No additional active-play features are indexed here yet; the full Ledger retains all advancement records.</p><?php endif; ?>
                </div>
            </section>

            <section class="gmrc-play-card">
                <header><h2>Equipment</h2><span><?php echo esc_html((string)($inventory['total_weight'] ?? 0)); ?> / <?php echo esc_html((string)($inventory['capacity'] ?? 0)); ?> lb</span></header>
                <div class="gmrc-adventuring-sheet__equipment"><?php foreach (($inventory['rows'] ?? []) as $item): ?><article class="<?php echo !empty($item['equipped']) ? 'is-equipped' : ''; ?>"><strong><?php echo esc_html((string)$item['label']); ?></strong><span>×<?php echo esc_html((string)$item['quantity']); ?></span><small><?php echo !empty($item['equipped']) ? 'Equipped · ' : ''; ?><?php echo esc_html((string)$item['category']); ?></small></article><?php endforeach; ?><?php if (($inventory['rows'] ?? [])===[]): ?><p class="gmrc-play-card__empty">The Adventurer’s Pack is empty.</p><?php endif; ?></div>
            </section>
        </main>

        <aside class="gmrc-adventuring-sheet__sidebar">
            <section class="gmrc-play-card"><header><h2>Saving Throws</h2></header><ul class="gmrc-adventuring-sheet__compact-list"><?php foreach ($saveLabels as $key=>$label): $save=$saves->get($key); ?><li class="<?php echo $save->isProficient()?'is-proficient':''; ?>"><span><?php echo $save->isProficient()?'●':'○'; ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($save->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-play-card"><header><h2>Skills</h2></header><ul class="gmrc-adventuring-sheet__compact-list"><?php foreach ($skillLabels as $key=>$label): $skill=$skills->get($key); ?><li class="<?php echo $skill->isProficient()?'is-proficient':''; ?>"><span><?php echo $skill->hasExpertise()?'◆':($skill->isProficient()?'●':'○'); ?> <?php echo esc_html($label); ?></span><strong><?php echo esc_html($skill->signed()); ?></strong></li><?php endforeach; ?></ul></section>
            <section class="gmrc-play-card"><header><h2>Proficiencies</h2></header><p><strong>Languages:</strong> <?php echo esc_html(implode(', ', array_map(static fn($l)=> (string)$l, $character->languages()->all()))); ?></p><p><strong>Tools:</strong> <?php echo esc_html(implode(', ', array_map(static fn($t)=> (string)$t, $character->toolProficiencies()->all()))); ?></p></section>
        </aside>
    </div>
</section>
