<?php

defined('ABSPATH') || exit;

$entries = is_array($entries ?? null) ? $entries : [];
$query = (string) ($query ?? '');
$libraryUrl = add_query_arg('gmrc_route', 'library', home_url('/companion/'));
$guideUrl = add_query_arg('gmrc_route', 'library/field-guide', home_url('/companion/'));
?>
<section class="gmrc-field-guide" aria-labelledby="gmrc-field-guide-title">
    <nav class="gmrc-spellbook__breadcrumb" aria-label="Guild Library breadcrumb">
        <a href="<?php echo esc_url($libraryUrl); ?>">Guild Library</a>
        <span aria-hidden="true">›</span>
        <span>Guild Field Guide</span>
    </nav>

    <header class="gmrc-field-guide__hero">
        <p class="gmrc-eyebrow">An Adventurer’s Creature Folio</p>
        <h1 id="gmrc-field-guide-title">The Guild Field Guide</h1>
        <p>
            Illustrated field notes approved for Guild adventurers. These pages
            record what may safely be known without opening the Dungeon Master’s
            private Bestiary ledger.
        </p>
    </header>

    <form class="gmrc-field-guide__search" method="get" action="<?php echo esc_url(home_url('/companion/')); ?>" role="search">
        <input type="hidden" name="gmrc_route" value="library/field-guide">
        <label for="gmrc-field-guide-search">Search the Field Guide</label>
        <div>
            <input id="gmrc-field-guide-search" name="q" type="search" value="<?php echo esc_attr($query); ?>" placeholder="Creature name or type">
            <button type="submit">Search</button>
            <?php if ($query !== '') : ?><a href="<?php echo esc_url($guideUrl); ?>">Clear</a><?php endif; ?>
        </div>
    </form>

    <p class="gmrc-field-guide__count" aria-live="polite">
        <?php echo esc_html((string) count($entries)); ?> approved <?php echo count($entries) === 1 ? 'entry' : 'entries'; ?>
    </p>

    <?php if ($entries === []) : ?>
        <section class="gmrc-field-guide__empty">
            <h2>No field notes found</h2>
            <p>
                <?php echo $query !== ''
                    ? 'No Steward-approved creature matches that search.'
                    : 'The Steward has not released any creature folios to the Guild yet.'; ?>
            </p>
        </section>
    <?php else : ?>
        <div class="gmrc-field-guide__grid">
            <?php foreach ($entries as $entry) :
                $imageId = absint($entry['image_attachment_id'] ?? 0);
                $imageUrl = $imageId > 0 ? wp_get_attachment_image_url($imageId, 'medium_large') : false;
                $entryUrl = add_query_arg(
                    'gmrc_route',
                    'library/field-guide/' . rawurlencode((string) ($entry['key'] ?? '')),
                    home_url('/companion/')
                );
                ?>
                <article class="gmrc-field-guide-card">
                    <a class="gmrc-field-guide-card__link" href="<?php echo esc_url($entryUrl); ?>">
                        <div class="gmrc-field-guide-card__art"<?php echo $imageUrl ? '' : ' data-empty-art'; ?>>
                            <?php if ($imageUrl) : ?>
                                <img src="<?php echo esc_url($imageUrl); ?>" alt="Illustration of <?php echo esc_attr((string) ($entry['name'] ?? 'creature')); ?>">
                            <?php else : ?>
                                <span aria-hidden="true">✦</span>
                                <small>Illustration awaiting the Steward</small>
                            <?php endif; ?>
                        </div>
                        <div class="gmrc-field-guide-card__copy">
                            <p><?php echo esc_html(trim(implode(' · ', array_filter([
                                (string) ($entry['size'] ?? ''),
                                (string) ($entry['creature_type'] ?? ''),
                            ])))); ?></p>
                            <h2><?php echo esc_html((string) ($entry['name'] ?? '')); ?></h2>
                            <p><?php echo esc_html((string) (($entry['description'] ?? '') ?: 'The Guild has recorded this creature, but its public field notes remain brief.')); ?></p>
                            <span>Open illustrated folio <span aria-hidden="true">→</span></span>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
