<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$flash = is_array($flash ?? null) ? $flash : [];
$action = admin_url('admin-post.php');
$dashboardUrl = add_query_arg('gmrc_route', 'dashboard', home_url('/companion/'));
?>
<section class="gmrc-market-pass" aria-labelledby="gmrc-market-pass-title">
    <header class="gmrc-market-pass__hero">
        <p class="gmrc-market-pass__eyebrow">Guild Campaign Invitation</p>
        <h1 id="gmrc-market-pass-title">Redeem a Market Pass</h1>
        <p>Enter the code your Dungeon Master shared with you. A Market Pass joins your Guild account to the Campaign; choosing the adventurer you will play comes later.</p>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-market-pass__notice is-success" role="status"><?php echo esc_html((string) $flash['success']); ?></div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-market-pass__notice is-error" role="alert"><?php echo esc_html((string) $flash['error']); ?></div>
    <?php endif; ?>

    <div class="gmrc-market-pass__paper">
        <form method="post" action="<?php echo esc_url($action); ?>">
            <input type="hidden" name="action" value="gmrc_app_request">
            <input type="hidden" name="gmrc_route" value="market-pass">
            <?php wp_nonce_field('gmrc_market_pass_redeem', 'gmrc_nonce'); ?>
            <label for="gmrc-market-pass-code">Market Pass code</label>
            <div class="gmrc-market-pass__entry">
                <input id="gmrc-market-pass-code" name="market_pass" type="text" maxlength="12" inputmode="text" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="ABCD-EFGH" required>
                <button type="submit">Join Campaign</button>
            </div>
            <p class="gmrc-market-pass__hint">Codes are not case-sensitive. Spaces and the hyphen are ignored.</p>
        </form>
    </div>

    <a class="gmrc-market-pass__return" href="<?php echo esc_url($dashboardUrl); ?>">← Return to the Guild Hall</a>
</section>
