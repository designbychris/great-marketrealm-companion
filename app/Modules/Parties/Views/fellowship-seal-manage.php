<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\FellowshipSeal;
use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$seal = $seal instanceof FellowshipSeal ? $seal : null;
$flash = is_array($flash ?? null) ? $flash : [];
$id = $party->id()->value();
$action = admin_url('admin-post.php');
$partyUrl = add_query_arg(
    'gmrc_route',
    'parties/' . rawurlencode($id),
    home_url('/companion/')
);
?>
<section class="gmrc-fellowship-seal" aria-labelledby="gmrc-fellowship-seal-manage-title">
    <header class="gmrc-fellowship-seal__hero">
        <p class="gmrc-eyebrow">Fellowship Invitation Desk</p>
        <div class="gmrc-fellowship-seal__mark" aria-hidden="true">✦</div>
        <h1 id="gmrc-fellowship-seal-manage-title">Fellowship Seal</h1>
        <p>
            Invite another Guild Player to bring one of their own adventurers
            into <strong><?php echo esc_html($party->name()->value()); ?></strong>.
            Custodianship and Company administration remain with you.
        </p>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-fellowship-seal__notice is-success" role="status">
            <?php echo esc_html((string) $flash['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-fellowship-seal__notice is-error" role="alert">
            <?php echo esc_html((string) $flash['error']); ?>
        </div>
    <?php endif; ?>

    <div class="gmrc-fellowship-seal__paper">
        <?php if ($seal instanceof FellowshipSeal && $seal->isRedeemable()) : ?>
            <p class="gmrc-fellowship-seal__status">Current active Seal</p>
            <p class="gmrc-fellowship-seal__code" aria-label="Current Fellowship Seal">
                <?php echo esc_html($seal->code()); ?>
            </p>
            <p>
                Expires <?php echo esc_html(
                    wp_date('j M Y · H:i', $seal->expiresAt())
                ); ?>.
            </p>

            <div class="gmrc-fellowship-seal__controls">
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('parties/' . $id . '/seal'); ?>">
                    <?php wp_nonce_field('gmrc_fellowship_seal_' . $id, 'gmrc_nonce'); ?>
                    <button class="gmrc-fellowship-button" type="submit">Rotate Seal</button>
                </form>

                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('parties/' . $id . '/seal'); ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <?php wp_nonce_field('gmrc_fellowship_seal_' . $id, 'gmrc_nonce'); ?>
                    <button class="gmrc-fellowship-button gmrc-fellowship-button--quiet" type="submit">
                        Revoke Seal
                    </button>
                </form>
            </div>
        <?php else : ?>
            <p class="gmrc-fellowship-seal__status">No active Seal</p>
            <p>
                Issue a short-lived Seal when you are ready to invite another
                Guild Player into this Fellowship.
            </p>
            <form method="post" action="<?php echo esc_url($action); ?>">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('parties/' . $id . '/seal'); ?>">
                <?php wp_nonce_field('gmrc_fellowship_seal_' . $id, 'gmrc_nonce'); ?>
                <button class="gmrc-fellowship-button gmrc-fellowship-button--primary" type="submit">
                    Issue Fellowship Seal
                </button>
            </form>
        <?php endif; ?>
    </div>

    <aside class="gmrc-fellowship-seal__note">
        <strong>Seal safeguards</strong>
        <p>
            Issuing or revoking a Seal never removes existing Fellowship
            members. A redeemed Seal can only add an adventurer owned by the
            Player who redeems it.
        </p>
    </aside>

    <a class="gmrc-fellowship-seal__return" href="<?php echo esc_url($partyUrl); ?>">
        ← Return to <?php echo esc_html($party->name()->value()); ?>
    </a>
</section>
