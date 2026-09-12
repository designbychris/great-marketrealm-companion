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
$abilityNames = ['STR'=>__('Strength', 'great-marketrealm-companion'),'DEX'=>__('Dexterity', 'great-marketrealm-companion'),'CON'=>__('Constitution', 'great-marketrealm-companion'),'INT'=>__('Intelligence', 'great-marketrealm-companion'),'WIS'=>__('Wisdom', 'great-marketrealm-companion'),'CHA'=>__('Charisma', 'great-marketrealm-companion')];
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
$printUrl = add_query_arg('gmrc_route', 'characters/' . rawurlencode($characterId) . '/printable-sheet', $companionUrl);
$appRequestUrl = admin_url('admin-post.php');
$skillLabels = [
'acrobatics'=>__('Acrobatics', 'great-marketrealm-companion'),'animal-handling'=>__('Animal Handling', 'great-marketrealm-companion'),'arcana'=>__('Arcana', 'great-marketrealm-companion'),'athletics'=>__('Athletics', 'great-marketrealm-companion'),'deception'=>__('Deception', 'great-marketrealm-companion'),'history'=>__('History', 'great-marketrealm-companion'),'insight'=>__('Insight', 'great-marketrealm-companion'),'intimidation'=>__('Intimidation', 'great-marketrealm-companion'),'investigation'=>__('Investigation', 'great-marketrealm-companion'),'medicine'=>__('Medicine', 'great-marketrealm-companion'),'nature'=>__('Nature', 'great-marketrealm-companion'),'perception'=>__('Perception', 'great-marketrealm-companion'),'performance'=>__('Performance', 'great-marketrealm-companion'),'persuasion'=>__('Persuasion', 'great-marketrealm-companion'),'religion'=>__('Religion', 'great-marketrealm-companion'),'sleight-of-hand'=>__('Sleight of Hand', 'great-marketrealm-companion'),'stealth'=>__('Stealth', 'great-marketrealm-companion'),'survival'=>__('Survival', 'great-marketrealm-companion')];
$skillAbilities = [
'acrobatics'=>'DEX','animal-handling'=>'WIS','arcana'=>'INT','athletics'=>'STR','deception'=>'CHA','history'=>'INT','insight'=>'WIS','intimidation'=>'CHA','investigation'=>'INT','medicine'=>'WIS','nature'=>'INT','perception'=>'WIS','performance'=>'CHA','persuasion'=>'CHA','religion'=>'INT','sleight-of-hand'=>'DEX','stealth'=>'DEX','survival'=>'WIS'];
$saveLabels = ['strength'=>'STR','dexterity'=>'DEX','constitution'=>'CON','intelligence'=>'INT','wisdom'=>'WIS','charisma'=>'CHA'];
$featureEntries = array_values(array_filter($arcana['entries'] ?? [], static fn(array $entry): bool => ($entry['kind'] ?? '') === 'feature'));
$purse = $character->purse()->formatted();
$customPortraitUrl = $portrait->attachmentUrl();
$isCustomPortrait = $portrait->isCustom()
    && is_string($customPortraitUrl)
    && $customPortraitUrl !== '';
$generatedPortraitSvg = trim($portrait->svg());
$portraitInitial = function_exists('mb_substr') ? mb_substr($name, 0, 1) : substr($name, 0, 1);
$portraitInitial = function_exists('mb_strtoupper') ? mb_strtoupper($portraitInitial) : strtoupper($portraitInitial);
?>
<section
    class="gmrc-adventuring-sheet"
    data-adventuring-sheet
    data-guild-dice-surface
    data-guild-dice-enabled="false"
