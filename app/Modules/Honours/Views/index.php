<?php

defined('ABSPATH') || exit;

$book = is_array($book ?? null) ? $book : [];
$entries = is_array($book['entries'] ?? null) ? $book['entries'] : [];
$earned = (int) ($book['earned'] ?? 0);
$total = (int) ($book['total'] ?? count($entries));
$completion = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
$companionUrl = home_url('/companion/');
$guildHallUrl = add_query_arg('gmrc_route', 'dashboard', $companionUrl);

$icons = [
    'first-character' => '✒',
    'character-shelf' => '▤',
    'campaign-table' => '⚔',
    'fellowship-forged' => '🤝',
    'archived-tale' => '▧',
    'campaign-keeper' => '♛',
];
?>

<section class="gmrc-book-of-deeds" aria-labelledby="gmrc-book-of-deeds-title">
    <nav class="gmrc-book-of-deeds__trail" aria-label="Book of Deeds breadcrumb">
        <span aria-hidden="true">♜</span>
        <a href="<?php echo esc_url($guildHallUrl); ?>">Guild Hall</a>
        <span aria-hidden="true">›</span>
        <span>Guild Honours</span>
        <span aria-hidden="true">›</span>
        <strong>Book of Deeds</strong>
        <a class="gmrc-book-of-deeds__back" href="<?php echo esc_url($guildHallUrl); ?>">← Back to Guild Hall</a>
    </nav>

    <div class="gmrc-book-of-deeds__intro">
        <header class="gmrc-book-of-deeds__header">
            <p class="gmrc-book-of-deeds__eyebrow">The Guild Hall · Volume of Distinction</p>
            <h1 id="gmrc-book-of-deeds-title">Book of Deeds</h1>
            <p>Honours earned across campaigns, fellowships and the realms. Certified deeds are written here for all time.</p>
            <span class="gmrc-book-of-deeds__flourish" aria-hidden="true">◇◆◇</span>
        </header>

        <aside class="gmrc-book-of-deeds__summary" aria-label="Book of Deeds summary">
            <div class="gmrc-book-of-deeds__summary-item">
                <span class="gmrc-book-of-deeds__summary-icon" aria-hidden="true">❧</span>
                <span>Honours Earned</span>
                <strong><?php echo esc_html((string) $earned); ?></strong>
                <small>of <?php echo esc_html((string) $total); ?></small>
            </div>
            <div class="gmrc-book-of-deeds__summary-item">
                <span class="gmrc-book-of-deeds__summary-icon" aria-hidden="true">★</span>
                <span>Completion</span>
                <strong><?php echo esc_html((string) $completion); ?>%</strong>
                <small>certified</small>
            </div>
            <div class="gmrc-book-of-deeds__summary-item">
                <span class="gmrc-book-of-deeds__summary-icon" aria-hidden="true">▤</span>
                <span>Book of Deeds</span>
                <strong class="gmrc-book-of-deeds__edition">1st Edition</strong>
                <small>Guild archive</small>
            </div>
        </aside>
    </div>

    <div class="gmrc-book-of-deeds__grid">
        <?php foreach ($entries as $entry) : ?>
            <?php
            $earnedEntry = ! empty($entry['earned']);
            $key = sanitize_key((string) ($entry['key'] ?? ''));
            $symbol = (string) ($entry['symbol'] ?? ($icons[$key] ?? '★'));
            ?>
            <article class="gmrc-deed<?php echo $earnedEntry ? ' gmrc-deed--earned' : ' gmrc-deed--locked'; ?>">
                <div class="gmrc-deed__medallion" aria-hidden="true">
                    <span class="gmrc-deed__symbol"><?php echo esc_html($symbol); ?></span>
                </div>
                <div class="gmrc-deed__body">
                    <h2><?php echo esc_html((string) ($entry['title'] ?? 'Guild Honour')); ?></h2>
                    <p><?php echo esc_html((string) ($entry['description'] ?? '')); ?></p>
                    <div class="gmrc-deed__rule" aria-hidden="true"></div>
                    <p class="gmrc-deed__state">
                        <span aria-hidden="true">●</span>
                        <?php echo $earnedEntry ? 'Certified honour' : 'Deed yet to be witnessed'; ?>
                    </p>
                    <?php if ($earnedEntry && ! empty($entry['certified_at'])) : ?>
                        <p class="gmrc-deed__date">Entered in the Book: <?php echo esc_html(wp_date('j F Y', strtotime((string) $entry['certified_at']))); ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <aside class="gmrc-book-of-deeds__note">
        <span class="gmrc-book-of-deeds__note-icon" aria-hidden="true">▤</span>
        <div>
            <strong>Honours are permanent</strong>
            <p>Once a deed is earned, it is written in the Book of Deeds for all time. Leaving a fellowship or campaign will not erase your honoured history.</p>
            <small>These are account-level Guild Honours. Auby’s archival note: Character-specific wax stamps can build on this certified register later without changing the deeds already recorded here.</small>
        </div>
    </aside>
</section>
