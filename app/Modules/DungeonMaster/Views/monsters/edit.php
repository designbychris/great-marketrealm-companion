<?php defined('ABSPATH') || exit; ?>
<section class="gmrc-monster-ledger gmrc-monster-form" aria-labelledby="gmrc-monster-form-title">
    <header class="gmrc-monster-ledger__hero">
        <div><p class="gmrc-dm-desk__eyebrow">Monster Ledger · Revision</p><h1 id="gmrc-monster-form-title">Edit <?php echo esc_html($monster->name()); ?></h1><p>Future Encounter snapshots will use the revised statistics. Existing Encounter snapshots stay unchanged until that Encounter is deliberately edited.</p></div>
    </header>
    <?php require __DIR__ . '/_form.php'; ?>
</section>
