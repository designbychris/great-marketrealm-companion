<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$editing = $encounter instanceof \GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Encounter;
$selectedCharacters = $editing ? $encounter->characterIds() : [];
$selectedMonsters = [];
if ($editing) {
    foreach ($encounter->monsterGroups() as $group) {
        if (! is_array($group)) { continue; }
        $monsterId = (string) ($group['monster_id'] ?? '');
        if ($monsterId !== '') {
            $selectedMonsters[$monsterId] = max(1, (int) ($group['quantity'] ?? 1));
        }
    }
}
$monsterLedgerUrl = add_query_arg('gmrc_route', 'dungeon-master/monsters', home_url('/companion/'));
?>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="gmrc-encounter-form__body">
    <input type="hidden" name="action" value="gmrc_app_request">
    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
    <input type="hidden" name="_method" value="<?php echo $editing ? 'PUT' : 'POST'; ?>">
    <input type="hidden" name="gmrc_nonce" value="<?php echo esc_attr(wp_create_nonce('gmrc_dm_encounter_' . $campaign->id())); ?>">

    <label>Encounter title
        <input type="text" name="title" maxlength="120" required value="<?php echo esc_attr($editing ? $encounter->title() : ''); ?>">
    </label>

    <div class="gmrc-encounter-form__row">
        <label>Status
            <select name="status">
                <?php foreach (['prepared' => 'Prepared', 'running' => 'Running', 'completed' => 'Completed'] as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($editing ? $encounter->status() : 'prepared', $value); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Threat
            <select name="threat">
                <?php foreach (['low' => 'Low', 'moderate' => 'Moderate', 'high' => 'High', 'deadly' => 'Deadly'] as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($editing ? $encounter->threat() : 'moderate', $value); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Session
            <select name="session_id">
                <option value="">Unassigned / future encounter</option>
                <?php foreach (($sessions ?? []) as $entry) : ?>
                    <option value="<?php echo esc_attr($entry->id()); ?>" <?php selected($editing ? $encounter->sessionId() : '', $entry->id()); ?>>Session <?php echo esc_html((string) $entry->number() . ' — ' . $entry->title()); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <label>Location / environment
        <input type="text" name="location" maxlength="160" value="<?php echo esc_attr($editing ? $encounter->location() : ''); ?>">
    </label>

    <fieldset class="gmrc-encounter-bestiary">
        <legend>Monster Ledger adversaries</legend>
        <p>Choose reusable stat blocks and quantities. The Encounter stores a snapshot, so later edits to the Monster Ledger do not silently rewrite this preparation.</p>
        <?php if (($monsters ?? []) === [] && ($canonicalMonsters ?? []) === []) : ?>
            <p>No creatures are recorded yet. <a href="<?php echo esc_url($monsterLedgerUrl); ?>">Open the Monster Ledger</a> to create a reusable stat block.</p>
        <?php else : ?>
            <div class="gmrc-encounter-bestiary__grid">
                <?php foreach (array_merge(($canonicalMonsters ?? []), ($monsters ?? [])) as $monster) :
                    $quantity = (int) ($selectedMonsters[$monster->id()] ?? 0);
                    ?>
                    <?php $ready = ! method_exists($monster, 'encounterReady') || $monster->encounterReady(); ?>
                    <label class="gmrc-encounter-bestiary__creature<?php echo $monster->isArchived() ? ' is-archived' : ''; ?><?php echo ! $ready ? ' is-reference-only' : ''; ?>">
                        <span><strong><?php echo esc_html($monster->name()); ?></strong><small><?php echo method_exists($monster, 'isCanonical') ? 'Canonical · ' : 'My Ledger · '; ?>AC <?php echo esc_html($monster->armorClass() === null ? '—' : (string) $monster->armorClass()); ?> · HP <?php echo esc_html($monster->maxHp() === null ? '—' : (string) $monster->maxHp()); ?> · Init <?php echo esc_html($monster->initiativeModifier() === null ? '—' : sprintf('%+d', $monster->initiativeModifier())); ?><?php echo $monster->isArchived() ? ' · Archived' : ''; ?><?php echo ! $ready ? ' · Reference only' : ''; ?></small></span>
                        <span>Qty <input type="number" name="monster_quantities[<?php echo esc_attr($monster->id()); ?>]" min="0" max="20" value="<?php echo esc_attr((string) $quantity); ?>" aria-label="Quantity of <?php echo esc_attr($monster->name()); ?>" <?php disabled(! $ready); ?>></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </fieldset>

    <label>Loose adversaries / hazards
        <textarea name="adversaries" rows="6" maxlength="5000" placeholder="Use this for one-off creatures, hazards or opposition that does not need a reusable stat block. One per line works beautifully."><?php echo esc_textarea($editing ? $encounter->adversaries() : ''); ?></textarea>
    </label>

    <label>Dungeon Master notes
        <textarea name="notes" rows="8" maxlength="10000"><?php echo esc_textarea($editing ? $encounter->notes() : ''); ?></textarea>
    </label>

    <fieldset class="gmrc-encounter-party">
        <legend>Participating adventurers</legend>
        <p>Only Characters already attached to this Campaign through the Player Roster can be selected.</p>
        <?php if (($characters ?? []) === []) : ?><p>No campaign Characters are attached yet. The Encounter can still be prepared now.</p><?php endif; ?>
        <div class="gmrc-encounter-party__grid">
            <?php foreach (($characters ?? []) as $character) : $cid = $character->id()->value(); ?>
                <label><input type="checkbox" name="character_ids[]" value="<?php echo esc_attr($cid); ?>" <?php checked(in_array($cid, $selectedCharacters, true)); ?>><?php echo esc_html($character->name()->value()); ?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <button class="gmrc-encounter-button" type="submit"><?php echo $editing ? 'Update Encounter' : 'Pin Encounter to Board'; ?></button>
</form>
