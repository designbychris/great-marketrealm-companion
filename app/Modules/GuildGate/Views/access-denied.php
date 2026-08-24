<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$guildUser = $guildUser ?? wp_get_current_user();
$logoutUrl = (string) ($logoutUrl ?? wp_logout_url(home_url('/companion/')));
$displayName = isset($guildUser->display_name) && is_string($guildUser->display_name)
    ? trim($guildUser->display_name)
    : '';
?>
<section
    class="gmrc-guild-gate gmrc-guild-gate--access-denied"
    aria-labelledby="gmrc-guild-access-title"
>
    <div class="gmrc-guild-gate__veil" aria-hidden="true"></div>

    <div class="gmrc-guild-gate__welcome">
        <span class="gmrc-guild-gate__seal" aria-hidden="true">✦</span>
        <p class="gmrc-guild-gate__eyebrow">The Great Marketrealm Companion</p>
        <h1 id="gmrc-guild-access-title">Guild papers required</h1>
        <p class="gmrc-guild-gate__lead">
            This WordPress session is signed in, but the account has not been
            granted entry to the Great Marketrealm Companion.
        </p>
    </div>

    <div class="gmrc-guild-gate__desk">
        <section class="gmrc-guild-gate__folio" aria-labelledby="gmrc-guild-access-folio-title">
            <p class="gmrc-guild-gate__folio-kicker">Admission certificate</p>
            <h2 id="gmrc-guild-access-folio-title">No active Guild calling</h2>

            <?php if ($displayName !== '') : ?>
                <p>
                    <strong><?php echo esc_html($displayName); ?></strong> is signed in to WordPress,
                    but this account does not currently hold Companion access.
                </p>
            <?php else : ?>
                <p>This signed-in WordPress account does not currently hold Companion access.</p>
            <?php endif; ?>

            <p>
                Sign out, then enter with a registered Marketrealm Player or Dungeon Master account.
                If Guild access was removed unexpectedly, contact the site Steward before continuing.
            </p>

            <div class="gmrc-guild-gate__access-actions">
                <a class="gmrc-guild-gate__access-action" href="<?php echo esc_url($logoutUrl); ?>">
                    Sign out and return to the Guild Gate
                </a>
            </div>
        </section>
    </div>
</section>
