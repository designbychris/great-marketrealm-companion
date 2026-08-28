<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

$characterId = isset($characterId) && is_scalar($characterId)
    ? (string) $characterId
    : '';

$token = isset($tabletopToken) && is_array($tabletopToken)
    ? $tabletopToken
    : [];

$portraitModel = isset($portrait) && $portrait instanceof PortraitViewModel
    ? $portrait
    : null;

if ($characterId === '' || ! $portraitModel instanceof PortraitViewModel) {
    return;
}

$source = isset($token['source']) ? (string) $token['source'] : 'portrait';
$isCustom = ! empty($token['is_custom']);
$customUrl = isset($token['custom_url']) && is_string($token['custom_url'])
    ? $token['custom_url']
    : null;
$frame = isset($token['frame']) ? (string) $token['frame'] : 'guild-brass';
$focusX = isset($token['focus_x']) ? (int) $token['focus_x'] : 50;
$focusY = isset($token['focus_y']) ? (int) $token['focus_y'] : 50;
$zoom = isset($token['zoom']) ? (int) $token['zoom'] : 100;
$frames = isset($token['frames']) && is_array($token['frames'])
    ? $token['frames']
    : [];

$portraitUrl = $portraitModel->attachmentUrl();
$portraitSvg = $portraitModel->svg();
$usesRasterPortrait = $portraitModel->mode() === 'custom'
    && is_string($portraitUrl)
    && $portraitUrl !== '';

$action = admin_url('admin-post.php');
$route = 'characters/' . rawurlencode($characterId) . '/tabletop-token';
$nonceAction = 'gmrc_character_tabletop_token_' . $characterId;
?>
<section
    class="gmrc-token-forge"
    aria-labelledby="gmrc-token-forge-title-<?php echo esc_attr($characterId); ?>"
    data-token-forge
>
    <header class="gmrc-token-forge__header">
        <p class="gmrc-eyebrow">Adventurer’s Token Forge</p>
        <h3 id="gmrc-token-forge-title-<?php echo esc_attr($characterId); ?>">
            Your piece upon the Table
        </h3>
        <p>
            Shape a dedicated icon for the Great Marketrealm Tabletop without
            changing the portrait in this Ledger. Until you forge one, the
            Tabletop may safely use your Character portrait.
        </p>
    </header>

    <div class="gmrc-token-forge__workspace">
        <figure class="gmrc-token-forge__preview">
            <div
                class="gmrc-token-forge__token gmrc-token-forge__token--<?php echo esc_attr(sanitize_html_class($frame)); ?>"
                data-token-preview
                style="--gmrc-token-focus-x: <?php echo esc_attr((string) $focusX); ?>%; --gmrc-token-focus-y: <?php echo esc_attr((string) $focusY); ?>%; --gmrc-token-zoom: <?php echo esc_attr((string) ($zoom / 100)); ?>;"
            >
                <div class="gmrc-token-forge__art" data-token-art>
                    <?php if ($isCustom && is_string($customUrl) && $customUrl !== '') : ?>
                        <img
                            src="<?php echo esc_url($customUrl); ?>"
                            alt=""
                            data-token-image
                        >
                    <?php elseif ($usesRasterPortrait) : ?>
                        <img
                            src="<?php echo esc_url((string) $portraitUrl); ?>"
                            alt=""
                            data-token-image
                        >
                    <?php else : ?>
                        <div class="gmrc-token-forge__svg" data-token-svg>
                            <?php echo $portraitSvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted renderer output. ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <figcaption>
                <?php echo $source === 'custom'
                    ? 'Dedicated Tabletop token'
                    : 'Following Character portrait'; ?>
            </figcaption>
        </figure>

        <div class="gmrc-token-forge__controls">
            <form
                class="gmrc-token-forge__design"
                method="post"
                action="<?php echo esc_url($action); ?>"
                data-token-design-form
            >
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
                <?php wp_nonce_field($nonceAction, 'gmrc_nonce'); ?>

                <label>
                    <span>Token ring</span>
                    <select name="token_frame" data-token-frame>
                        <?php foreach ($frames as $frameKey => $frameLabel) : ?>
                            <option
                                value="<?php echo esc_attr((string) $frameKey); ?>"
                                <?php selected($frame, (string) $frameKey); ?>
                            ><?php echo esc_html((string) $frameLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Horizontal focus <output data-token-focus-x-output><?php echo esc_html((string) $focusX); ?>%</output></span>
                    <input type="range" min="0" max="100" step="1" name="token_focus_x" value="<?php echo esc_attr((string) $focusX); ?>" data-token-focus-x>
                </label>

                <label>
                    <span>Vertical focus <output data-token-focus-y-output><?php echo esc_html((string) $focusY); ?>%</output></span>
                    <input type="range" min="0" max="100" step="1" name="token_focus_y" value="<?php echo esc_attr((string) $focusY); ?>" data-token-focus-y>
                </label>

                <label>
                    <span>Zoom <output data-token-zoom-output><?php echo esc_html((string) $zoom); ?>%</output></span>
                    <input type="range" min="100" max="220" step="5" name="token_zoom" value="<?php echo esc_attr((string) $zoom); ?>" data-token-zoom>
                </label>

                <button class="gmrc-button gmrc-button--secondary" type="submit">
                    Save token design
                </button>
            </form>

            <form
                class="gmrc-token-forge__upload"
                method="post"
                action="<?php echo esc_url($action); ?>"
                enctype="multipart/form-data"
            >
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
                <input type="hidden" name="token_frame" value="<?php echo esc_attr($frame); ?>">
                <input type="hidden" name="token_focus_x" value="<?php echo esc_attr((string) $focusX); ?>">
                <input type="hidden" name="token_focus_y" value="<?php echo esc_attr((string) $focusY); ?>">
                <input type="hidden" name="token_zoom" value="<?php echo esc_attr((string) $zoom); ?>">
                <?php wp_nonce_field($nonceAction, 'gmrc_nonce'); ?>

                <label class="gmrc-token-forge__upload-label">
                    <strong>Upload a dedicated token</strong>
                    <span>JPG, PNG or WebP · maximum 4 MB</span>
                    <input
                        type="file"
                        name="gmrc_tabletop_token"
                        accept="image/jpeg,image/png,image/webp"
                        required
                    >
                </label>

                <button class="gmrc-button gmrc-button--secondary" type="submit">
                    Forge uploaded token
                </button>
            </form>

            <?php if ($isCustom) : ?>
                <form method="post" action="<?php echo esc_url($action); ?>">
                    <input type="hidden" name="action" value="gmrc_app_request">
                    <input type="hidden" name="gmrc_route" value="<?php echo esc_attr($route); ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <?php wp_nonce_field($nonceAction, 'gmrc_nonce'); ?>
                    <button class="gmrc-button gmrc-button--ghost" type="submit">
                        Use Character portrait instead
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <p class="gmrc-token-forge__note">
        The Token Forge stores a non-destructive crop recipe. Your original
        portrait or uploaded token image is never cropped or overwritten.
    </p>
</section>
