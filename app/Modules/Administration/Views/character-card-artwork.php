<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$kindLabels = [
    'race' => 'Folk & Races',
    'class' => 'Classes & Callings',
    'background' => 'Backgrounds',
];
$baseUrl = add_query_arg(
    ['page' => 'gmrc-stewards-office', 'section' => 'character-card-artwork'],
    admin_url('admin.php')
);
?>
<div class="wrap gmrc-admin gmrc-stewards-office gmrc-canonical-steward gmrc-character-card-artwork-admin">
    <header class="gmrc-stewards-office__hero">
        <div>
            <p class="gmrc-stewards-office__eyebrow">Character Creation · Presentation</p>
            <h1>Character Card Artwork</h1>
            <p>Assign inviting illustrations to the Race, Class and Background cards shown at the Registrar’s Desk. Cards without artwork keep their familiar monogram fallback.</p>
        </div>
        <a class="button" href="<?php echo esc_url(add_query_arg(['page' => 'gmrc-stewards-office'], admin_url('admin.php'))); ?>">← Steward’s Office</a>
    </header>

    <?php if (! empty($_GET['gmrc_card_artwork_saved'])) : ?>
        <div class="notice notice-success is-dismissible"><p>Character card artwork saved.</p></div>
    <?php endif; ?>
    <?php if (! empty($_GET['gmrc_card_artwork_error'])) : ?>
        <div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_card_artwork_error'])); ?></p></div>
    <?php endif; ?>

    <div class="gmrc-canonical-steward__layout gmrc-card-artwork-admin__layout">
        <aside class="gmrc-canonical-steward__register" aria-label="Character Creation card register">
            <?php foreach ($artworkRecords as $kind => $records) : ?>
                <h2><?php echo esc_html($kindLabels[$kind] ?? ucfirst($kind)); ?></h2>
                <div class="gmrc-canonical-steward__list gmrc-card-artwork-admin__list">
                    <?php foreach ($records as $key => $label) :
                        $id = $artworkRegister->attachmentId($kind, (string) $key);
                        $thumb = $id > 0 ? wp_get_attachment_image_url($id, 'thumbnail') : false;
                        $url = add_query_arg(['kind' => $kind, 'record' => $key], $baseUrl);
                    ?>
                        <a href="<?php echo esc_url($url); ?>"<?php echo $selectedArtworkKind === $kind && $selectedArtworkKey === $key ? ' aria-current="page"' : ''; ?>>
                            <span class="gmrc-card-artwork-admin__thumb" aria-hidden="true">
                                <?php if ($thumb) : ?><img src="<?php echo esc_url($thumb); ?>" alt=""><?php else : ?><?php echo esc_html(strtoupper(substr((string) $label, 0, 1))); ?><?php endif; ?>
                            </span>
                            <span><strong><?php echo esc_html((string) $label); ?></strong><small><?php echo $id > 0 ? 'Illustrated' : 'Monogram fallback'; ?></small></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </aside>

        <main class="gmrc-canonical-steward__editor">
            <?php if ($selectedArtworkKey === '') : ?>
                <section class="gmrc-stewards-office__card"><h2>No card records found</h2><p>Publish a Character Creation record before assigning artwork.</p></section>
            <?php else : ?>
                <form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="gmrc_save_character_card_artwork">
                    <input type="hidden" name="artwork_kind" value="<?php echo esc_attr($selectedArtworkKind); ?>">
                    <input type="hidden" name="record_key" value="<?php echo esc_attr($selectedArtworkKey); ?>">
                    <?php wp_nonce_field('gmrc_save_character_card_artwork_' . $selectedArtworkKind . '_' . $selectedArtworkKey, 'gmrc_character_card_artwork_nonce'); ?>

                    <header>
                        <div>
                            <p class="gmrc-stewards-office__eyebrow"><?php echo esc_html($kindLabels[$selectedArtworkKind] ?? ucfirst($selectedArtworkKind)); ?></p>
                            <h2><?php echo esc_html($selectedArtworkLabel); ?></h2>
                            <p><code><?php echo esc_html($selectedArtworkKey); ?></code></p>
                        </div>
                        <span class="gmrc-stewards-office__status"><?php echo $selectedArtworkId > 0 ? 'Illustrated' : 'Using monogram'; ?></span>
                    </header>

                    <section class="gmrc-canonical-steward__artwork gmrc-card-artwork-admin__editor-artwork">
                        <div class="gmrc-canonical-steward__paper-frame gmrc-card-artwork-admin__preview-frame">
                            <img src="<?php echo esc_url($selectedArtworkUrl ?: ''); ?>" alt="" data-gmrc-card-artwork-preview<?php echo $selectedArtworkUrl ? '' : ' hidden'; ?>>
                            <span data-gmrc-card-artwork-empty<?php echo $selectedArtworkUrl ? ' hidden' : ''; ?>><?php echo esc_html(strtoupper(substr($selectedArtworkLabel, 0, 1))); ?></span>
                        </div>
                        <div>
                            <h3>Character Creation Illustration</h3>
                            <p>Choose an image from the WordPress Media Library. Landscape or square artwork works best; the card uses a centre crop and gently zooms on hover or selection.</p>
                            <input type="hidden" name="image_attachment_id" value="<?php echo esc_attr((string) $selectedArtworkId); ?>" data-gmrc-card-artwork-id>
                            <button class="button button-primary" type="button" data-gmrc-card-artwork-select>Choose / Replace Image</button>
                            <button class="button-link-delete" type="button" data-gmrc-card-artwork-remove<?php echo $selectedArtworkId > 0 ? '' : ' hidden'; ?>>Remove Image</button>
                        </div>
                    </section>

                    <aside class="gmrc-canonical-steward__source"><strong>Presentation only:</strong> this artwork never changes Race, Class or Background mechanics. Removing an illustration restores the monogram card automatically.</aside>
                    <div class="gmrc-canonical-steward__actions"><button class="button button-primary button-large" type="submit">Save Card Artwork</button></div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</div>
