<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Administration\Workshop\MonsterWorkshop;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Bestiary\Models\CanonicalMonster;

defined('ABSPATH') || exit;

$recordsUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'monster-workshop'], admin_url('admin.php'));
$isNew = ! $selectedMonster instanceof CanonicalMonster;
$status = $isNew ? MonsterWorkshop::STATUS_DRAFT : $selectedMonster->publicationStatus();
$imageId = $isNew ? 0 : $selectedMonster->imageAttachmentId();
$imageUrl = $imageId > 0 ? wp_get_attachment_image_url($imageId, 'medium') : false;
$value = static fn (string $method, string $fallback = ''): string => $isNew ? $fallback : (string) $selectedMonster->{$method}();
?>
<div class="wrap gmrc-stewards-office gmrc-canonical-steward gmrc-monster-workshop">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Phase III.16.19 · Custom Content Registry</p>
        <h1>The Steward's Workshop</h1>
        <p>Create shared Marketrealm creatures without editing PHP. Drafts remain private to the Steward's Office; published creatures join the Bestiary for every Dungeon Master; archived records remain preserved but unavailable for new encounters.</p>
    </header>

    <?php if (isset($_GET['gmrc_workshop_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The Steward creature has been sealed.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_workshop_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_workshop_error'])); ?></p></div><?php endif; ?>

    <div class="gmrc-canonical-steward__layout">
        <aside class="gmrc-canonical-steward__register">
            <div class="gmrc-canonical-steward__register-head"><h2>Steward Creatures</h2><a class="button button-primary" href="<?php echo esc_url($recordsUrl); ?>">Add Monster</a></div>
            <?php if ($stewardMonsters === []) : ?><p>No custom creatures have been written yet.</p><?php endif; ?>
            <div class="gmrc-canonical-steward__list">
                <?php foreach ($stewardMonsters as $key => $data) :
                    $monster = new CanonicalMonster($data);
                    $url = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'monster-workshop', 'monster' => $key], admin_url('admin.php'));
                    ?>
                    <a href="<?php echo esc_url($url); ?>"<?php echo ! $isNew && $selectedMonster->key() === $key ? ' aria-current="page"' : ''; ?>>
                        <span class="gmrc-canonical-steward__thumb" aria-hidden="true">✦</span>
                        <span><strong><?php echo esc_html($monster->name()); ?></strong><small><?php echo esc_html(ucfirst($monster->publicationStatus()) . ($monster->challenge() !== '' ? ' · CR ' . $monster->challenge() : '')); ?></small></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <p><a href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-records'], admin_url('admin.php'))); ?>">Review protected canonical Bestiary →</a></p>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="gmrc_save_steward_monster">
                <input type="hidden" name="monster_key" value="<?php echo esc_attr($isNew ? '' : $selectedMonster->key()); ?>">
                <?php wp_nonce_field('gmrc_save_steward_monster_' . ($isNew ? 'new' : $selectedMonster->key()), 'gmrc_steward_monster_nonce'); ?>
                <header><div><p class="gmrc-stewards-office__eyebrow"><?php echo $isNew ? 'New shared creature' : 'Editing Steward creation'; ?></p><h2><?php echo esc_html($isNew ? 'Untitled Monster' : $selectedMonster->name()); ?></h2></div><span class="gmrc-stewards-office__status"><?php echo esc_html(ucfirst($status)); ?></span></header>

                <section class="gmrc-canonical-steward__artwork"><div class="gmrc-canonical-steward__paper-frame"><img src="<?php echo esc_url($imageUrl ?: ''); ?>" alt="" data-gmrc-canonical-image-preview<?php echo $imageUrl ? '' : ' hidden'; ?>><span data-gmrc-canonical-image-empty<?php echo $imageUrl ? ' hidden' : ''; ?>>No illustration assigned</span></div><div><h3>Bestiary Artwork</h3><p>Choose an image from the WordPress Media Library.</p><input type="hidden" name="image_attachment_id" value="<?php echo esc_attr((string) $imageId); ?>" data-gmrc-canonical-image-id><button class="button" type="button" data-gmrc-canonical-image-select>Choose / Replace Image</button><button class="button-link-delete" type="button" data-gmrc-canonical-image-remove>Remove Image</button></div></section>

                <div class="gmrc-canonical-steward__fields">
                    <label><strong>Name</strong><input name="name" type="text" required value="<?php echo esc_attr($value('name')); ?>"></label>
                    <label><strong>Publication</strong><select name="status"><option value="draft"<?php selected($status, 'draft'); ?>>Draft</option><option value="published"<?php selected($status, 'published'); ?>>Published</option><option value="archived"<?php selected($status, 'archived'); ?>>Archived</option></select></label>
                    <label><strong>Creature type</strong><input name="type" type="text" value="<?php echo esc_attr($value('creatureType')); ?>"></label>
                    <label><strong>Size</strong><input name="size" type="text" value="<?php echo esc_attr($value('size')); ?>"></label>
                    <label><strong>Alignment</strong><input name="alignment" type="text" value="<?php echo esc_attr($value('alignment')); ?>"></label>
                    <label><strong>Armor Class</strong><input name="ac" type="number" min="0" max="40" value="<?php echo esc_attr($isNew || $selectedMonster->armorClass() === null ? '' : (string) $selectedMonster->armorClass()); ?>"></label>
                    <label><strong>Armor description</strong><input name="armor_description" type="text" value="<?php echo esc_attr($value('armorDescription')); ?>"></label>
                    <label><strong>Hit Points</strong><input name="hp" type="number" min="0" max="9999" value="<?php echo esc_attr($isNew || $selectedMonster->maxHp() === null ? '' : (string) $selectedMonster->maxHp()); ?>"></label>
                    <label><strong>HP formula</strong><input name="hp_formula" type="text" value="<?php echo esc_attr($value('hpFormula')); ?>"></label>
                    <label><strong>Speed</strong><input name="speed" type="text" value="<?php echo esc_attr($value('speed')); ?>"></label>
                    <label><strong>Challenge Rating</strong><input name="cr" type="text" value="<?php echo esc_attr($value('challenge')); ?>"></label>
                </div>
                <fieldset class="gmrc-canonical-steward__abilities"><legend>Ability Scores</legend><?php foreach (['str'=>['STR','strength'],'dex'=>['DEX','dexterity'],'con'=>['CON','constitution'],'int'=>['INT','intelligence'],'wis'=>['WIS','wisdom'],'cha'=>['CHA','charisma']] as $name => [$label,$method]) : $score=$isNew?null:$selectedMonster->{$method}(); ?><label><span><?php echo esc_html($label); ?></span><input name="<?php echo esc_attr($name); ?>" type="number" min="1" max="30" value="<?php echo esc_attr($score===null?'':(string)$score); ?>"></label><?php endforeach; ?></fieldset>

                <label><strong>Bestiary Description</strong><textarea name="description" rows="3"><?php echo esc_textarea($value('description')); ?></textarea></label>
                <section class="gmrc-canonical-steward__field-guide"><h3>Guild Field Guide</h3><p>Only spoiler-safe lore is projected to Players.</p><label class="gmrc-canonical-steward__toggle"><input name="field_guide_visible" type="checkbox" value="1"<?php checked(! $isNew && $selectedMonster->fieldGuideVisible()); ?>><span><strong>Visible in the Guild Field Guide</strong><small>Published creatures only.</small></span></label><label><strong>Player-safe description</strong><textarea name="player_description" rows="4"><?php echo esc_textarea($value('playerDescription')); ?></textarea></label></section>
                <div class="gmrc-canonical-steward__fields gmrc-canonical-steward__fields--rules"><?php foreach (['saving_throws'=>['Saving Throws','savingThrows'],'skills'=>['Skills','skills'],'damage_resistances'=>['Damage Resistances','damageResistances'],'damage_immunities'=>['Damage Immunities','damageImmunities'],'damage_vulnerabilities'=>['Damage Vulnerabilities','damageVulnerabilities'],'condition_immunities'=>['Condition Immunities','conditionImmunities'],'senses'=>['Senses','senses'],'languages'=>['Languages','languages']] as $name=>[$label,$method]) : ?><label><strong><?php echo esc_html($label); ?></strong><textarea name="<?php echo esc_attr($name); ?>" rows="2"><?php echo esc_textarea($value($method)); ?></textarea></label><?php endforeach; ?></div>
                <?php foreach (['traits'=>['Special Traits','traits',7],'spellcasting'=>['Spellcasting','spellcasting',5],'actions'=>['Actions','actions',7],'reactions'=>['Reactions','reactions',4],'legendary_actions'=>['Legendary Actions','legendaryActions',6],'mythic_actions'=>['Mythic Features','mythicActions',6],'lair_actions'=>['Lair Actions','lairActions',5],'notes'=>['Steward / lore notes','notes',5]] as $name=>[$label,$method,$rows]) : ?><label><strong><?php echo esc_html($label); ?></strong><textarea name="<?php echo esc_attr($name); ?>" rows="<?php echo esc_attr((string)$rows); ?>"><?php echo esc_textarea($value($method)); ?></textarea></label><?php endforeach; ?>
                <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit"><?php echo $isNew ? 'Create Steward Creature' : 'Seal Steward Creature'; ?></button><a class="button" href="<?php echo esc_url($recordsUrl); ?>">New creature</a></div>
            </form>
        </main>
    </div>
</div>
