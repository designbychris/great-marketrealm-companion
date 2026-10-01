<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$flash = is_array($flash ?? null) ? $flash : [];
$isSignedIn = (bool) ($isSignedIn ?? false);
$membershipSummary = is_array($membershipSummary ?? null) ? $membershipSummary : [];
$requestedAt = (string) ($requestedAt ?? '');
$gateUrl = (string) ($gateUrl ?? home_url('/companion/'));
$profileUrl = (string) ($profileUrl ?? add_query_arg('gmrc_route', 'guild-profile', home_url('/companion/')));
$action = admin_url('admin-post.php');
?>
<section class="gmrc-account-deletion" aria-labelledby="gmrc-account-deletion-title">
    <header class="gmrc-account-deletion__hero">
        <p class="gmrc-account-deletion__eyebrow">The Registrar's Desk</p>
        <h1 id="gmrc-account-deletion-title">Delete your Great MarketRealm account and data</h1>
        <p>The Great MarketRealm Pocket Companion uses the same Guild account as the Great MarketRealm Companion. You can request deletion of that account and its associated personal Companion data here.</p>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-account-deletion__notice gmrc-account-deletion__notice--success" role="status"><?php echo esc_html((string) $flash['success']); ?></div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-account-deletion__notice gmrc-account-deletion__notice--error" role="alert"><?php echo esc_html((string) $flash['error']); ?></div>
    <?php endif; ?>

    <div class="gmrc-account-deletion__grid">
        <section class="gmrc-account-deletion__card">
            <p class="gmrc-account-deletion__kicker">What the request covers</p>
            <h2>Your account and personal Companion records</h2>
            <p>When the Registrar completes your request, your Guild account and personal data owned by that account are removed. This includes your Guild profile and owned Character records, together with associated Companion data that belongs only to you.</p>
            <p>Access credentials issued to the Pocket Companion are revoked as part of account closure.</p>
        </section>

        <section class="gmrc-account-deletion__card">
            <p class="gmrc-account-deletion__kicker">Shared records</p>
            <h2>Campaigns and Fellowships</h2>
            <p>Some Campaign or Fellowship records can involve other Guild members. Where removing an entire shared record would remove another member's history, your personal association is detached or anonymised instead of destroying the shared record.</p>
            <p>Information that must be retained for legal, security or fraud-prevention obligations may be retained only for as long as that obligation requires. The Companion does not use retained deletion records for advertising.</p>
        </section>
    </div>

    <section class="gmrc-account-deletion__card gmrc-account-deletion__steps">
        <p class="gmrc-account-deletion__kicker">How to request deletion</p>
        <h2>Seal a request with the Registrar</h2>
        <ol>
            <li>Sign in to your Great MarketRealm Guild account.</li>
            <li>Review the account relationships shown below.</li>
            <li>Confirm your current password and type <strong>DELETE MY ACCOUNT</strong>.</li>
            <li>The Registrar receives the verified request and completes deletion while safely handling shared records.</li>
        </ol>
    </section>

    <?php if (! $isSignedIn) : ?>
        <section class="gmrc-account-deletion__card gmrc-account-deletion__action-card">
            <h2>Ready to make a request?</h2>
            <p>Sign in with the Guild account you want deleted. You will return to this page after authentication.</p>
            <a class="gmrc-account-deletion__button" href="<?php echo esc_url($gateUrl); ?>">Sign in to request account deletion</a>
        </section>
    <?php elseif ($requestedAt !== '') : ?>
        <section class="gmrc-account-deletion__card gmrc-account-deletion__pending" role="status">
            <p class="gmrc-account-deletion__kicker">Request recorded</p>
            <h2>The Registrar has your request</h2>
            <p>Your verified deletion request was recorded on <strong><?php echo esc_html($requestedAt); ?></strong>. You do not need to submit another request.</p>
            <p>If you submitted this in error, contact Great MarketRealm support as soon as possible.</p>
        </section>
    <?php else : ?>
        <section class="gmrc-account-deletion__card gmrc-account-deletion__inventory">
            <p class="gmrc-account-deletion__kicker">Before you continue</p>
            <h2>Your current Guild relationships</h2>
            <dl>
                <div><dt>Owned adventurers</dt><dd><?php echo esc_html((string) ($membershipSummary['characters'] ?? 0)); ?></dd></div>
                <div><dt>Active Campaign relationships</dt><dd><?php echo esc_html((string) ($membershipSummary['active_campaigns'] ?? 0)); ?></dd></div>
                <div><dt>Archived Campaign records</dt><dd><?php echo esc_html((string) ($membershipSummary['archived_campaigns'] ?? 0)); ?></dd></div>
                <div><dt>Owned Fellowships</dt><dd><?php echo esc_html((string) ($membershipSummary['owned_fellowships'] ?? 0)); ?></dd></div>
                <div><dt>Shared Fellowships</dt><dd><?php echo esc_html((string) ($membershipSummary['shared_fellowships'] ?? 0)); ?></dd></div>
            </dl>
        </section>

        <section class="gmrc-account-deletion__card gmrc-account-deletion__danger">
            <p class="gmrc-account-deletion__kicker">Irreversible request</p>
            <h2>Request account and data deletion</h2>
            <p>Submitting this form starts the deletion process. Do not continue if you only want to sign out or change your profile.</p>
            <form method="post" action="<?php echo esc_url($action); ?>" autocomplete="off">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="delete-account">
                <?php wp_nonce_field('gmrc_account_deletion_request', 'gmrc_nonce'); ?>

                <label for="gmrc-delete-current-password">Current password</label>
                <input id="gmrc-delete-current-password" name="current_password" type="password" autocomplete="current-password" required>

                <label for="gmrc-delete-confirmation">Type <strong>DELETE MY ACCOUNT</strong> to confirm</label>
                <input id="gmrc-delete-confirmation" name="confirmation_phrase" type="text" autocomplete="off" required>

                <button type="submit" class="gmrc-account-deletion__delete-button">Request deletion of my account and data</button>
            </form>
            <a class="gmrc-account-deletion__back" href="<?php echo esc_url($profileUrl); ?>">Return to Guild Profile</a>
        </section>
    <?php endif; ?>

    <footer class="gmrc-account-deletion__footer">
        <p>Need help accessing the account you want deleted? Contact Great MarketRealm support at <a href="<?php echo esc_url(home_url('/support/')); ?>">the support desk</a>.</p>
    </footer>
</section>
