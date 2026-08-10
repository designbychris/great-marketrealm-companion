<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$characterId = isset($characterId)
    ? (string) $characterId
    : '';

$isCustom = isset($isCustom)
    ? (bool) $isCustom
    : false;

$portraitAction = admin_url(
    'admin-post.php'
);

$portraitRoute = 'characters/'
    . rawurlencode($characterId)
    . '/portrait';
?>
<section
    class="gmrc-illuminator-workbench"
    aria-labelledby="gmrc-illuminator-workbench-title"
>
    <div class="gmrc-illuminator-workbench__pin" aria-hidden="true"></div>

    <header class="gmrc-illuminator-workbench__header">
        <p class="gmrc-eyebrow">Illuminator’s Toolkit</p>
        <h3 id="gmrc-illuminator-workbench-title">
            Bring your own portrait
        </h3>
        <p>
            Prefer your own artwork? Ask the Guild Illuminator to frame
            a JPG, PNG or WebP image instead. Your generated portrait is
            kept safely underneath so it can be restored later.
        </p>
    </header>

    <form
        class="gmrc-illuminator-workbench__upload"
        method="post"
        action="<?php echo esc_url($portraitAction); ?>"
        enctype="multipart/form-data"
    >
        <input
            type="hidden"
            name="action"
            value="gmrc_app_request"
        >
        <input
            type="hidden"
            name="gmrc_route"
            value="<?php echo esc_attr($portraitRoute); ?>"
        >

        <?php
        wp_nonce_field(
            'gmrc_character_portrait_' . $characterId,
            'gmrc_nonce'
        );
        ?>

        <label
            class="gmrc-portrait-dropzone"
            for="gmrc-custom-portrait-<?php echo esc_attr($characterId); ?>"
        >
            <span class="gmrc-portrait-dropzone__icon" aria-hidden="true">
                ✦
            </span>
            <span>
                <strong>Choose a portrait</strong>
                <small>JPG, PNG or WebP · maximum 8 MB</small>
            </span>
        </label>

        <input
            id="gmrc-custom-portrait-<?php echo esc_attr($characterId); ?>"
            class="gmrc-portrait-file-input"
            type="file"
            name="gmrc_custom_portrait"
            accept="image/jpeg,image/png,image/webp"
            required
            data-gmrc-portrait-file
        >

        <p
            class="gmrc-portrait-file-name"
            data-gmrc-portrait-file-name
            aria-live="polite"
        >
            No image selected.
        </p>

        <button
            class="gmrc-button gmrc-button--secondary"
            type="submit"
        >
            Frame this portrait
        </button>
    </form>

    <?php if ($isCustom) : ?>
        <form
            class="gmrc-illuminator-workbench__restore"
            method="post"
            action="<?php echo esc_url($portraitAction); ?>"
        >
            <input
                type="hidden"
                name="action"
                value="gmrc_app_request"
            >
            <input
                type="hidden"
                name="gmrc_route"
                value="<?php echo esc_attr($portraitRoute); ?>"
            >
            <input type="hidden" name="_method" value="DELETE">

            <?php
            wp_nonce_field(
                'gmrc_character_portrait_' . $characterId,
                'gmrc_nonce'
            );
            ?>

            <button
                class="gmrc-button gmrc-button--ghost"
                type="submit"
            >
                Restore Guild portrait
            </button>
        </form>
    <?php endif; ?>
</section>
