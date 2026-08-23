<?php

defined('ABSPATH') || exit;

$entry = is_array($entry ?? null) ? $entry : [];
$guideUrl = add_query_arg('gmrc_route', 'library/field-guide', home_url('/companion/'));
$libraryUrl = add_query_arg('gmrc_route', 'library', home_url('/companion/'));
$imageId = absint($entry['image_attachment_id'] ?? 0);
$imageUrl = $imageId > 0 ? wp_get_attachment_image_url($imageId, 'large') : false;
$name = (string) ($entry['name'] ?? 'Guild creature');
?>
<article class="gmrc-field-guide-folio" aria-labelledby="gmrc-field-guide-folio-title">
    <nav class="gmrc-spellbook__breadcrumb" aria-label="Guild Field Guide breadcrumb">
        <a href="<?php echo esc_url($libraryUrl); ?>">Guild Library</a>
        <span aria-hidden="true">›</span>
        <a href="<?php echo esc_url($guideUrl); ?>">Guild Field Guide</a>
        <span aria-hidden="true">›</span>
        <span><?php echo esc_html($name); ?></span>
    </nav>

    <div class="gmrc-field-guide-folio__paper">
        <figure class="gmrc-field-guide-folio__art"<?php echo $imageUrl ? '' : ' data-empty-art'; ?>>
            <?php if ($imageUrl) : ?>
                <img src="<?php echo esc_url($imageUrl); ?>" alt="Illustration of <?php echo esc_attr($name); ?>">
            <?php else : ?>
                <span aria-hidden="true">✦</span>
                <figcaption>Illustration awaiting the Steward</figcaption>
            <?php endif; ?>
        </figure>

        <div class="gmrc-field-guide-folio__copy">
            <p class="gmrc-eyebrow">Guild-approved field notes</p>
            <h1 id="gmrc-field-guide-folio-title"><?php echo esc_html($name); ?></h1>
            <p class="gmrc-field-guide-folio__kind">
                <?php echo esc_html(trim(implode(' · ', array_filter([
                    (string) ($entry['size'] ?? ''),
                    (string) ($entry['creature_type'] ?? ''),
                ])))); ?>
            </p>
            <div class="gmrc-field-guide-folio__description">
                <?php if ((string) ($entry['description'] ?? '') !== '') : ?>
                    <?php echo wpautop(esc_html((string) $entry['description'])); ?>
                <?php else : ?>
                    <p>The Guild recognises this creature, but no public field notes have yet been sealed for circulation.</p>
                <?php endif; ?>
            </div>
            <aside class="gmrc-field-guide-folio__notice" role="note">
                <strong>Adventurer’s edition</strong>
                <p>This folio contains lore only. Combat records remain sealed in the Dungeon Master’s Bestiary.</p>
            </aside>
        </div>
    </div>
</article>