>
    <header class="gmrc-adventuring-sheet__masthead">
        <div class="gmrc-adventuring-sheet__portrait" aria-label="<?php echo esc_attr(sprintf(__('%s portrait', 'great-marketrealm-companion'), $name)); ?>">
            <?php if ($isCustomPortrait): ?>
                <img src="<?php echo esc_url((string) $customPortraitUrl); ?>" alt="" loading="eager">
            <?php elseif ($generatedPortraitSvg !== ''): ?>
                <div class="gmrc-adventuring-sheet__portrait-svg" aria-hidden="true">
                    <?php echo $generatedPortraitSvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php else: ?>
                <span class="gmrc-adventuring-sheet__portrait-fallback" aria-hidden="true"><?php echo esc_html($portraitInitial); ?></span>
            <?php endif; ?>
        </div>
        <div class="gmrc-adventuring-sheet__identity">
            <p class="gmrc-eyebrow"><?php esc_html_e('Adventuring Sheet · Live Play', 'great-marketrealm-companion'); ?></p>
            <h1><?php echo esc_html($name); ?></h1>
            <p class="gmrc-adventuring-sheet__byline"><?php esc_html_e('Level', 'great-marketrealm-companion'); ?> <?php echo esc_html((string)$level); ?> <?php echo esc_html($race); ?> <?php echo esc_html($class); ?><?php echo $pathLabel !== '' ? ' · ' . esc_html($pathLabel) : ''; ?></p>
            <p class="gmrc-adventuring-sheet__background"><?php echo esc_html($background); ?></p>
        </div>
        <div class="gmrc-adventuring-sheet__actions">
            <button
                type="button"
                class="gmrc-adventuring-sheet__dice-toggle"
                data-adventuring-dice-toggle
                role="switch"
                aria-checked="false"
            >
                <span aria-hidden="true">🎲</span>
                <span><?php esc_html_e('Guild Dice', 'great-marketrealm-companion'); ?></span>
                <strong data-adventuring-dice-state><?php esc_html_e('Off', 'great-marketrealm-companion'); ?></strong>
            </button>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($printUrl); ?>"><?php esc_html_e('Printable / PDF Sheet', 'great-marketrealm-companion'); ?></a>
            <a class="gmrc-button gmrc-button--secondary" href="<?php echo esc_url($ledgerUrl); ?>"><?php esc_html_e('Open Full Ledger', 'great-marketrealm-companion'); ?></a>
            <span class="gmrc-adventuring-sheet__mode"><?php esc_html_e('Dice are optional. Advancement and archival detail stay in the Ledger.', 'great-marketrealm-companion'); ?></span>
        </div>
    </header>

    <section class="gmrc-adventuring-sheet__measures" aria-label="<?php echo esc_attr__('Core adventuring measures', 'great-marketrealm-companion'); ?>">
        <div><span>AC</span><strong><?php echo esc_html((string)$armourClass); ?></strong></div>
        <div><span><?php esc_html_e('Initiative', 'great-marketrealm-companion'); ?></span><button type="button" class="gmrc-adventure-roll" data-guild-roll="d20" data-roll-kind="initiative" data-roll-source="Initiative" data-roll-ability="DEX" data-roll-proficiency="none" data-roll-label="Initiative" data-roll-modifier="<?php echo esc_attr((string)$character->initiative()->modifier()); ?>"><?php echo esc_html($character->initiative()->signed()); ?></button></div>
        <div><span><?php esc_html_e('Speed', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html($character->speed()->formatted()); ?></strong></div>
        <div><span><?php esc_html_e('Proficiency', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html($character->proficiencyBonus()->signed()); ?></strong></div>
        <div><span><?php esc_html_e('Passive Perception', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html((string)$character->passivePerception()->value()); ?></strong></div>
        <div><span><?php esc_html_e('Coin Purse', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html($purse); ?></strong></div>
    </section>

    <div class="gmrc-adventuring-sheet__grid">
        <main class="gmrc-adventuring-sheet__main">
            <section class="gmrc-play-card gmrc-play-card--abilities">
                <header><h2><?php esc_html_e('Abilities', 'great-marketrealm-companion'); ?></h2><span class="gmrc-dice-enabled-note"><?php esc_html_e('Click a modifier to roll', 'great-marketrealm-companion'); ?></span></header>
                <div class="gmrc-adventuring-sheet__abilities">
                    <?php foreach ($abilities as $label => $score): ?>
                        <div class="gmrc-ability-tile">
                            <span><?php echo esc_html($label); ?></span>
                            <strong><?php echo esc_html((string)$score->value()); ?></strong>
                            <button type="button" class="gmrc-adventure-roll" data-guild-roll="d20" data-roll-kind="ability" data-roll-source="<?php echo esc_attr($abilityNames[$label]); ?>" data-roll-ability="<?php echo esc_attr($label); ?>" data-roll-proficiency="none" data-roll-label="<?php echo esc_attr($abilityNames[$label] . ' ' . __('Check', 'great-marketrealm-companion')); ?>" data-roll-modifier="<?php echo esc_attr((string)$score->modifier()); ?>"><?php echo esc_html(sprintf('%+d', $score->modifier())); ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="gmrc-play-card gmrc-play-card--vitals" data-vital-measures data-maximum-hp="<?php echo esc_attr((string)$hp->maximum()); ?>">
                <header><h2><?php esc_html_e('Hit Points', 'great-marketrealm-companion'); ?></h2><span data-vital-consciousness><?php echo esc_html($character->isConscious() ? __('Conscious', 'great-marketrealm-companion') : __('Unconscious', 'great-marketrealm-companion')); ?></span></header>
                <form method="post" action="<?php echo esc_url($appRequestUrl); ?>" data-vital-measures-form>
                    <input type="hidden" name="action" value="gmrc_app_request"><input type="hidden" name="gmrc_route" value="<?php echo esc_attr('characters/' . rawurlencode($characterId) . '/vital-measures'); ?>">
                    <?php wp_nonce_field('gmrc_character_vitals_' . $characterId, 'gmrc_nonce'); ?>
                    <div class="gmrc-adventuring-sheet__hp">
                        <label><?php esc_html_e('Current HP', 'great-marketrealm-companion'); ?><input type="number" name="current_hp" min="0" max="<?php echo esc_attr((string)$hp->maximum()); ?>" value="<?php echo esc_attr((string)$hp->current()); ?>" data-vital-current required></label>
                        <div class="gmrc-adventuring-sheet__max-hp"><span><?php esc_html_e('Maximum', 'great-marketrealm-companion'); ?></span><strong><?php echo esc_html((string)$hp->maximum()); ?></strong><small><?php esc_html_e('Guild certified', 'great-marketrealm-companion'); ?></small></div>
                        <label><?php esc_html_e('Temporary HP', 'great-marketrealm-companion'); ?><input type="number" name="temporary_hp" min="0" max="999" value="<?php echo esc_attr((string)$hp->temporary()); ?>" data-vital-temporary required></label>
                        <button class="gmrc-button gmrc-button--secondary" type="submit"><?php esc_html_e('Save HP', 'great-marketrealm-companion'); ?></button>
                    </div>
                </form>
            </section>

            <section class="gmrc-play-card">
                <header><h2><?php esc_html_e('Attacks', 'great-marketrealm-companion'); ?></h2><span class="gmrc-dice-enabled-note"><?php esc_html_e('Attack and damage become rollable', 'great-marketrealm-companion'); ?></span></header>
                <?php if ($attacks === []): ?><p class="gmrc-play-card__empty"><?php esc_html_e('No equipped weapon attacks.', 'great-marketrealm-companion'); ?></p><?php else: ?>
                <div class="gmrc-adventuring-sheet__attack-list">
                    <?php foreach ($attacks as $attack): ?>
                    <article>
                        <div><strong><?php echo esc_html((string)$attack['label']); ?></strong><small><?php echo esc_html((string)$attack['range']); ?></small></div>
                        <button type="button" class="gmrc-adventure-roll gmrc-adventure-roll--attack" data-guild-roll="d20" data-roll-kind="attack" data-roll-source="<?php echo esc_attr((string)$attack['label']); ?>" data-roll-ability="<?php echo esc_attr((string)($attack['ability'] ?? '')); ?>" data-roll-proficiency="proficient" data-roll-label="<?php echo esc_attr((string)$attack['label'] . ' — Attack'); ?>" data-roll-modifier="<?php echo esc_attr((string)$attack['attack_bonus']); ?>" data-roll-result-suffix="to hit" data-roll-critical-formula="<?php echo esc_attr((string)($attack['critical_damage_die'] ?? $attack['damage_die'])); ?>" data-roll-critical-modifier="<?php echo esc_attr((string)$attack['damage_modifier']); ?>" data-roll-critical-damage-type="<?php echo esc_attr((string)$attack['damage_type']); ?>"><?php echo esc_html(sprintf('%+d', (int)$attack['attack_bonus'])); ?> to hit</button>
                        <button type="button" class="gmrc-adventure-roll gmrc-adventure-roll--damage" data-guild-roll="damage" data-roll-kind="damage" data-roll-source="<?php echo esc_attr((string)$attack['label']); ?>" data-roll-ability="<?php echo esc_attr((string)($attack['ability'] ?? '')); ?>" data-roll-proficiency="proficient" data-roll-label="<?php echo esc_attr((string)$attack['label'] . ' — Damage'); ?>" data-roll-formula="<?php echo esc_attr((string)$attack['damage_die']); ?>" data-roll-modifier="<?php echo esc_attr((string)$attack['damage_modifier']); ?>" data-roll-damage-type="<?php echo esc_attr((string)$attack['damage_type']); ?>"><?php echo esc_html((string)$attack['damage_die']); ?><?php $dm=(int)$attack['damage_modifier']; echo $dm!==0 ? esc_html(sprintf(' %+d',$dm)) : ''; ?> <?php echo esc_html((string)$attack['damage_type']); ?></button>
                    </article>
                    <?php endforeach; ?>
                </div><?php endif; ?>
            </section>

            <?php if (! empty($arcana['has_spells'])): ?>
            <section class="gmrc-play-card gmrc-play-card--spells">
                <header>
                    <h2><?php esc_html_e('Spellcasting', 'great-marketrealm-companion'); ?></h2>
                    <span><?php echo esc_html((string)($arcana['casting_ability'] ?? '')); ?> · DC <?php echo esc_html((string)($arcana['save_dc'] ?? '—')); ?> · <?php if (isset($arcana['spell_attack'])): ?><button type="button" class="gmrc-adventure-roll gmrc-adventure-roll--inline" data-guild-roll="d20" data-roll-kind="spell-attack" data-roll-source="Spellcasting" data-roll-ability="<?php echo esc_attr((string)($arcana['casting_ability'] ?? '')); ?>" data-roll-proficiency="proficient" data-roll-label="Spell Attack" data-roll-modifier="<?php echo esc_attr((string)$arcana['spell_attack']); ?>">Attack <?php echo esc_html(sprintf('%+d',(int)$arcana['spell_attack'])); ?></button><?php else: ?>Attack —<?php endif; ?></span>
                </header>
                <?php if (! empty($arcana['slots'])): ?><div class="gmrc-adventuring-sheet__slots"><?php foreach ($arcana['slots'] as $slot): ?><span>Lv <?php echo esc_html((string)($slot['level']??'')); ?> <strong><?php echo esc_html((string)($slot['remaining'] ?? $slot['total'] ?? 0)); ?>/<?php echo esc_html((string)($slot['total']??0)); ?></strong></span><?php endforeach; ?></div><?php endif; ?>
                <?php foreach (($arcana['shelves'] ?? []) as $shelf): if (($shelf['kind'] ?? '') === 'feature') continue; ?><div class="gmrc-adventuring-sheet__spell-shelf"><h3><?php echo esc_html((string)$shelf['label']); ?></h3><div><?php foreach ($shelf['entries'] as $spell): ?><article><strong><?php echo esc_html((string)$spell['label']); ?></strong><small><?php echo esc_html((string)$spell['activation']); ?> · <?php echo esc_html((string)$spell['range']); ?> · <?php echo esc_html((string)$spell['duration']); ?></small><p><?php echo esc_html((string)$spell['description']); ?></p></article><?php endforeach; ?></div></div><?php endforeach; ?>
            </section>
            <?php endif; ?>

            <section class="gmrc-play-card"><header><h2><?php esc_html_e('Features & Gifts', 'great-marketrealm-companion'); ?></h2></header><div class="gmrc-adventuring-sheet__feature-list">
                <?php foreach (($pathGifts['gifts'] ?? []) as $gift): ?><article><strong><?php echo esc_html((string)($gift['label'] ?? $gift['name'] ?? 'Path Gift')); ?></strong><p><?php echo esc_html((string)($gift['detail'] ?? $gift['description'] ?? '')); ?></p></article><?php endforeach; ?>
                <?php foreach ($featureEntries as $feature): ?><article><strong><?php echo esc_html((string)$feature['label']); ?></strong><p><?php echo esc_html((string)$feature['description']); ?></p></article><?php endforeach; ?>
                <?php if (($pathGifts['gifts'] ?? []) === [] && $featureEntries === []): ?><p class="gmrc-play-card__empty"><?php esc_html_e('No additional active-play features are indexed here yet; the full Ledger retains all advancement records.', 'great-marketrealm-companion'); ?></p><?php endif; ?>
            </div></section>

            <section class="gmrc-play-card"><header><h2><?php esc_html_e('Equipment', 'great-marketrealm-companion'); ?></h2><span><?php echo esc_html((string)($inventory['total_weight'] ?? 0)); ?> / <?php echo esc_html((string)($inventory['capacity'] ?? 0)); ?> lb</span></header><div class="gmrc-adventuring-sheet__equipment"><?php foreach (($inventory['rows'] ?? []) as $item): ?><article class="<?php echo !empty($item['equipped']) ? 'is-equipped' : ''; ?>"><strong><?php echo esc_html((string)$item['label']); ?></strong><span>×<?php echo esc_html((string)$item['quantity']); ?></span><small><?php echo !empty($item['equipped']) ? 'Equipped · ' : ''; ?><?php echo esc_html((string)$item['category']); ?></small></article><?php endforeach; ?><?php if (($inventory['rows'] ?? [])===[]): ?><p class="gmrc-play-card__empty">The Adventurer’s Pack is empty.</p><?php endif; ?></div></section>
        </main>

        <aside class="gmrc-adventuring-sheet__sidebar">
            <section class="gmrc-play-card"><header><h2><?php esc_html_e('Saving Throws', 'great-marketrealm-companion'); ?></h2><span class="gmrc-dice-enabled-note"><?php esc_html_e('Roll when enabled', 'great-marketrealm-companion'); ?></span></header><ul class="gmrc-adventuring-sheet__compact-list"><?php foreach ($saveLabels as $key=>$label): $save=$saves->get($key); ?><li class="<?php echo $save->isProficient()?'is-proficient':''; ?>"><span><?php echo $save->isProficient()?'●':'○'; ?> <?php echo esc_html($label); ?></span><button type="button" class="gmrc-adventure-roll" data-guild-roll="d20" data-roll-kind="saving-throw" data-roll-source="<?php echo esc_attr($label . ' Saving Throw'); ?>" data-roll-ability="<?php echo esc_attr($label); ?>" data-roll-proficiency="<?php echo $save->isProficient() ? 'proficient' : 'none'; ?>" data-roll-label="<?php echo esc_attr($label . ' Saving Throw'); ?>" data-roll-modifier="<?php echo esc_attr((string)$save->modifier()); ?>"><?php echo esc_html($save->signed()); ?></button></li><?php endforeach; ?></ul></section>
            <section class="gmrc-play-card"><header><h2><?php esc_html_e('Skills', 'great-marketrealm-companion'); ?></h2><span class="gmrc-dice-enabled-note"><?php esc_html_e('Roll when enabled', 'great-marketrealm-companion'); ?></span></header><ul class="gmrc-adventuring-sheet__compact-list"><?php foreach ($skillLabels as $key=>$label): $skill=$skills->get($key); ?><li class="<?php echo $skill->isProficient()?'is-proficient':''; ?>"><span><?php echo $skill->hasExpertise()?'◆':($skill->isProficient()?'●':'○'); ?> <?php echo esc_html($label); ?></span><button type="button" class="gmrc-adventure-roll" data-guild-roll="d20" data-roll-kind="skill" data-roll-source="<?php echo esc_attr($label); ?>" data-roll-ability="<?php echo esc_attr($skillAbilities[$key] ?? ''); ?>" data-roll-proficiency="<?php echo $skill->hasExpertise() ? 'expertise' : ($skill->isProficient() ? 'proficient' : 'none'); ?>" data-roll-label="<?php echo esc_attr($label . ' Check'); ?>" data-roll-modifier="<?php echo esc_attr((string)$skill->modifier()); ?>"><?php echo esc_html($skill->signed()); ?></button></li><?php endforeach; ?></ul></section>
            <section class="gmrc-play-card"><header><h2><?php esc_html_e('Proficiencies', 'great-marketrealm-companion'); ?></h2></header><p><strong><?php esc_html_e('Languages:', 'great-marketrealm-companion'); ?></strong> <?php echo esc_html(implode(', ', array_map(static fn($l)=> (string)$l, $character->languages()->all()))); ?></p><p><strong><?php esc_html_e('Tools:', 'great-marketrealm-companion'); ?></strong> <?php echo esc_html(implode(', ', array_map(static fn($t)=> (string)$t, $character->toolProficiencies()->all()))); ?></p></section>
        </aside>
    </div>

    <?php require __DIR__ . '/partials/guild-dice-tray.php'; ?>
</section>
