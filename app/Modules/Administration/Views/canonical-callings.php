<?php

defined('ABSPATH') || exit;
$callings = is_array($callings ?? null) ? $callings : [];
$selectedCalling = $selectedCalling ?? null;
$selectedCallingOverridden = ! empty($selectedCallingOverridden);
$officeUrl = add_query_arg(['page' => 'gmrc-stewards-office'], admin_url('admin.php'));
$registerUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-callings'], admin_url('admin.php'));
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-calling-steward">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Canonical Records · Calling Stewardship</p>
        <h1>The Canonical Calling Register</h1>
        <p>The Great Marketrealm Players Handbook remains the baseline. Steward wording overrides sit above canon without rewriting certified character history or progression machinery.</p>
        <p><a class="button" href="<?php echo esc_url($officeUrl); ?>">← Return to Steward's Office</a></p>
    </header>
    <?php if (isset($_GET['gmrc_calling_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The canonical Calling record has been sealed.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_calling_reset'])) : ?><div class="notice notice-success is-dismissible"><p>The Calling has been restored to its Players Handbook baseline.</p></div><?php endif; ?>
    <?php if (! empty($_GET['gmrc_calling_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_calling_error'])); ?></p></div><?php endif; ?>

    <div class="gmrc-canonical-steward__layout gmrc-calling-steward__layout">
        <aside class="gmrc-canonical-steward__register" aria-labelledby="gmrc-calling-register-title">
            <h2 id="gmrc-calling-register-title">Calling Register</h2>
            <p><?php echo esc_html((string) count($callings)); ?> Handbook records</p>
            <label class="screen-reader-text" for="gmrc-calling-filter">Filter Callings and subclasses</label>
            <input id="gmrc-calling-filter" type="search" placeholder="Search Callings…" data-gmrc-calling-filter>
            <div class="gmrc-canonical-steward__list" data-gmrc-calling-list>
                <?php foreach ($callings as $calling) :
                    $url = add_query_arg(['page'=>'gmrc-stewards-office','section'=>'canonical-callings','kind'=>$calling->kind(),'calling'=>$calling->key()], admin_url('admin.php'));
                ?>
                    <a href="<?php echo esc_url($url); ?>" data-gmrc-calling-name="<?php echo esc_attr(strtolower($calling->name() . ' ' . $calling->parent())); ?>"<?php echo $selectedCalling && $selectedCalling->kind() === $calling->kind() && $selectedCalling->key() === $calling->key() ? ' aria-current="page"' : ''; ?>>
                        <span class="gmrc-canonical-steward__thumb" aria-hidden="true"><?php echo $calling->kind() === 'class' ? '⚔️' : '📜'; ?></span>
                        <span><strong><?php echo esc_html($calling->name()); ?></strong><small><?php echo esc_html($calling->kind() === 'class' ? 'Calling' : 'Subclass · ' . ucwords(str_replace('-', ' ', $calling->parent()))); ?></small></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <?php if (! $selectedCalling) : ?>
                <section class="gmrc-stewards-office__card gmrc-canonical-steward__welcome"><span class="dashicons dashicons-welcome-learn-more" aria-hidden="true"></span><h2>Select a Calling record</h2><p>Choose a class or subclass to inspect its Players Handbook identity and maintain its Steward-facing canonical wording.</p></section>
            <?php else : ?>
                <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_canonical_calling">
                    <input type="hidden" name="calling_kind" value="<?php echo esc_attr($selectedCalling->kind()); ?>">
                    <input type="hidden" name="calling_key" value="<?php echo esc_attr($selectedCalling->key()); ?>">
                    <?php wp_nonce_field('gmrc_save_canonical_calling_' . $selectedCalling->kind() . '_' . $selectedCalling->key(), 'gmrc_canonical_calling_nonce'); ?>
                    <header><div><p class="gmrc-stewards-office__eyebrow">Editing <?php echo esc_html($selectedCalling->kind()); ?> record</p><h2><?php echo esc_html($selectedCalling->name()); ?></h2></div><span class="gmrc-stewards-office__status"><?php echo esc_html($selectedCallingOverridden ? 'Steward override active' : 'Handbook baseline'); ?></span></header>
                    <div class="gmrc-calling-steward__seal"><strong>Canonical key</strong><code><?php echo esc_html($selectedCalling->kind() . ':' . $selectedCalling->key()); ?></code><strong>Source</strong><span><?php echo esc_html($selectedCalling->source()); ?></span></div>
                    <label><strong>Name</strong><input name="name" type="text" required value="<?php echo esc_attr($selectedCalling->name()); ?>"></label>
                    <label><strong>Handbook description</strong><textarea name="description" rows="5"><?php echo esc_textarea($selectedCalling->description()); ?></textarea></label>
                    <?php if ($selectedCalling->kind() === 'class') : ?><p class="gmrc-calling-steward__mechanic"><strong>Certified hit die:</strong> d<?php echo esc_html((string) $selectedCalling->hitDie()); ?> <span>Mechanical identity remains read-only in III.16.5.</span></p><?php else : ?><p class="gmrc-calling-steward__mechanic"><strong>Parent Calling:</strong> <?php echo esc_html(ucwords(str_replace('-', ' ', $selectedCalling->parent()))); ?> <span>Parentage remains read-only to protect existing characters.</span></p><?php endif; ?>
                    <?php if ($selectedCalling->traits() !== []) : ?><section><h3>Handbook traits</h3><ul><?php foreach ($selectedCalling->traits() as $trait) : ?><li><?php echo esc_html($trait); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
                    <label><strong>Steward notes</strong><textarea name="steward_notes" rows="5" placeholder="Balance notes, future revisions, table rulings…"><?php echo esc_textarea($selectedCalling->stewardNotes()); ?></textarea></label>
                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Seal Calling Record</button><a class="button" href="<?php echo esc_url($registerUrl); ?>">Back to register</a></div>
                </form>
                <?php if ($selectedCallingOverridden) : ?><form class="gmrc-canonical-steward__reset" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Restore this Calling to the Players Handbook baseline? Steward wording and notes will be removed.');"><input type="hidden" name="action" value="gmrc_reset_canonical_calling"><input type="hidden" name="calling_kind" value="<?php echo esc_attr($selectedCalling->kind()); ?>"><input type="hidden" name="calling_key" value="<?php echo esc_attr($selectedCalling->key()); ?>"><?php wp_nonce_field('gmrc_reset_canonical_calling_' . $selectedCalling->kind() . '_' . $selectedCalling->key(), 'gmrc_canonical_calling_reset_nonce'); ?><button class="button-link-delete" type="submit">Restore Players Handbook baseline</button></form><?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
