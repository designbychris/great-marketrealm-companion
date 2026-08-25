<?php

defined('ABSPATH') || exit;
$gateSecurity = is_array($gateSecurity ?? null) ? $gateSecurity : [];
$configured = ! empty($gateSecurityConfigured);
$companionSettings = is_array($companionSettings ?? null) ? $companionSettings : [];
$diagnostics = is_array($diagnostics ?? null) ? $diagnostics : [];
$counts = is_array($diagnostics['counts'] ?? null) ? $diagnostics['counts'] : ['healthy' => 0, 'attention' => 0, 'info' => 0];
?>
<div class="wrap gmrc-admin gmrc-stewards-office">
    <header class="gmrc-stewards-office__hero">
        <p class="gmrc-stewards-office__eyebrow">Companion Administration</p>
        <h1>The Steward's Office</h1>
        <p>The administrator's workspace for safeguarding the Companion, maintaining its canonical records, and configuring services shared across the Great Marketrealm.</p>
    </header>

    <?php if (isset($_GET['gmrc_saved'])) : ?>
        <div class="notice notice-success is-dismissible"><p>Gate Security settings have been sealed.</p></div>
    <?php endif; ?>
    <?php if (isset($_GET['gmrc_settings_saved'])) : ?>
        <div class="notice notice-success is-dismissible"><p>Companion Settings have been sealed.</p></div>
    <?php endif; ?>

    <section class="gmrc-steward-health" aria-labelledby="gmrc-steward-health-title">
        <div>
            <p class="gmrc-stewards-office__eyebrow">Steward Diagnostics</p>
            <h2 id="gmrc-steward-health-title"><?php echo esc_html((string) ($diagnostics['seal'] ?? 'Companion status unavailable.')); ?></h2>
            <p>Live checks cover the WordPress environment, media services, Gate Security, and the Companion's operational foundations.</p>
        </div>
        <div class="gmrc-steward-health__counts" aria-label="Diagnostic summary">
            <span class="is-healthy"><strong><?php echo esc_html((string) ($counts['healthy'] ?? 0)); ?></strong> Healthy</span>
            <span class="is-attention"><strong><?php echo esc_html((string) ($counts['attention'] ?? 0)); ?></strong> Attention</span>
            <span class="is-info"><strong><?php echo esc_html((string) ($counts['info'] ?? 0)); ?></strong> Informational</span>
        </div>
    </section>

    <?php $workshopCertification = is_array($workshopCertification ?? null) ? $workshopCertification : []; ?>
    <section class="gmrc-stewards-office__workshop-certification" aria-labelledby="gmrc-workshop-certification-title">
        <div>
            <p class="gmrc-stewards-office__eyebrow">Steward-authored content · Integration health</p>
            <h2 id="gmrc-workshop-certification-title">Workshop Certification</h2>
            <p>One lifecycle now governs Monsters, Spells, Backgrounds, Equipment, Callings &amp; Paths, and Folk &amp; Heritages: Draft, Published, Archived, with dependency-safe permanent deletion for disposable Steward records.</p>
        </div>
        <div class="gmrc-stewards-office__workshop-seal">
            <strong><?php echo ! empty($workshopCertification['certified']) ? 'Workshop system certified' : 'Workshop system needs attention'; ?></strong>
            <span><?php echo esc_html((string) ($workshopCertification['workshop_count'] ?? 0)); ?>/6 authoring rooms registered</span>
        </div>
        <?php $totals = is_array($workshopCertification['totals'] ?? null) ? $workshopCertification['totals'] : []; ?>
        <dl class="gmrc-stewards-office__workshop-totals">
            <div><dt>Records</dt><dd><?php echo esc_html((string) ($totals['records'] ?? 0)); ?></dd></div>
            <div><dt>Draft</dt><dd><?php echo esc_html((string) ($totals['draft'] ?? 0)); ?></dd></div>
            <div><dt>Published</dt><dd><?php echo esc_html((string) ($totals['published'] ?? 0)); ?></dd></div>
            <div><dt>Archived</dt><dd><?php echo esc_html((string) ($totals['archived'] ?? 0)); ?></dd></div>
        </dl>
        <p class="description"><?php echo esc_html((string) ($workshopCertification['policy'] ?? '')); ?></p>
    </section>

    <div class="gmrc-stewards-office__grid">
        <section class="gmrc-stewards-office__card gmrc-stewards-office__card--wide" id="diagnostics">
            <span class="dashicons dashicons-heart" aria-hidden="true"></span>
            <h2>System Diagnostics</h2>
            <div class="gmrc-diagnostic-list">
                <?php foreach ((array) ($diagnostics['checks'] ?? []) as $check) : ?>
                    <article class="gmrc-diagnostic gmrc-diagnostic--<?php echo esc_attr((string) ($check['status'] ?? 'info')); ?>">
                        <span class="gmrc-diagnostic__status" aria-hidden="true"></span>
                        <div><h3><?php echo esc_html((string) ($check['label'] ?? 'Check')); ?></h3><p><?php echo esc_html((string) ($check['detail'] ?? '')); ?></p></div>
                        <strong><?php echo esc_html(ucfirst((string) ($check['status'] ?? 'info'))); ?></strong>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (! empty($diagnostics['environment'])) : ?>
                <h3>Environment</h3>
                <dl class="gmrc-environment-list">
                    <?php foreach ((array) $diagnostics['environment'] as $label => $value) : ?>
                        <div><dt><?php echo esc_html((string) $label); ?></dt><dd><?php echo esc_html((string) $value); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </section>

        <section class="gmrc-stewards-office__card gmrc-stewards-office__card--wide" id="gate-security">
            <span class="dashicons dashicons-shield-alt" aria-hidden="true"></span>
            <h2>Gate Security</h2>
            <p>Protect the Guild Gate with Cloudflare Turnstile. Verification is performed server-side before login or registration is accepted.</p>
            <span class="gmrc-stewards-office__status"><?php echo esc_html($configured ? 'Turnstile configured' : 'Awaiting credentials'); ?></span>

            <form class="gmrc-gate-security-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="gmrc_save_gate_security">
                <?php wp_nonce_field('gmrc_save_gate_security', 'gmrc_gate_security_nonce'); ?>

                <label for="gmrc-turnstile-site-key"><strong>Turnstile Site Key</strong></label>
                <input id="gmrc-turnstile-site-key" name="site_key" type="text" autocomplete="off" value="<?php echo esc_attr((string) ($gateSecurity['site_key'] ?? '')); ?>" placeholder="0x4AAAA...">
                <p class="description">Public key used by the Guild Gate widget.</p>

                <label for="gmrc-turnstile-secret-key"><strong>Turnstile Secret Key</strong></label>
                <input id="gmrc-turnstile-secret-key" name="secret_key" type="password" autocomplete="new-password" value="" placeholder="<?php echo $configured ? 'Saved securely — leave blank to keep it' : 'Enter secret key'; ?>">
                <p class="description">Never rendered back into the page. Leave blank after saving to keep the current secret.</p>

                <fieldset>
                    <legend><strong>Protect these Guild Gate actions</strong></legend>
                    <label><input type="checkbox" name="protect_registration" value="1" <?php checked(! empty($gateSecurity['protect_registration'])); ?>> New member registration</label>
                    <label><input type="checkbox" name="protect_login" value="1" <?php checked(! empty($gateSecurity['protect_login'])); ?>> Member login</label>
                </fieldset>

                <div class="gmrc-gate-security-form__actions">
                    <?php submit_button('Save Gate Security', 'primary', 'submit', false); ?>
                    <?php if ($configured) : ?>
                        <label class="gmrc-gate-security-form__clear"><input type="checkbox" name="clear_secret" value="1"> Clear saved secret on save</label>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-hammer" aria-hidden="true"></span><h2>Monster Workshop</h2>
            <p>Create draft, published and archived Marketrealm creatures that join the shared Bestiary without modifying protected canonical records.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'monster-workshop'], admin_url('admin.php'))); ?>">Open Monster Workshop</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-wand" aria-hidden="true"></span><h2>Spell Workshop</h2>
            <p>Create new Marketrealm spells as Drafts, publish mechanically complete magic into Sage’s Spellbook and Character spell catalogues, or archive it without deleting its Steward record.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'spell-workshop'], admin_url('admin.php'))); ?>">Open Spell Workshop</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-id-alt" aria-hidden="true"></span><h2>Background Workshop</h2>
            <p>Create new Marketrealm backgrounds as Drafts, publish mechanically complete histories into future Character inscription and the Guild Library, or archive them without deleting their Steward record.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'background-workshop'], admin_url('admin.php'))); ?>">Open Background Workshop</a>
        </section>


        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-hammer" aria-hidden="true"></span><h2>Equipment &amp; Item Workshop</h2>
            <p>Create mundane weapons, armour, shields, tools, consumables and adventuring gear. Published items join the shared Armoury and Character satchels; archived records remain safe for existing adventurers.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'equipment-workshop'], admin_url('admin.php'))); ?>">Open Equipment Workshop</a>
        </section>


        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-buddicons-community" aria-hidden="true"></span><h2>Folk &amp; Heritage Workshop</h2>
            <p>Create new playable Marketrealm Folk and their Heritages. Published identities join future Character inscription while protected canonical peoples remain untouched.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'folk-workshop'], admin_url('admin.php'))); ?>">Open Folk Workshop</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-groups" aria-hidden="true"></span><h2>Class &amp; Calling Path Workshop</h2>
            <p>Create new playable Callings with certified hit dice and saving throws, then author their specialist Calling Paths. Published records join future Character inscription without replacing protected Handbook canon.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'calling-workshop'], admin_url('admin.php'))); ?>">Open Calling Workshop</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-book-alt" aria-hidden="true"></span><h2>Canonical Records</h2>
            <p>Curate the official Marketrealm Bestiary, including stat lines, traits, actions, and WordPress Media Library artwork.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-records'], admin_url('admin.php'))); ?>">Open Bestiary Stewardship</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-welcome-learn-more" aria-hidden="true"></span><h2>Canonical Callings</h2>
            <p>Browse the Players Handbook class and subclass register, maintain canonical wording, and keep private Steward balance notes without rewriting certified character mechanics.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-callings'], admin_url('admin.php'))); ?>">Open Calling Register</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-id" aria-hidden="true"></span><h2>Canonical Backgrounds</h2>
            <p>Curate Players Handbook background names, features and future-character proficiencies while preserving existing adventurers’ certified Background snapshots.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-backgrounds'], admin_url('admin.php'))); ?>">Open Background Register</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-editor-spellcheck" aria-hidden="true"></span><h2>Canonical Spells</h2>
            <p>Curate the Players Handbook spell names and canonical rules wording while keeping stable spell identity, source variants and access metadata protected.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'canonical-spells'], admin_url('admin.php'))); ?>">Open Spell Register</a>
        </section>

        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-archive" aria-hidden="true"></span><h2>Starting Equipment</h2>
            <p>Maintain certified class starter kits mapped to the Marketrealm Armoury. Changes affect future Characters only.</p>
            <a class="button button-primary" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'starting-equipment'], admin_url('admin.php'))); ?>">Open Equipment Packages</a>
        </section>

        <section class="gmrc-stewards-office__card gmrc-stewards-office__card--settings" id="companion-settings">
            <span class="dashicons dashicons-admin-settings" aria-hidden="true"></span><h2>Companion Settings</h2>
            <p>Small operational preferences shared by the Steward's Office live here. These settings do not alter game mechanics.</p>
            <form class="gmrc-companion-settings-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="gmrc_save_companion_settings">
                <?php wp_nonce_field('gmrc_save_companion_settings', 'gmrc_companion_settings_nonce'); ?>
                <label for="gmrc-steward-email"><strong>Steward contact email</strong></label>
                <input id="gmrc-steward-email" name="steward_email" type="email" value="<?php echo esc_attr((string) ($companionSettings['steward_email'] ?? '')); ?>">
                <p class="description">Reserved for future Steward notices and service alerts.</p>
                <label><input type="checkbox" name="show_environment_details" value="1" <?php checked(! empty($companionSettings['show_environment_details'])); ?>> Show detailed environment values in diagnostics</label>
                <?php submit_button('Save Companion Settings', 'secondary', 'submit', false); ?>
            </form>
        </section>
    </div>

    <aside class="gmrc-stewards-office__seal"><strong>Steward's seal:</strong> diagnostics are read-only, configuration changes require administrator capability and nonces, and saved Gate secrets are never rendered back into the Office.</aside>
</div>
