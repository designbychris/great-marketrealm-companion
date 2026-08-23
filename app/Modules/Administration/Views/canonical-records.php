<?php

defined('ABSPATH') || exit;
$canonicalMonsters = is_array($canonicalMonsters ?? null) ? $canonicalMonsters : [];
$selectedMonster = $selectedMonster ?? null;
$selectedOverridden = ! empty($selectedOverridden);
$officeUrl = add_query_arg(['page' => 'gmrc-stewards-office'], admin_url('admin.php'));
$recordsUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-records'], admin_url('admin.php'));
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-canonical-steward">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Canonical Records · Bestiary Stewardship</p>
        <h1>The Canonical Bestiary</h1>
        <p>The Dungeon Master Guide remains the source baseline. Steward overrides can tune live canonical records and assign artwork without changing historical Encounter snapshots.</p>
        <p><a class="button" href="<?php echo esc_url($officeUrl); ?>">← Return to Steward's Office</a></p>
    </header>

    <?php if (isset($_GET['gmrc_canonical_saved'])) : ?>
        <div class="notice notice-success is-dismissible"><p>The canonical creature record has been sealed.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['gmrc_canonical_reset'])) : ?>
        <div class="notice notice-success is-dismissible"><p>The creature has been restored to its Dungeon Master Guide baseline.</p></div>
    <?php endif; ?>
    <?php if (! empty($_GET['gmrc_canonical_error'])) : ?>
        <div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_canonical_error'])); ?></p></div>
    <?php endif; ?>

    <div class="gmrc-canonical-steward__layout">
        <aside class="gmrc-canonical-steward__register" aria-labelledby="gmrc-canonical-register-title">
            <h2 id="gmrc-canonical-register-title">Creature Register</h2>
            <p><?php echo esc_html((string) count($canonicalMonsters)); ?> canonical records</p>
            <label class="screen-reader-text" for="gmrc-canonical-filter">Filter canonical creatures</label>
            <input id="gmrc-canonical-filter" type="search" placeholder="Search creatures…" data-gmrc-canonical-filter>
            <div class="gmrc-canonical-steward__list" data-gmrc-canonical-list>
                <?php foreach ($canonicalMonsters as $monster) :
                    $url = add_query_arg([
                        'page' => 'gmrc-stewards-office',
                        'section' => 'canonical-records',
                        'monster' => $monster->key(),
                    ], admin_url('admin.php'));
                    ?>
                    <a href="<?php echo esc_url($url); ?>" data-gmrc-canonical-name="<?php echo esc_attr(strtolower($monster->name())); ?>"<?php echo $selectedMonster && $selectedMonster->key() === $monster->key() ? ' aria-current="page"' : ''; ?>>
                        <?php if ($monster->imageAttachmentId() > 0) : ?>
                            <?php echo wp_get_attachment_image($monster->imageAttachmentId(), [52, 52], false, ['alt' => '']); ?>
                        <?php else : ?><span class="gmrc-canonical-steward__thumb" aria-hidden="true">📖</span><?php endif; ?>
                        <span><strong><?php echo esc_html($monster->name()); ?></strong><small><?php echo esc_html(($monster->challenge() !== '' ? 'CR ' . $monster->challenge() . ' · ' : '') . ($monster->creatureType() ?: 'Unclassified')); ?></small></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <?php if (! $selectedMonster) : ?>
                <section class="gmrc-stewards-office__card gmrc-canonical-steward__welcome">
                    <span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
                    <h2>Select a canonical creature</h2>
                    <p>Choose a Bestiary record from the register to review its Dungeon Master Guide statistics, tune its live canonical values, or assign an illustration from the WordPress Media Library.</p>
                </section>
            <?php else :
                $imageId = $selectedMonster->imageAttachmentId();
                $imageUrl = $imageId > 0 ? wp_get_attachment_image_url($imageId, 'medium') : false;
                ?>
                <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_canonical_monster">
                    <input type="hidden" name="monster_key" value="<?php echo esc_attr($selectedMonster->key()); ?>">
                    <?php wp_nonce_field('gmrc_save_canonical_monster_' . $selectedMonster->key(), 'gmrc_canonical_monster_nonce'); ?>

                    <header>
                        <div><p class="gmrc-stewards-office__eyebrow">Editing canonical record</p><h2><?php echo esc_html($selectedMonster->name()); ?></h2></div>
                        <span class="gmrc-stewards-office__status"><?php echo esc_html($selectedOverridden ? 'Steward override active' : 'Guide baseline'); ?></span>
                    </header>

                    <section class="gmrc-canonical-steward__artwork" aria-labelledby="gmrc-canonical-art-title">
                        <div class="gmrc-canonical-steward__paper-frame">
                            <img src="<?php echo esc_url($imageUrl ?: ''); ?>" alt="" data-gmrc-canonical-image-preview<?php echo $imageUrl ? '' : ' hidden'; ?>>
                            <span data-gmrc-canonical-image-empty<?php echo $imageUrl ? ' hidden' : ''; ?>>No illustration assigned</span>
                        </div>
                        <div><h3 id="gmrc-canonical-art-title">Bestiary Artwork</h3><p>Select an image already in the WordPress Media Library or upload a new one through the standard picker.</p>
                            <input type="hidden" name="image_attachment_id" value="<?php echo esc_attr((string) $imageId); ?>" data-gmrc-canonical-image-id>
                            <button class="button" type="button" data-gmrc-canonical-image-select>Choose / Replace Image</button>
                            <button class="button-link-delete" type="button" data-gmrc-canonical-image-remove>Remove Image</button>
                        </div>
                    </section>

                    <div class="gmrc-canonical-steward__fields">
                        <label><strong>Name</strong><input name="name" type="text" required value="<?php echo esc_attr($selectedMonster->name()); ?>"></label>
                        <label><strong>Creature type</strong><input name="type" type="text" value="<?php echo esc_attr($selectedMonster->creatureType()); ?>"></label>
                        <label><strong>Size</strong><input name="size" type="text" value="<?php echo esc_attr($selectedMonster->size()); ?>"></label>
                        <label><strong>Alignment</strong><input name="alignment" type="text" value="<?php echo esc_attr($selectedMonster->alignment()); ?>"></label>
                        <label><strong>Armor Class</strong><input name="ac" type="number" min="0" max="40" value="<?php echo esc_attr($selectedMonster->armorClass() === null ? '' : (string) $selectedMonster->armorClass()); ?>"></label>
                        <label><strong>Armor description</strong><input name="armor_description" type="text" value="<?php echo esc_attr($selectedMonster->armorDescription()); ?>" placeholder="Crisped Armor"></label>
                        <label><strong>Hit Points</strong><input name="hp" type="number" min="0" max="9999" value="<?php echo esc_attr($selectedMonster->maxHp() === null ? '' : (string) $selectedMonster->maxHp()); ?>"></label>
                        <label><strong>HP formula</strong><input name="hp_formula" type="text" value="<?php echo esc_attr($selectedMonster->hpFormula()); ?>" placeholder="6d8+18"></label>
                        <label><strong>Speed</strong><input name="speed" type="text" value="<?php echo esc_attr($selectedMonster->speed()); ?>"></label>
                        <label><strong>Challenge Rating</strong><input name="cr" type="text" value="<?php echo esc_attr($selectedMonster->challenge()); ?>"></label>
                    </div>

                    <fieldset class="gmrc-canonical-steward__abilities"><legend>Ability Scores</legend>
                        <?php foreach (['str' => ['STR', $selectedMonster->strength()], 'dex' => ['DEX', $selectedMonster->dexterity()], 'con' => ['CON', $selectedMonster->constitution()], 'int' => ['INT', $selectedMonster->intelligence()], 'wis' => ['WIS', $selectedMonster->wisdom()], 'cha' => ['CHA', $selectedMonster->charisma()]] as $name => [$label, $score]) : ?>
                            <label><span><?php echo esc_html($label); ?></span><input name="<?php echo esc_attr($name); ?>" type="number" min="1" max="30" value="<?php echo esc_attr($score === null ? '' : (string) $score); ?>"></label>
                        <?php endforeach; ?>
                    </fieldset>

                    <label><strong>Bestiary Description</strong><textarea name="description" rows="3"><?php echo esc_textarea($selectedMonster->description()); ?></textarea></label>
                    <div class="gmrc-canonical-steward__fields gmrc-canonical-steward__fields--rules">
                        <?php foreach ([
                            'saving_throws' => ['Saving Throws', $selectedMonster->savingThrows()],
                            'skills' => ['Skills', $selectedMonster->skills()],
                            'damage_resistances' => ['Damage Resistances', $selectedMonster->damageResistances()],
                            'damage_immunities' => ['Damage Immunities', $selectedMonster->damageImmunities()],
                            'damage_vulnerabilities' => ['Damage Vulnerabilities', $selectedMonster->damageVulnerabilities()],
                            'condition_immunities' => ['Condition Immunities', $selectedMonster->conditionImmunities()],
                            'senses' => ['Senses', $selectedMonster->senses()],
                            'languages' => ['Languages', $selectedMonster->languages()],
                        ] as $name => [$label, $value]) : ?>
                            <label><strong><?php echo esc_html($label); ?></strong><textarea name="<?php echo esc_attr($name); ?>" rows="2"><?php echo esc_textarea($value); ?></textarea></label>
                        <?php endforeach; ?>
                    </div>
                    <label><strong>Special Traits</strong><textarea name="traits" rows="7"><?php echo esc_textarea($selectedMonster->traits()); ?></textarea></label>
                    <label><strong>Spellcasting</strong><textarea name="spellcasting" rows="5"><?php echo esc_textarea($selectedMonster->spellcasting()); ?></textarea></label>
                    <label><strong>Actions</strong><textarea name="actions" rows="7"><?php echo esc_textarea($selectedMonster->actions()); ?></textarea></label>
                    <label><strong>Reactions</strong><textarea name="reactions" rows="4"><?php echo esc_textarea($selectedMonster->reactions()); ?></textarea></label>
                    <label><strong>Legendary Actions</strong><textarea name="legendary_actions" rows="6"><?php echo esc_textarea($selectedMonster->legendaryActions()); ?></textarea></label>
                    <label><strong>Mythic Features</strong><textarea name="mythic_actions" rows="6"><?php echo esc_textarea($selectedMonster->mythicActions()); ?></textarea></label>
                    <label><strong>Lair Actions</strong><textarea name="lair_actions" rows="5"><?php echo esc_textarea($selectedMonster->lairActions()); ?></textarea></label>
                    <label><strong>Steward / lore notes</strong><textarea name="notes" rows="5"><?php echo esc_textarea($selectedMonster->notes()); ?></textarea></label>
                    <?php if ($selectedMonster->sourceIssue() !== '') : ?><p class="gmrc-canonical-steward__source"><strong>Source note:</strong> <?php echo esc_html($selectedMonster->sourceIssue()); ?></p><?php endif; ?>

                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Seal Canonical Record</button><a class="button" href="<?php echo esc_url($recordsUrl); ?>">Back to register</a></div>
                </form>

                <?php if ($selectedOverridden) : ?>
                    <form class="gmrc-canonical-steward__reset" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Restore this creature to the Dungeon Master Guide baseline? Steward changes and artwork assignment will be removed.');">
                        <input type="hidden" name="action" value="gmrc_reset_canonical_monster"><input type="hidden" name="monster_key" value="<?php echo esc_attr($selectedMonster->key()); ?>">
                        <?php wp_nonce_field('gmrc_reset_canonical_monster_' . $selectedMonster->key(), 'gmrc_canonical_reset_nonce'); ?>
                        <button class="button-link-delete" type="submit">Restore Dungeon Master Guide baseline</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
