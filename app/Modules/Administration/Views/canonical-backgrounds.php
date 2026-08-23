<?php

defined('ABSPATH') || exit;
$backgrounds = is_array($backgrounds ?? null) ? $backgrounds : [];
$selectedBackground = $selectedBackground ?? null;
$selectedBackgroundOverridden = ! empty($selectedBackgroundOverridden);
$officeUrl = add_query_arg(['page' => 'gmrc-stewards-office'], admin_url('admin.php'));
$registerUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-backgrounds'], admin_url('admin.php'));
$labels = static fn (array $items): string => implode(', ', array_map(
    static fn (string $item): string => ucwords(str_replace('-', ' ', $item)),
    $items
));
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-background-steward">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Canonical Records · Background Stewardship</p>
        <h1>The Canonical Background Register</h1>
        <p>The Great Marketrealm Players Handbook remains the baseline. Steward wording overrides sit above canon while certified proficiencies stay protected for existing adventurers.</p>
        <p><a class="button" href="<?php echo esc_url($officeUrl); ?>">← Return to Steward's Office</a></p>
    </header>

    <?php if (isset($_GET['gmrc_background_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The canonical Background record has been sealed.</p></div><?php endif; ?>
    <?php if (isset($_GET['gmrc_background_reset'])) : ?><div class="notice notice-success is-dismissible"><p>The Background has been restored to its Players Handbook baseline.</p></div><?php endif; ?>
    <?php if (! empty($_GET['gmrc_background_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_background_error'])); ?></p></div><?php endif; ?>

    <div class="gmrc-canonical-steward__layout gmrc-background-steward__layout">
        <aside class="gmrc-canonical-steward__register" aria-labelledby="gmrc-background-steward-register-title">
            <h2 id="gmrc-background-steward-register-title">Background Register</h2>
            <p><?php echo esc_html((string) count($backgrounds)); ?> Handbook records</p>
            <label class="screen-reader-text" for="gmrc-background-steward-filter">Filter Backgrounds</label>
            <input id="gmrc-background-steward-filter" type="search" placeholder="Search Backgrounds…" data-gmrc-background-filter>
            <div class="gmrc-canonical-steward__list" data-gmrc-background-list>
                <?php foreach ($backgrounds as $background) :
                    $url = add_query_arg(['page'=>'gmrc-stewards-office','section'=>'canonical-backgrounds','background'=>$background->key()], admin_url('admin.php'));
                ?>
                    <a href="<?php echo esc_url($url); ?>" data-gmrc-background-name="<?php echo esc_attr(strtolower($background->name() . ' ' . $background->featureName())); ?>"<?php echo $selectedBackground && $selectedBackground->key() === $background->key() ? ' aria-current="page"' : ''; ?>>
                        <span class="gmrc-canonical-steward__thumb" aria-hidden="true">📖</span>
                        <span><strong><?php echo esc_html($background->name()); ?></strong><small><?php echo esc_html($background->featureName()); ?></small></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <?php if (! $selectedBackground) : ?>
                <section class="gmrc-stewards-office__card gmrc-canonical-steward__welcome"><span class="dashicons dashicons-id" aria-hidden="true"></span><h2>Select a Background record</h2><p>Choose a Handbook background to inspect its certified proficiencies and maintain its canonical presentation wording.</p></section>
            <?php else : ?>
                <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_canonical_background">
                    <input type="hidden" name="background_key" value="<?php echo esc_attr($selectedBackground->key()); ?>">
                    <?php wp_nonce_field('gmrc_save_canonical_background_' . $selectedBackground->key(), 'gmrc_canonical_background_nonce'); ?>

                    <header><div><p class="gmrc-stewards-office__eyebrow">Optional Marketrealm Background</p><h2><?php echo esc_html($selectedBackground->name()); ?></h2></div><?php if ($selectedBackgroundOverridden) : ?><span class="gmrc-stewards-office__status">Steward override active</span><?php endif; ?></header>

                    <div class="gmrc-calling-steward__seal"><strong>Canonical key</strong><code><?php echo esc_html($selectedBackground->key()); ?></code><strong>Source</strong><span><?php echo esc_html($selectedBackground->source()); ?></span></div>

                    <label><strong>Name</strong><input name="name" type="text" required value="<?php echo esc_attr($selectedBackground->name()); ?>"></label>
                    <label><strong>Feature name</strong><input name="feature_name" type="text" required value="<?php echo esc_attr($selectedBackground->featureName()); ?>"></label>
                    <label><strong>Feature text</strong><textarea name="feature_detail" rows="5" required><?php echo esc_textarea($selectedBackground->featureDetail()); ?></textarea></label>

                    <section class="gmrc-background-steward__mechanics" aria-labelledby="gmrc-background-mechanics-title">
                        <h3 id="gmrc-background-mechanics-title">Certified proficiencies</h3>
                        <dl class="gmrc-definition-list"><div><dt>Skills</dt><dd><?php echo esc_html($labels($selectedBackground->skills())); ?></dd></div><div><dt>Tool</dt><dd><?php echo esc_html($selectedBackground->toolLabel() !== '' ? $selectedBackground->toolLabel() : $labels($selectedBackground->tools())); ?></dd></div></dl>
                        <p>Skills and tools remain read-only here so existing Character records keep their certified mechanical identity. A dedicated mechanics bridge will govern validated future-character changes.</p>
                    </section>

                    <?php if ($selectedBackground->sourceIssues() !== []) : ?><aside class="gmrc-background-steward__source-gaps"><strong>Handbook source gaps</strong><ul><?php foreach ($selectedBackground->sourceIssues() as $issue) : ?><li><?php echo esc_html(ucwords(str_replace('-', ' ', $issue))); ?></li><?php endforeach; ?></ul></aside><?php endif; ?>

                    <label><strong>Steward notes</strong><textarea name="steward_notes" rows="5" placeholder="Balance notes, future revisions, table rulings…"><?php echo esc_textarea($selectedBackground->stewardNotes()); ?></textarea></label>
                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Seal Background Record</button><a class="button" href="<?php echo esc_url($registerUrl); ?>">Back to register</a></div>
                </form>

                <?php if ($selectedBackgroundOverridden) : ?><form class="gmrc-canonical-steward__reset" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Restore this Background to the Players Handbook baseline? Steward wording and notes will be removed.');"><input type="hidden" name="action" value="gmrc_reset_canonical_background"><input type="hidden" name="background_key" value="<?php echo esc_attr($selectedBackground->key()); ?>"><?php wp_nonce_field('gmrc_reset_canonical_background_' . $selectedBackground->key(), 'gmrc_canonical_background_reset_nonce'); ?><button class="button-link-delete" type="submit">Restore Players Handbook baseline</button></form><?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
