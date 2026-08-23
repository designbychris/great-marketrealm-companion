<?php

defined('ABSPATH') || exit;

$base = home_url('/companion/');
$route = 'dungeon-master/campaigns/' . $campaign->id()
    . '/encounters/' . $encounter->id() . '/initiative';
$combatants = $table->combatants();
$combatLog = array_reverse($table->log());
$archived = $campaign->isArchived();
$conditionOptions = [
    'Blinded',
    'Charmed',
    'Deafened',
    'Frightened',
    'Grappled',
    'Incapacitated',
    'Paralyzed',
    'Poisoned',
    'Prone',
    'Restrained',
    'Stunned',
    'Unconscious',
];
?>
<section class="gmrc-initiative-table gmrc-combat-console" data-initiative-table data-combat-console>
    <header class="gmrc-initiative-table__hero">
        <div>
            <p class="gmrc-dm-desk__eyebrow">
                Initiative Table · Round
                <?php echo esc_html((string) $table->round()); ?>
            </p>
            <h1>The Combat Console</h1>
            <p>
                <strong><?php echo esc_html($encounter->title()); ?></strong>
                · Run the live fight, track consequences, and preserve the
                story of each round without mutating Player-owned ledgers.
            </p>
        </div>
        <a
            class="gmrc-initiative-button gmrc-initiative-button--ghost"
            href="<?php echo esc_url(add_query_arg(
                'gmrc_route',
                'dungeon-master/campaigns/' . $campaign->id()
                    . '/encounters/' . $encounter->id(),
                $base
            )); ?>"
        >← Encounter</a>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <p class="gmrc-initiative-flash" role="status">
            <?php echo esc_html($flash['success']); ?>
        </p>
    <?php endif; ?>

    <?php if ($archived) : ?>
        <p class="gmrc-initiative-notice">
            This Campaign is archived. Its final Combat Console is preserved
            as read-only combat history.
        </p>
    <?php endif; ?>

    <form
        method="post"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        class="gmrc-initiative-form"
        data-combat-form
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
        <input type="hidden" name="_method" value="PUT">
        <input
            type="hidden"
            name="gmrc_nonce"
            value="<?php echo esc_attr(wp_create_nonce(
                'gmrc_dm_initiative_' . $campaign->id() . '_' . $encounter->id()
            )); ?>"
        >
        <input type="hidden" name="round" value="<?php echo esc_attr((string) $table->round()); ?>">
        <input type="hidden" name="turn_index" value="<?php echo esc_attr((string) $table->turnIndex()); ?>">
        <input type="hidden" name="remove_id" value="" data-remove-id>

        <div class="gmrc-initiative-toolbar" aria-label="Combat controls">
            <div>
                <span>Round</span>
                <strong><?php echo esc_html((string) $table->round()); ?></strong>
            </div>
            <div>
                <span>Active turn</span>
                <strong>
                    <?php echo esc_html(
                        $combatants === []
                            ? '—'
                            : (string) ($table->turnIndex() + 1)
                                . ' / ' . count($combatants)
                    ); ?>
                </strong>
            </div>
            <div>
                <span>Combatants</span>
                <strong><?php echo esc_html((string) count($combatants)); ?></strong>
            </div>

            <?php if (! $archived) : ?>
                <div class="gmrc-combat-console__turn-controls">
                    <button
                        class="gmrc-initiative-button gmrc-initiative-button--ghost"
                        type="submit"
                        name="initiative_action"
                        value="rewind"
                        <?php disabled($combatants === []); ?>
                    >← Rewind</button>
                    <button
                        class="gmrc-initiative-button"
                        type="submit"
                        name="initiative_action"
                        value="advance"
                        <?php disabled($combatants === []); ?>
                    >Advance →</button>
                    <button
                        class="gmrc-initiative-button gmrc-initiative-button--ghost"
                        type="submit"
                        name="initiative_action"
                        value="sort"
                        <?php disabled($combatants === []); ?>
                    >Sort Initiative</button>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($combatants === []) : ?>
            <article class="gmrc-initiative-empty">
                <h2>The live table is waiting for combatants</h2>
                <p>
                    Add participating Characters or creatures on the Encounter
                    Board, or use the unexpected combatant panel below.
                </p>
            </article>
        <?php else : ?>
            <ol class="gmrc-initiative-order">
                <?php foreach ($combatants as $index => $combatant) : ?>
                    <?php
                    $active = $index === $table->turnIndex();
                    $state = (string) ($combatant['state'] ?? (
                        ! empty($combatant['defeated']) ? 'defeated' : 'standing'
                    ));
                    $conditions = array_filter(array_map(
                        'trim',
                        explode(',', (string) ($combatant['conditions'] ?? ''))
                    ));
                    ?>
                    <li
                        class="gmrc-initiative-combatant<?php echo $active ? ' is-active' : ''; ?><?php echo $state === 'defeated' ? ' is-defeated' : ''; ?>"
                        <?php echo $active ? 'aria-current="step"' : ''; ?>
                        data-combatant
                    >
                        <input
                            type="hidden"
                            name="combatants[<?php echo esc_attr((string) $index); ?>][id]"
                            value="<?php echo esc_attr((string) $combatant['id']); ?>"
                        >

                        <header class="gmrc-initiative-combatant__identity">
                            <span>
                                <?php echo esc_html(
                                    $combatant['type'] === 'character'
                                        ? 'Adventurer'
                                        : ($combatant['type'] === 'ally' ? 'Guest Ally' : 'Adversary')
                                ); ?>
                            </span>
                            <strong><?php echo esc_html((string) $combatant['name']); ?></strong>
                            <?php if (
                                $combatant['type'] === 'adversary'
                                && ! empty($combatant['source_id'])
                            ) : ?>
                                <small>
                                    AC <?php echo esc_html((string) ($combatant['armor_class'] ?? '—')); ?>
                                    <?php echo ! empty($combatant['challenge'])
                                        ? ' · ' . esc_html((string) $combatant['challenge'])
                                        : ''; ?>
                                </small>
                            <?php endif; ?>
                            <?php if ($active) : ?>
                                <em>Taking turn</em>
                            <?php endif; ?>
                            <span class="gmrc-combat-state gmrc-combat-state--<?php echo esc_attr($state); ?>">
                                <?php echo esc_html(ucfirst($state)); ?>
                            </span>
                        </header>

                        <div class="gmrc-combat-console__vitals">
                            <label>
                                Initiative
                                <span class="gmrc-initiative-roll">
                                    <input
                                        type="number"
                                        min="-20"
                                        max="99"
                                        name="combatants[<?php echo esc_attr((string) $index); ?>][initiative]"
                                        value="<?php echo esc_attr((string) ($combatant['initiative'] ?? 0)); ?>"
                                        <?php disabled($archived); ?>
                                    >
                                    <?php if (! $archived) : ?>
                                        <button
                                            type="button"
                                            data-roll-initiative
                                            data-modifier="<?php echo esc_attr((string) ($combatant['modifier'] ?? 0)); ?>"
                                            aria-label="Roll initiative for <?php echo esc_attr((string) $combatant['name']); ?>"
                                        >🎲</button>
                                    <?php endif; ?>
                                </span>
                            </label>

                            <label>
                                HP
                                <span class="gmrc-initiative-hp">
                                    <input
                                        type="number"
                                        min="0"
                                        max="99999"
                                        name="combatants[<?php echo esc_attr((string) $index); ?>][current_hp]"
                                        value="<?php echo esc_attr((string) ($combatant['current_hp'] ?? 0)); ?>"
                                        data-current-hp
                                        <?php disabled($archived); ?>
                                    >
                                    <span>/</span>
                                    <input
                                        type="number"
                                        min="0"
                                        max="99999"
                                        name="combatants[<?php echo esc_attr((string) $index); ?>][max_hp]"
                                        value="<?php echo esc_attr((string) ($combatant['max_hp'] ?? 0)); ?>"
                                        data-max-hp
                                        <?php disabled($archived); ?>
                                        <?php echo $combatant['type'] === 'character'
                                            ? 'readonly aria-readonly="true"'
                                            : ''; ?>
                                    >
                                </span>
                            </label>

                            <label>
                                Temporary HP
                                <input
                                    type="number"
                                    min="0"
                                    max="99999"
                                    name="combatants[<?php echo esc_attr((string) $index); ?>][temp_hp]"
                                    value="<?php echo esc_attr((string) ($combatant['temp_hp'] ?? 0)); ?>"
                                    data-temp-hp
                                    <?php disabled($archived); ?>
                                >
                            </label>
                        </div>

                        <?php if (! $archived) : ?>
                            <div class="gmrc-combat-console__quick" aria-label="Quick hit point adjustment">
                                <label>
                                    Amount
                                    <input type="number" min="1" max="99999" value="1" data-quick-amount>
                                </label>
                                <button type="button" data-quick-vital="damage">Damage</button>
                                <button type="button" data-quick-vital="heal">Heal</button>
                            </div>
                        <?php endif; ?>

                        <section class="gmrc-combat-console__conditions" aria-label="Conditions">
                            <label>
                                Conditions
                                <input
                                    type="text"
                                    maxlength="180"
                                    name="combatants[<?php echo esc_attr((string) $index); ?>][conditions]"
                                    value="<?php echo esc_attr((string) ($combatant['conditions'] ?? '')); ?>"
                                    placeholder="Poisoned, prone…"
                                    data-conditions-input
                                    <?php disabled($archived); ?>
                                >
                            </label>
                            <?php if (! $archived) : ?>
                                <div class="gmrc-combat-console__condition-chips">
                                    <?php foreach ($conditionOptions as $condition) : ?>
                                        <button
                                            type="button"
                                            data-condition="<?php echo esc_attr($condition); ?>"
                                            aria-pressed="<?php echo in_array($condition, $conditions, true) ? 'true' : 'false'; ?>"
                                        ><?php echo esc_html($condition); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </section>

                        <div class="gmrc-combat-console__state-row">
                            <label>
                                Combat state
                                <select
                                    name="combatants[<?php echo esc_attr((string) $index); ?>][state]"
                                    data-combat-state
                                    <?php disabled($archived); ?>
                                >
                                    <option value="standing" <?php selected($state, 'standing'); ?>>Standing</option>
                                    <option value="unconscious" <?php selected($state, 'unconscious'); ?>>Unconscious</option>
                                    <option value="defeated" <?php selected($state, 'defeated'); ?>>Defeated</option>
                                </select>
                            </label>
                            <label class="gmrc-combat-console__concentration">
                                <input
                                    type="checkbox"
                                    name="combatants[<?php echo esc_attr((string) $index); ?>][concentrating]"
                                    value="1"
                                    <?php checked(! empty($combatant['concentrating'])); ?>
                                    <?php disabled($archived); ?>
                                >
                                Concentrating
                            </label>
                        </div>

                        <label class="gmrc-combat-console__notes">
                            DM combat note
                            <textarea
                                name="combatants[<?php echo esc_attr((string) $index); ?>][notes]"
                                rows="2"
                                maxlength="500"
                                placeholder="Recharge, hidden effect, morale…"
                                <?php disabled($archived); ?>
                            ><?php echo esc_textarea((string) ($combatant['notes'] ?? '')); ?></textarea>
                        </label>

                        <?php if (! $archived) : ?>
                            <button
                                class="gmrc-combat-console__remove"
                                type="submit"
                                name="initiative_action"
                                value="remove"
                                data-remove-combatant="<?php echo esc_attr((string) $combatant['id']); ?>"
                                data-confirm="Remove <?php echo esc_attr((string) $combatant['name']); ?> from this live combat?"
                            >Remove from combat</button>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>

        <?php if (! $archived) : ?>
            <section class="gmrc-combat-console__add" aria-labelledby="gmrc-add-combatant-title">
                <div>
                    <p class="gmrc-dm-desk__eyebrow">Unexpected arrival</p>
                    <h2 id="gmrc-add-combatant-title">Add a combatant mid-fight</h2>
                    <p>
                        For reinforcements, summoned allies, hazards, or anyone
                        who was not on the prepared Encounter Board.
                    </p>
                </div>
                <label>
                    Name
                    <input type="text" name="new_name" maxlength="120">
                </label>
                <label>
                    Side
                    <select name="new_type">
                        <option value="adversary">Adversary</option>
                        <option value="ally">Ally</option>
                    </select>
                </label>
                <label>
                    Max HP
                    <input type="number" name="new_max_hp" min="0" max="99999" value="0">
                </label>
                <label>
                    Initiative modifier
                    <input type="number" name="new_modifier" min="-20" max="20" value="0">
                </label>
                <button
                    class="gmrc-initiative-button"
                    type="submit"
                    name="initiative_action"
                    value="add"
                >Add to Combat</button>
            </section>

            <div class="gmrc-initiative-actions">
                <button
                    class="gmrc-initiative-button"
                    type="submit"
                    name="initiative_action"
                    value="save"
                >Save Console</button>
                <button
                    class="gmrc-initiative-button gmrc-initiative-button--ghost"
                    type="submit"
                    name="initiative_action"
                    value="reset"
                    data-confirm="Reset the live table from the prepared Encounter? Current combatant changes remain in the combat log."
                >Reset from Encounter</button>
                <button
                    class="gmrc-initiative-button gmrc-initiative-button--danger"
                    type="submit"
                    name="initiative_action"
                    value="complete"
                    data-confirm="Complete this Encounter and preserve the final Combat Console?"
                >Complete Encounter</button>
            </div>
        <?php endif; ?>

        <p class="gmrc-initiative-live" aria-live="polite" data-initiative-live></p>
    </form>

    <aside class="gmrc-combat-log" aria-labelledby="gmrc-combat-log-title">
        <header>
            <p class="gmrc-dm-desk__eyebrow">Persistent encounter history</p>
            <h2 id="gmrc-combat-log-title">Combat Log</h2>
        </header>
        <?php if ($combatLog === []) : ?>
            <p>No combat events have been recorded yet.</p>
        <?php else : ?>
            <ol>
                <?php foreach ($combatLog as $entry) : ?>
                    <li>
                        <span>Round <?php echo esc_html((string) ($entry['round'] ?? 1)); ?></span>
                        <strong><?php echo esc_html((string) ($entry['message'] ?? 'Combat updated.')); ?></strong>
                        <?php if (! empty($entry['recorded_at'])) : ?>
                            <small><?php echo esc_html((string) $entry['recorded_at']); ?></small>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </aside>
</section>
