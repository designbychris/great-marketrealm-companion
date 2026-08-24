<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Skills;

defined('ABSPATH') || exit;

$campaignId = (string) ($campaignId ?? '');
$campaignName = (string) ($campaignName ?? 'Campaign');
$characterId = $character->id()->value();
$abilities = [
    'Strength' => $character->abilityScores()->strength(),
    'Dexterity' => $character->abilityScores()->dexterity(),
    'Constitution' => $character->abilityScores()->constitution(),
    'Intelligence' => $character->abilityScores()->intelligence(),
    'Wisdom' => $character->abilityScores()->wisdom(),
    'Charisma' => $character->abilityScores()->charisma(),
];
$savingThrowLabels = [
    'strength' => 'Strength',
    'dexterity' => 'Dexterity',
    'constitution' => 'Constitution',
    'intelligence' => 'Intelligence',
    'wisdom' => 'Wisdom',
    'charisma' => 'Charisma',
];
$skillLabels = [
    'acrobatics' => 'Acrobatics', 'animal-handling' => 'Animal Handling',
    'arcana' => 'Arcana', 'athletics' => 'Athletics', 'deception' => 'Deception',
    'history' => 'History', 'insight' => 'Insight', 'intimidation' => 'Intimidation',
    'investigation' => 'Investigation', 'medicine' => 'Medicine', 'nature' => 'Nature',
    'perception' => 'Perception', 'performance' => 'Performance', 'persuasion' => 'Persuasion',
    'religion' => 'Religion', 'sleight-of-hand' => 'Sleight of Hand',
    'stealth' => 'Stealth', 'survival' => 'Survival',
];
$backUrl = add_query_arg(
    'gmrc_route',
    'dungeon-master/campaigns/' . rawurlencode($campaignId) . '/players',
    home_url('/companion/')
);
$hitPoints = $character->hitPoints();
$saves = $character->savingThrows();
$skills = $character->skills();
$inventory = is_array($inventory ?? null) ? $inventory : ['rows' => []];
$attacks = is_array($attacks ?? null) ? $attacks : [];
$arcana = is_array($arcana ?? null) ? $arcana : [];
?>
<section class="gmrc-command-centre gmrc-dm-character-ledger" aria-labelledby="gmrc-dm-character-ledger-title">
    <header class="gmrc-command-centre__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">Campaign Record · Read Only</p>
            <h1 id="gmrc-dm-character-ledger-title"><?php echo esc_html($character->name()->value()); ?></h1>
            <p><?php echo esc_html($campaignName); ?> · This certified projection belongs to another Guild member and cannot be edited from the Dungeon Master’s Desk.</p>
        </div>
        <div class="gmrc-command-centre__hero-actions">
            <a class="gmrc-campaign-button" href="<?php echo esc_url($backUrl); ?>">Back to Player Roster</a>
        </div>
    </header>

    <div class="gmrc-command-centre__stats" aria-label="Character overview">
        <div><strong><?php echo esc_html((string) $character->level()->value()); ?></strong><span>Level</span></div>
        <div><strong><?php echo esc_html((string) $inventoryArmourClass); ?></strong><span>Armour Class</span></div>
        <div><strong><?php echo esc_html($character->initiative()->signed()); ?></strong><span>Initiative</span></div>
        <div><strong><?php echo esc_html($character->speed()->formatted()); ?></strong><span>Speed</span></div>
        <div><strong><?php echo esc_html((string) $character->passivePerception()->value()); ?></strong><span>Passive Perception</span></div>
    </div>

    <div class="gmrc-command-centre__grid">
        <article class="gmrc-command-card">
            <div class="gmrc-ledger-page__portrait">
                <?php echo $this->component('components.media.illuminated-portrait', [
                    'portrait' => $portrait,
                    'portraitPersisted' => true,
                    'controlsEnabled' => false,
                ]); ?>
            </div>
            <p class="gmrc-dm-desk__eyebrow">Identity</p>
            <h2>Guild Record</h2>
            <dl class="gmrc-ledger-background">
                <div><dt>Race</dt><dd><?php echo esc_html($character->race()->label()); ?></dd></div>
                <div><dt>Class</dt><dd><?php echo esc_html($character->characterClass()->label()); ?></dd></div>
                <div><dt>Background</dt><dd><?php echo esc_html($character->background()->label()); ?></dd></div>
                <div><dt>Experience</dt><dd><?php echo esc_html((string) $character->experience()->value()); ?></dd></div>
            </dl>
        </article>

        <article class="gmrc-command-card">
            <p class="gmrc-dm-desk__eyebrow">Adventuring Measures</p>
            <h2>Hit Points</h2>
            <dl class="gmrc-ledger-background">
                <div><dt>Current</dt><dd><?php echo esc_html((string) $hitPoints->current()); ?></dd></div>
                <div><dt>Maximum</dt><dd><?php echo esc_html((string) $hitPoints->maximum()); ?></dd></div>
                <div><dt>Temporary</dt><dd><?php echo esc_html((string) $hitPoints->temporary()); ?></dd></div>
                <div><dt>Status</dt><dd><?php echo esc_html($character->isConscious() ? 'Conscious' : 'Unconscious'); ?></dd></div>
            </dl>
        </article>
    </div>

    <section class="gmrc-command-card" aria-labelledby="gmrc-dm-abilities-title">
        <p class="gmrc-dm-desk__eyebrow">Core Statistics</p>
        <h2 id="gmrc-dm-abilities-title">Abilities & Saving Throws</h2>
        <div class="gmrc-command-centre__stats">
            <?php foreach ($abilities as $label => $score) : ?>
                <div><strong><?php echo esc_html((string) $score->value()); ?></strong><span><?php echo esc_html($label . ' ' . ($score->modifier() >= 0 ? '+' : '') . $score->modifier()); ?></span></div>
            <?php endforeach; ?>
        </div>
        <dl class="gmrc-ledger-saves">
            <?php foreach ($savingThrowLabels as $key => $label) : $save = $saves->get($key); ?>
                <div class="<?php echo $save->isProficient() ? 'is-proficient' : ''; ?>">
                    <dt><?php echo $save->isProficient() ? '● ' : ''; ?><?php echo esc_html($label); ?></dt>
                    <dd><?php echo esc_html($save->signed()); ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </section>

    <section class="gmrc-command-card" aria-labelledby="gmrc-dm-skills-title">
        <p class="gmrc-dm-desk__eyebrow">Training</p>
        <h2 id="gmrc-dm-skills-title">Skills</h2>
        <dl class="gmrc-ledger-skill-list">
            <?php foreach ($skillLabels as $key => $label) : $skill = $skills->get($key); ?>
                <div class="<?php echo $skill->hasExpertise() ? 'has-expertise' : ($skill->isProficient() ? 'is-proficient' : ''); ?>">
                    <dt><?php echo $skill->hasExpertise() ? '◆ ' : ($skill->isProficient() ? '● ' : ''); ?><?php echo esc_html($label); ?></dt>
                    <dd><?php echo esc_html($skill->signed()); ?> <small><?php echo esc_html(ucfirst(Skills::governingAbility($key))); ?></small></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </section>

    <div class="gmrc-command-centre__grid">
        <article class="gmrc-command-card">
            <p class="gmrc-dm-desk__eyebrow">Combat</p>
            <h2>Attacks</h2>
            <?php if ($attacks === []) : ?><p>No prepared attacks are recorded.</p><?php else : ?>
                <ul class="gmrc-command-centre__notes">
                    <?php foreach ($attacks as $attack) : ?>
                        <li><strong><?php echo esc_html((string) $attack['label']); ?></strong><span><?php echo esc_html(($attack['attack_bonus'] >= 0 ? '+' : '') . (string) $attack['attack_bonus'] . ' to hit · ' . $attack['damage_die'] . ($attack['damage_modifier'] >= 0 ? ' +' : ' ') . (string) $attack['damage_modifier'] . ' ' . $attack['damage_type']); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>

        <article class="gmrc-command-card">
            <p class="gmrc-dm-desk__eyebrow">Arcana</p>
            <h2>Spellcasting</h2>
            <?php if (empty($arcana['has_spells'])) : ?><p>No spellcasting register is recorded.</p><?php else : ?>
                <dl class="gmrc-ledger-background">
                    <div><dt>Casting ability</dt><dd><?php echo esc_html((string) ($arcana['casting_ability'] ?? '—')); ?></dd></div>
                    <div><dt>Spell attack</dt><dd><?php echo esc_html((string) ($arcana['spell_attack'] ?? '—')); ?></dd></div>
                    <div><dt>Save DC</dt><dd><?php echo esc_html((string) ($arcana['save_dc'] ?? '—')); ?></dd></div>
                </dl>
                <?php if (! empty($arcana['entries']) && is_array($arcana['entries'])) : ?>
                    <ul class="gmrc-command-centre__notes">
                        <?php foreach ($arcana['entries'] as $entry) : ?><li><strong><?php echo esc_html((string) ($entry['name'] ?? $entry['label'] ?? 'Spell')); ?></strong></li><?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    </div>

    <section class="gmrc-command-card" aria-labelledby="gmrc-dm-equipment-title">
        <p class="gmrc-dm-desk__eyebrow">Carried Gear</p>
        <h2 id="gmrc-dm-equipment-title">Equipment</h2>
        <?php if (empty($inventory['rows'])) : ?><p>The Adventurer’s Pack is empty.</p><?php else : ?>
            <ul class="gmrc-command-centre__notes">
                <?php foreach ($inventory['rows'] as $row) : ?>
                    <li><strong><?php echo esc_html((string) ($row['label'] ?? 'Item')); ?></strong><span>×<?php echo esc_html((string) ($row['quantity'] ?? 1)); ?><?php echo ! empty($row['equipped']) ? ' · Equipped' : ''; ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <div class="gmrc-command-centre__grid">
        <article class="gmrc-command-card"><p class="gmrc-dm-desk__eyebrow">Languages</p><h2>Known Languages</h2><p><?php echo esc_html(implode(', ', $character->languages()->values()) ?: 'None recorded'); ?></p></article>
        <article class="gmrc-command-card"><p class="gmrc-dm-desk__eyebrow">Tools</p><h2>Tool Proficiencies</h2><p><?php echo esc_html(implode(', ', $character->toolProficiencies()->values()) ?: 'None recorded'); ?></p></article>
    </div>

    <p class="gmrc-command-centre__notice" role="note"><strong>Private Player notes are not included in this Campaign projection.</strong> Character ownership and every editing action remain with the Player.</p>
</section>
