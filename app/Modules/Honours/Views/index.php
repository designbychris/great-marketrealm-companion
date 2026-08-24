<?php

defined('ABSPATH') || exit;

$book = is_array($book ?? null) ? $book : [];
$entries = is_array($book['entries'] ?? null) ? $book['entries'] : [];
$earned = (int) ($book['earned'] ?? 0);
$total = (int) ($book['total'] ?? count($entries));
?>

<section class="gmrc-book-of-deeds" aria-labelledby="gmrc-book-of-deeds-title">
    <header class="gmrc-book-of-deeds__header">
        <p class="gmrc-book-of-deeds__eyebrow">The Guild Hall · Volume of Distinction</p>
        <h1 id="gmrc-book-of-deeds-title">Guild Honours &amp; the Book of Deeds</h1>
        <p>Honours are certified from records the Companion can already prove. Once entered here, a deed remains in the Guild archive even if the road later changes.</p>
        <div class="gmrc-book-of-deeds__seal" role="status">
            <strong><?php echo esc_html((string) $earned); ?></strong>
            <span>of <?php echo esc_html((string) $total); ?> honours certified</span>
        </div>
    </header>

    <div class="gmrc-book-of-deeds__grid">
        <?php foreach ($entries as $entry) : ?>
            <?php $earnedEntry = ! empty($entry['earned']); ?>
            <article class="gmrc-deed<?php echo $earnedEntry ? ' gmrc-deed--earned' : ' gmrc-deed--locked'; ?>">
                <span class="gmrc-deed__symbol" aria-hidden="true"><?php echo esc_html((string) ($entry['symbol'] ?? '★')); ?></span>
                <p class="gmrc-deed__state"><?php echo $earnedEntry ? 'Certified honour' : 'Deed yet to be witnessed'; ?></p>
                <h2><?php echo esc_html((string) ($entry['title'] ?? 'Guild Honour')); ?></h2>
                <p><?php echo esc_html((string) ($entry['description'] ?? '')); ?></p>
                <?php if ($earnedEntry && ! empty($entry['certified_at'])) : ?>
                    <p class="gmrc-deed__date">Entered in the Book: <?php echo esc_html(wp_date('j F Y', strtotime((string) $entry['certified_at']))); ?></p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

    <aside class="gmrc-book-of-deeds__note">
        <strong>Auby’s archival note:</strong> These are account-level Guild Honours. Character-specific wax stamps can build on this certified register later without changing the deeds already recorded here.
    </aside>
</section>
