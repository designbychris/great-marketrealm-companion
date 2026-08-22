<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$old = is_array($old ?? null) ? $old : [];
$flash = is_array($flash ?? null) ? $flash : [];
$userId = (int) ($guildUser->ID ?? 0);
$displayName = (string) ($old['display_name'] ?? ($guildUser->display_name ?? ''));
$email = (string) ($old['email'] ?? ($guildUser->user_email ?? ''));
$bio = (string) ($old['profile_bio'] ?? ($profileBio ?? ''));
$portraitId = (int) ($portraitId ?? 0);
$portrait = $portraitId > 0
    ? wp_get_attachment_image($portraitId, 'medium', false, ['class' => 'gmrc-guild-profile__portrait-image', 'alt' => ''])
    : get_avatar($userId, 220, '', '', ['class' => 'gmrc-guild-profile__portrait-image']);
$action = admin_url('admin-post.php');
$passwordUrl = (string) ($passwordUrl ?? wp_lostpassword_url());
$logoutUrl = (string) ($logoutUrl ?? wp_logout_url(home_url('/companion/')));
$isDm = ($accountType ?? '') === \GreatMarketrealmCompanion\Modules\GuildGate\AccountType::DM;
?>
<section class="gmrc-guild-profile" aria-labelledby="gmrc-guild-profile-title">
    <header class="gmrc-guild-profile__hero">
        <div>
            <p class="gmrc-guild-profile__eyebrow">Guild Registry</p>
            <h1 id="gmrc-guild-profile-title">Your Guild Profile</h1>
            <p>Keep the identity shown around the Companion up to date.</p>
        </div>
        <span class="gmrc-guild-profile__role"><?php echo esc_html((string) $accountTypeLabel); ?></span>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-guild-profile__notice gmrc-guild-profile__notice--success" role="status"><?php echo esc_html((string) $flash['success']); ?></div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-guild-profile__notice gmrc-guild-profile__notice--error" role="alert"><?php echo esc_html((string) $flash['error']); ?></div>
    <?php endif; ?>

    <div class="gmrc-guild-profile__grid">
        <aside class="gmrc-guild-profile__card gmrc-guild-profile__portrait-card" aria-labelledby="gmrc-profile-portrait-title">
            <p class="gmrc-guild-profile__kicker">Guild Illuminator</p>
            <h2 id="gmrc-profile-portrait-title">Profile portrait</h2>
            <div class="gmrc-guild-profile__portrait"><?php echo $portrait; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

            <form method="post" action="<?php echo esc_url($action); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="guild-profile/portrait">
                <?php wp_nonce_field('gmrc_guild_profile_portrait', 'gmrc_nonce'); ?>
                <label for="gmrc-profile-portrait">Choose a new portrait</label>
                <input id="gmrc-profile-portrait" type="file" name="gmrc_profile_portrait" accept="image/jpeg,image/png,image/webp" required>
                <small>JPG, PNG or WebP, up to 5 MB.</small>
                <button type="submit">Frame new portrait</button>
            </form>

            <?php if ($portraitId > 0) : ?>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="guild-profile/portrait">
                    <input type="hidden" name="_method" value="DELETE">
                    <?php wp_nonce_field('gmrc_guild_profile_portrait', 'gmrc_nonce'); ?>
                    <button class="gmrc-guild-profile__secondary" type="submit">Restore Guild avatar</button>
                </form>
            <?php endif; ?>
        </aside>

        <main class="gmrc-guild-profile__card" aria-labelledby="gmrc-profile-details-title">
            <p class="gmrc-guild-profile__kicker">Registry details</p>
            <h2 id="gmrc-profile-details-title">Your details</h2>
            <form method="post" action="<?php echo esc_url($action); ?>" class="gmrc-guild-profile__details-form">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="guild-profile">
                <?php wp_nonce_field('gmrc_guild_profile_update', 'gmrc_nonce'); ?>

                <label for="gmrc-profile-display-name">Display name</label>
                <input id="gmrc-profile-display-name" name="display_name" type="text" maxlength="100" autocomplete="name" value="<?php echo esc_attr($displayName); ?>" required>

                <label for="gmrc-profile-email">Email address</label>
                <input id="gmrc-profile-email" name="email" type="email" maxlength="100" autocomplete="email" value="<?php echo esc_attr($email); ?>" required>

                <label for="gmrc-profile-bio">About your Guild self</label>
                <textarea id="gmrc-profile-bio" name="profile_bio" rows="6" maxlength="500" aria-describedby="gmrc-profile-bio-help"><?php echo esc_textarea($bio); ?></textarea>
                <small id="gmrc-profile-bio-help">Optional. Up to 500 characters.</small>

                <div class="gmrc-guild-profile__identity">
                    <span>Guild username</span><strong><?php echo esc_html((string) ($guildUser->user_login ?? '')); ?></strong>
                    <span>Account calling</span><strong><?php echo esc_html((string) $accountTypeLabel); ?></strong>
                </div>
                <p class="gmrc-guild-profile__role-note">Your Player or Dungeon Master calling is protected by the Guild and cannot be changed from this profile form.</p>

                <button type="submit">Save Guild profile</button>
            </form>
        </main>
    </div>

    <section class="gmrc-guild-profile__card gmrc-guild-profile__security" aria-labelledby="gmrc-profile-security-title">
        <div>
            <p class="gmrc-guild-profile__kicker">Account &amp; security</p>
            <h2 id="gmrc-profile-security-title">Guild account</h2>
            <p>Your Companion access is backed by your WordPress account. Password recovery and sign-out continue through WordPress's secure authentication flow.</p>
        </div>

        <dl class="gmrc-guild-profile__account-facts">
            <div><dt>Signed in as</dt><dd><?php echo esc_html((string) ($guildUser->user_login ?? '')); ?></dd></div>
            <div><dt>Recovery email</dt><dd><?php echo esc_html($email); ?></dd></div>
            <div><dt>Guild calling</dt><dd><?php echo esc_html((string) $accountTypeLabel); ?></dd></div>
            <div><dt>Access</dt><dd><?php echo esc_html($isDm ? 'Companion + Dungeon Master tools' : 'Companion player tools'); ?></dd></div>
        </dl>

        <div class="gmrc-guild-profile__security-actions">
            <a class="gmrc-guild-profile__action" href="<?php echo esc_url($passwordUrl); ?>">Manage password</a>
            <a class="gmrc-guild-profile__action gmrc-guild-profile__action--secondary" href="<?php echo esc_url($logoutUrl); ?>">Sign out of the Companion</a>
        </div>
        <p class="gmrc-guild-profile__role-note">Guild calling is capability-protected. Changing profile details never changes Player or Dungeon Master permissions.</p>
    </section>
</section>
