<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$id = $party->id()->value();
$companionUrl = home_url('/companion/');
$partyUrl = add_query_arg(
    'gmrc_route',
    'parties/' . rawurlencode($id),
    $companionUrl
);
?>

<section class="gmrc-fellowship-form-page">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Administration</p>
        <h1>
            Amend <?php echo esc_html($party->name()->value()); ?>
        </h1>
        <p>
            The Guild permits the company name to change without disturbing
            its membership or the Characters it references.
        </p>
    </header>

    <form
        class="gmrc-fellowship-form"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input
            type="hidden"
            name="gmrc_route"
            value="<?php echo esc_attr('parties/' . $id); ?>"
        >
        <input type="hidden" name="_method" value="PUT">

        <?php wp_nonce_field(
            'gmrc_party_' . $id,
            'gmrc_nonce'
        ); ?>

        <label class="gmrc-fellowship-field">
            <span>Fellowship name</span>
            <input
                type="text"
                name="name"
                value="<?php echo esc_attr($party->name()->value()); ?>"
                minlength="2"
                maxlength="80"
                required
            >
        </label>

        <div class="gmrc-fellowship-form__actions">
            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--primary"
                type="submit"
            >
                Save Fellowship
            </button>

            <a
                class="gmrc-fellowship-button gmrc-fellowship-button--quiet"
                href="<?php echo esc_url($partyUrl); ?>"
            >
                Cancel
            </a>
        </div>
    </form>

    <section class="gmrc-fellowship-danger">
        <p class="gmrc-eyebrow">Registrar’s red ink</p>
        <h2>Disband this Fellowship</h2>
        <p>
            This removes only the Fellowship record. Its adventurers remain
            safely registered as independent Characters.
        </p>

        <form
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
            method="post"
        >
            <input type="hidden" name="action" value="gmrc_app_request">
            <input
                type="hidden"
                name="gmrc_route"
                value="<?php echo esc_attr('parties/' . $id); ?>"
            >
            <input type="hidden" name="_method" value="DELETE">

            <?php wp_nonce_field(
                'gmrc_party_' . $id,
                'gmrc_nonce'
            ); ?>

            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--danger"
                type="submit"
            >
                Disband Fellowship
            </button>
        </form>
    </section>
</section>
