<?php

defined('ABSPATH') || exit;
$gateSecurity = is_array($gateSecurity ?? null) ? $gateSecurity : [];
$configured = ! empty($gateSecurityConfigured);
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

    <div class="gmrc-stewards-office__grid">
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
            <span class="dashicons dashicons-book-alt" aria-hidden="true"></span><h2>Canonical Records</h2>
            <p>Future stewardship tools will manage Bestiary entries, Callings, and other certified game records.</p><span class="gmrc-stewards-office__status">Foundation ready</span>
        </section>
        <section class="gmrc-stewards-office__card">
            <span class="dashicons dashicons-admin-settings" aria-hidden="true"></span><h2>Companion Settings</h2>
            <p>Shared application configuration will live behind administrator capability checks.</p><span class="gmrc-stewards-office__status">Foundation ready</span>
        </section>
    </div>

    <aside class="gmrc-stewards-office__seal"><strong>Steward's seal:</strong> Turnstile credentials are administrator-only, the secret is never printed back into the Office, and Guild Gate verification occurs on the server.</aside>
</div>
