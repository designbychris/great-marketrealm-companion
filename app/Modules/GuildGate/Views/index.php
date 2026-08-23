<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$old = is_array($old ?? null) ? $old : [];
$flash = is_array($flash ?? null) ? $flash : [];
$returnRoute = isset($returnRoute) ? (string) $returnRoute : 'dashboard';
$intent = isset($gateIntent) && $gateIntent === 'register' ? 'register' : 'login';
$showRegister = $intent === 'register';
$action = admin_url('admin-post.php');
$gateBase = home_url('/companion/');
$gateTabUrl = static fn (string $tab): string => add_query_arg(
    [
        'gate' => $tab,
        'return_route' => $returnRoute,
    ],
    $gateBase
);
$turnstile = is_array($turnstile ?? null) ? $turnstile : [];
$turnstileConfigured = ! empty($turnstileConfigured);
$siteKey = (string) ($turnstile['site_key'] ?? '');
?>
<section
    class="gmrc-guild-gate"
    aria-labelledby="gmrc-guild-gate-title"
    data-guild-gate
    data-guild-gate-active="<?php echo esc_attr($intent); ?>"
>
    <div class="gmrc-guild-gate__veil" aria-hidden="true"></div>

    <div class="gmrc-guild-gate__welcome">
        <span class="gmrc-guild-gate__seal" aria-hidden="true">✦</span>
        <p class="gmrc-guild-gate__eyebrow">The Great Marketrealm Companion</p>
        <h1 id="gmrc-guild-gate-title">The Guild Gate</h1>
        <p class="gmrc-guild-gate__lead">
            Present your Guild papers, or register a new name with the
            gatekeeper, before entering the Companion.
        </p>
        <div class="gmrc-guild-gate__promise" aria-label="Guild account benefits">
            <span>Keep your Characters</span>
            <span>Join Fellowships</span>
            <span>Choose Player or DM</span>
        </div>
    </div>

    <div class="gmrc-guild-gate__desk">
        <?php if (! empty($flash['error'])) : ?>
            <div class="gmrc-guild-gate__notice gmrc-guild-gate__notice--error" role="alert">
                <?php echo esc_html((string) $flash['error']); ?>
            </div>
        <?php endif; ?>

        <nav
            class="gmrc-guild-gate__switcher"
            role="tablist"
            aria-label="Guild Gate account access"
            data-guild-gate-tabs
        >
            <a
                id="gmrc-gate-tab-login"
                href="<?php echo esc_url($gateTabUrl('login')); ?>"
                role="tab"
                aria-selected="<?php echo $showRegister ? 'false' : 'true'; ?>"
                aria-controls="guild-gate-login"
                tabindex="<?php echo $showRegister ? '-1' : '0'; ?>"
                data-guild-gate-tab="login"
            >
                Log In
            </a>
            <a
                id="gmrc-gate-tab-register"
                href="<?php echo esc_url($gateTabUrl('register')); ?>"
                role="tab"
                aria-selected="<?php echo $showRegister ? 'true' : 'false'; ?>"
                aria-controls="guild-gate-register"
                tabindex="<?php echo $showRegister ? '0' : '-1'; ?>"
                data-guild-gate-tab="register"
            >
                Join the Guild
            </a>
        </nav>

        <div class="gmrc-guild-gate__forms" data-guild-gate-panels>
            <section
                id="guild-gate-login"
                class="gmrc-guild-gate__folio"
                role="tabpanel"
                aria-labelledby="gmrc-gate-tab-login"
                data-guild-gate-panel="login"
                <?php echo $showRegister ? 'hidden' : ''; ?>
            >
                <p class="gmrc-guild-gate__folio-kicker">Returning member</p>
                <h2>Open your ledger</h2>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="guild-gate/login">
                    <input type="hidden" name="return_route" value="<?php echo esc_attr($returnRoute); ?>">
                    <?php wp_nonce_field('gmrc_guild_gate_login', 'gmrc_nonce'); ?>

                    <label for="gmrc-gate-login">Username or email</label>
                    <input id="gmrc-gate-login" name="login" type="text" autocomplete="username" value="<?php echo esc_attr((string) ($old['login'] ?? '')); ?>" required>

                    <label for="gmrc-gate-password">Passphrase</label>
                    <input id="gmrc-gate-password" name="password" type="password" autocomplete="current-password" required>

                    <div class="gmrc-guild-gate__login-options">
                        <label class="gmrc-guild-gate__remember">
                            <input type="checkbox" name="remember" value="1">
                            <span>Keep my Guild seal ready on this device</span>
                        </label>
                        <a href="<?php echo esc_url(wp_lostpassword_url(home_url('/companion/'))); ?>">
                            Forgotten your passphrase?
                        </a>
                    </div>

                    <?php if ($turnstileConfigured && ! empty($turnstile['protect_login'])) : ?>
                        <div class="gmrc-guild-gate__turnstile">
                            <div class="cf-turnstile" data-sitekey="<?php echo esc_attr($siteKey); ?>" data-theme="auto"></div>
                            <small>Protected by Cloudflare Turnstile.</small>
                        </div>
                    <?php endif; ?>

                    <button type="submit">Enter the Companion</button>
                </form>
            </section>

            <section
                id="guild-gate-register"
                class="gmrc-guild-gate__folio"
                role="tabpanel"
                aria-labelledby="gmrc-gate-tab-register"
                data-guild-gate-panel="register"
                <?php echo $showRegister ? '' : 'hidden'; ?>
            >
                <p class="gmrc-guild-gate__folio-kicker">First visit</p>
                <h2>Register your Guild papers</h2>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="guild-gate/register">
                    <input type="hidden" name="return_route" value="<?php echo esc_attr($returnRoute); ?>">
                    <?php wp_nonce_field('gmrc_guild_gate_register', 'gmrc_nonce'); ?>

                    <label for="gmrc-gate-display-name">Display name</label>
                    <input id="gmrc-gate-display-name" name="display_name" type="text" autocomplete="name" maxlength="100" value="<?php echo esc_attr((string) ($old['display_name'] ?? '')); ?>" required>

                    <label for="gmrc-gate-username">Guild username</label>
                    <input id="gmrc-gate-username" name="username" type="text" autocomplete="username" maxlength="60" value="<?php echo esc_attr((string) ($old['username'] ?? '')); ?>" required>

                    <label for="gmrc-gate-email">Email address</label>
                    <input id="gmrc-gate-email" name="email" type="email" autocomplete="email" maxlength="100" value="<?php echo esc_attr((string) ($old['email'] ?? '')); ?>" required>

                    <fieldset class="gmrc-guild-gate__calling">
                        <legend>How will you enter the Guild?</legend>
                        <label>
                            <input type="radio" name="account_type" value="player" <?php checked(($old['account_type'] ?? 'player'), 'player'); ?>>
                            <span><strong>Player</strong><small>Characters, Fellowships and adventuring ledgers.</small></span>
                        </label>
                        <label>
                            <input type="radio" name="account_type" value="dm" <?php checked(($old['account_type'] ?? ''), 'dm'); ?>>
                            <span><strong>Dungeon Master</strong><small>Campaigns, players, sessions, encounters and live combat tools.</small></span>
                        </label>
                    </fieldset>

                    <label for="gmrc-gate-new-password">Passphrase</label>
                    <input id="gmrc-gate-new-password" name="password" type="password" autocomplete="new-password" minlength="10" aria-describedby="gmrc-gate-password-help" required>
                    <small id="gmrc-gate-password-help">Use at least 10 characters. The Guild never stores your plain passphrase.</small>

                    <?php if ($turnstileConfigured && ! empty($turnstile['protect_registration'])) : ?>
                        <div class="gmrc-guild-gate__turnstile">
                            <div class="cf-turnstile" data-sitekey="<?php echo esc_attr($siteKey); ?>" data-theme="auto"></div>
                            <small>Protected by Cloudflare Turnstile.</small>
                        </div>
                    <?php endif; ?>

                    <button type="submit">Seal my Guild papers</button>
                </form>
            </section>
        </div>
    </div>
</section>
