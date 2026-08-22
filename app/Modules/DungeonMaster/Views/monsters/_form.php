<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$editing = $monster instanceof \GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Monster;
$base = home_url('/companion/');
$route = $editing ? 'dungeon-master/monsters/' . $monster->id() : 'dungeon-master/monsters';
$method = $editing ? 'PUT' : 'POST';
$nonce = $editing ? 'gmrc_dm_monster_' . $monster->id() : 'gmrc_dm_monster_create';
$value = static fn (string $methodName, mixed $fallback = ''): mixed => $editing ? $monster->{$methodName}() : $fallback;
?>
<form class="gmrc-monster-form__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="gmrc_app_request">
    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
    <input type="hidden" name="_method" value="<?php echo esc_attr($method); ?>">
    <?php wp_nonce_field($nonce, 'gmrc_nonce'); ?>

    <label>Creature name
        <input type="text" name="name" maxlength="120" required value="<?php echo esc_attr((string) $value('name')); ?>">
    </label>

    <div class="gmrc-monster-form__row">
        <label>Creature type
            <input type="text" name="creature_type" maxlength="80" placeholder="e.g. ooze, construct, beast" value="<?php echo esc_attr((string) $value('creatureType')); ?>">
        </label>
        <label>Size
            <input type="text" name="size" maxlength="40" placeholder="e.g. Medium" value="<?php echo esc_attr((string) $value('size')); ?>">
        </label>
        <label>Challenge / threat label
            <input type="text" name="challenge" maxlength="30" placeholder="e.g. 2, Elite, Boss" value="<?php echo esc_attr((string) $value('challenge')); ?>">
        </label>
    </div>

    <div class="gmrc-monster-form__row gmrc-monster-form__row--combat">
        <label>Armor Class
            <input type="number" name="armor_class" min="0" max="99" required value="<?php echo esc_attr((string) $value('armorClass', 10)); ?>">
        </label>
        <label>Maximum HP
            <input type="number" name="max_hp" min="1" max="99999" required value="<?php echo esc_attr((string) $value('maxHp', 1)); ?>">
        </label>
        <label>Speed
            <input type="text" name="speed" maxlength="120" placeholder="e.g. 30 ft." value="<?php echo esc_attr((string) $value('speed')); ?>">
        </label>
    </div>

    <fieldset class="gmrc-monster-abilities">
        <legend>Ability scores</legend>
        <div class="gmrc-monster-abilities__grid">
            <?php foreach ([
                'strength' => 'STR',
                'dexterity' => 'DEX',
                'constitution' => 'CON',
                'intelligence' => 'INT',
                'wisdom' => 'WIS',
                'charisma' => 'CHA',
            ] as $field => $label) : ?>
                <label><?php echo esc_html($label); ?>
                    <input type="number" name="<?php echo esc_attr($field); ?>" min="1" max="30" required value="<?php echo esc_attr((string) $value($field, 10)); ?>">
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <label>Traits
        <textarea name="traits" rows="7" maxlength="10000" placeholder="Passive features, resistances, senses, special rules…"><?php echo esc_textarea((string) $value('traits')); ?></textarea>
    </label>
    <label>Actions
        <textarea name="actions" rows="8" maxlength="10000" placeholder="Attacks, spells, reactions or signature actions…"><?php echo esc_textarea((string) $value('actions')); ?></textarea>
    </label>
    <label>Dungeon Master notes
        <textarea name="notes" rows="6" maxlength="10000"><?php echo esc_textarea((string) $value('notes')); ?></textarea>
    </label>

    <button class="gmrc-monster-button" type="submit"><?php echo esc_html($editing ? 'Update Stat Block' : 'Enter Creature in Ledger'); ?></button>
</form>
