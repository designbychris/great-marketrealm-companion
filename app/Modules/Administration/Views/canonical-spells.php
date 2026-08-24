<?php

defined('ABSPATH') || exit;
$spells = is_array($spells ?? null) ? $spells : [];
$selectedSpell = $selectedSpell ?? null;
$selectedSpellOverridden = ! empty($selectedSpellOverridden);
$selectedSpellNotes = (string) ($selectedSpellNotes ?? '');
$officeUrl = add_query_arg(['page' => 'gmrc-stewards-office'], admin_url('admin.php'));
$registerUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-spells'], admin_url('admin.php'));
$label = static fn (?string $value, string $fallback): string => $value === null || $value === ''
    ? $fallback
    : ucwords(str_replace('-', ' ', $value));
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-spell-steward">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Canonical Records · Spell Stewardship</p>
        <h1>The Canonical Spell Register</h1>
        <p>The Great Marketrealm Players Handbook remains the baseline. Steward wording overrides flow into Sage’s Spellbook and canonical Character spell references without changing stable spell identity.</p>
        <p><a class="button" href="<?php echo esc_url($officeUrl); ?>">← Return to Steward's Office</a></p>
    </header>

    <?php if (isset($_GET['gmrc_spell_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The canonical Spell record has been sealed.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_spell_reset'])) : ?><div class="notice notice-success is-dismissible"><p>The Spell has been restored to its Players Handbook baseline.</p></div><?php endif; ?>
    <?php if (! empty($_GET['gmrc_spell_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_spell_error'])); ?></p></div><?php endif; ?>

    <div class="gmrc-canonical-steward__layout gmrc-spell-steward__layout">
        <aside class="gmrc-canonical-steward__register" aria-labelledby="gmrc-spell-steward-register-title">
            <h2 id="gmrc-spell-steward-register-title">Spell Register</h2>
            <p><?php echo esc_html((string) count($spells)); ?> Handbook records</p>
            <label class="screen-reader-text" for="gmrc-spell-steward-filter">Filter Spells</label>
            <input id="gmrc-spell-steward-filter" type="search" placeholder="Search spells…" data-gmrc-spell-filter>
            <div class="gmrc-canonical-steward__list" data-gmrc-spell-list>
                <?php foreach ($spells as $spell) :
                    $url = add_query_arg(['page'=>'gmrc-stewards-office','section'=>'canonical-spells','spell'=>$spell->key()], admin_url('admin.php'));
                    $search = strtolower(implode(' ', [
                        $spell->name(),
                        $spell->originalSpell() ?? '',
                        $spell->school() ?? '',
                        implode(' ', $spell->accessLabels()),
                    ]));
                ?>
                    <a href="<?php echo esc_url($url); ?>" data-gmrc-spell-name="<?php echo esc_attr($search); ?>"<?php echo $selectedSpell && $selectedSpell->key() === $spell->key() ? ' aria-current="page"' : ''; ?>>
                        <span class="gmrc-canonical-steward__thumb" aria-hidden="true">✦</span>
                        <span><strong><?php echo esc_html($spell->name()); ?></strong><small><?php echo esc_html($spell->originalSpell() ?? ($spell->kind() === 'marketrealm-original' ? 'Marketrealm original' : 'Renamed spell')); ?></small></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <?php if (! $selectedSpell) : ?>
                <section class="gmrc-stewards-office__card gmrc-canonical-steward__welcome"><span class="dashicons dashicons-editor-spellcheck" aria-hidden="true"></span><h2>Select a Spell record</h2><p>Choose a Players Handbook spell to inspect its protected identity and maintain the canonical Marketrealm name and rules wording.</p></section>
            <?php else : ?>
                <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_canonical_spell">
                    <input type="hidden" name="spell_key" value="<?php echo esc_attr($selectedSpell->key()); ?>">
                    <?php wp_nonce_field('gmrc_save_canonical_spell_' . $selectedSpell->key(), 'gmrc_canonical_spell_nonce'); ?>

                    <header><div><p class="gmrc-stewards-office__eyebrow"><?php echo esc_html($selectedSpell->kind() === 'renamed' ? 'Marketrealm Rename' : 'Marketrealm Original'); ?></p><h2><?php echo esc_html($selectedSpell->name()); ?></h2></div><?php if ($selectedSpellOverridden) : ?><span class="gmrc-stewards-office__status">Steward override active</span><?php endif; ?></header>

                    <div class="gmrc-calling-steward__seal"><strong>Canonical key</strong><code><?php echo esc_html($selectedSpell->key()); ?></code><strong>Source</strong><span>The Great Marketrealm - Players Handbook</span></div>

                    <label><strong>Marketrealm spell name</strong><input name="name" type="text" required value="<?php echo esc_attr($selectedSpell->name()); ?>"></label>

                    <section class="gmrc-spell-steward__identity" aria-labelledby="gmrc-spell-identity-title">
                        <h3 id="gmrc-spell-identity-title">Protected spell identity</h3>
                        <dl>
                            <div><dt>Kind</dt><dd><?php echo esc_html($selectedSpell->kind() === 'renamed' ? 'Marketrealm Rename' : 'Marketrealm Original'); ?></dd></div>
                            <div><dt>Original spell</dt><dd><?php echo esc_html($selectedSpell->originalSpell() ?? 'Not applicable'); ?></dd></div>
                            <div><dt>Level</dt><dd><?php echo esc_html($selectedSpell->level() === null ? 'Not stated in Handbook' : ($selectedSpell->level() === 0 ? 'Cantrip' : 'Level ' . $selectedSpell->level())); ?></dd></div>
                            <div><dt>School</dt><dd><?php echo esc_html($label($selectedSpell->school(), 'Not stated in Handbook')); ?></dd></div>
                            <div><dt>Access</dt><dd><?php echo esc_html($selectedSpell->accessLabels() === [] ? 'Not stated in Handbook' : implode(', ', $selectedSpell->accessLabels())); ?></dd></div>
                        </dl>
                        <aside class="gmrc-background-steward__history-note" role="note"><strong>Stable identity protected</strong><span>Kind, original spell, level, school, class access and source-variant identity remain read-only in this register.</span></aside>
                    </section>

                    <section class="gmrc-spell-steward__variants" aria-labelledby="gmrc-spell-variants-title">
                        <h3 id="gmrc-spell-variants-title">Canonical Handbook wording</h3>
                        <p>Each retained Handbook source variant remains independently editable so duplicate source records are never silently collapsed.</p>
                        <?php foreach ($selectedSpell->variants() as $variant) :
                            $variantId = (string) ($variant['source_variant'] ?? '');
                        ?>
                            <label><strong>Source variant <?php echo esc_html($variantId !== '' ? $variantId : 'record'); ?></strong><textarea name="variant_texts[<?php echo esc_attr($variantId); ?>]" rows="8" required><?php echo esc_textarea((string) ($variant['source_text'] ?? '')); ?></textarea></label>
                        <?php endforeach; ?>
                    </section>

                    <?php if ($selectedSpell->sourceIssues() !== []) : ?><aside class="gmrc-background-steward__source-gaps"><strong>Handbook source gaps</strong><ul><?php foreach ($selectedSpell->sourceIssues() as $issue) : ?><li><?php echo esc_html(ucwords(str_replace('-', ' ', $issue))); ?></li><?php endforeach; ?></ul></aside><?php endif; ?>

                    <label><strong>Steward notes</strong><textarea name="steward_notes" rows="5" placeholder="Balance notes, naming decisions, table rulings…"><?php echo esc_textarea($selectedSpellNotes); ?></textarea></label>
                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Seal Spell Record</button><a class="button" href="<?php echo esc_url($registerUrl); ?>">Back to register</a></div>
                </form>

                <?php if ($selectedSpellOverridden) : ?><form class="gmrc-canonical-steward__reset" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Restore this Spell to the Players Handbook baseline? Steward wording and notes will be removed.');"><input type="hidden" name="action" value="gmrc_reset_canonical_spell"><input type="hidden" name="spell_key" value="<?php echo esc_attr($selectedSpell->key()); ?>"><?php wp_nonce_field('gmrc_reset_canonical_spell_' . $selectedSpell->key(), 'gmrc_canonical_spell_reset_nonce'); ?><button class="button-link-delete" type="submit">Restore Players Handbook baseline</button></form><?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
