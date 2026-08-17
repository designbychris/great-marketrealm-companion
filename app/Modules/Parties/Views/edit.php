<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$id = $party->id()->value();
?>

<section class="gmrc-parties-scaffold">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Register</p>
        <h1>Edit <?php echo esc_html($party->name()->value()); ?></h1>
    </header>

    <form
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('parties/' . $id); ?>">
        <input type="hidden" name="_method" value="PUT">

        <?php wp_nonce_field(
            'gmrc_party_' . $id,
            'gmrc_nonce'
        ); ?>

        <label>
            Fellowship name
            <input
                type="text"
                name="name"
                value="<?php echo esc_attr($party->name()->value()); ?>"
                minlength="2"
                maxlength="80"
                required
            >
        </label>

        <button type="submit">
            Save Fellowship
        </button>
    </form>

    <form
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input type="hidden" name="gmrc_route" value="<?php echo esc_attr('parties/' . $id); ?>">
        <input type="hidden" name="_method" value="DELETE">

        <?php wp_nonce_field(
            'gmrc_party_' . $id,
            'gmrc_nonce'
        ); ?>

        <button type="submit">
            Delete Fellowship
        </button>
    </form>
</section>
