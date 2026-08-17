<?php

declare(strict_types=1);

defined('ABSPATH') || exit;
?>

<section class="gmrc-parties-scaffold">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Register</p>
        <h1>Create Fellowship</h1>
    </header>

    <form
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input type="hidden" name="gmrc_route" value="parties">

        <?php wp_nonce_field(
            'gmrc_create_party',
            'gmrc_nonce'
        ); ?>

        <label>
            Fellowship name
            <input
                type="text"
                name="name"
                minlength="2"
                maxlength="80"
                required
            >
        </label>

        <button type="submit">
            Register Fellowship
        </button>
    </form>
</section>
