<?php

declare(strict_types=1);

defined('ABSPATH') || exit;
?>

<section class="gmrc-dm-forbidden" aria-labelledby="gmrc-dm-forbidden-title">
    <div class="gmrc-dm-forbidden__seal" aria-hidden="true">🔒</div>
    <p class="gmrc-dm-desk__eyebrow">The wax seal remains unbroken</p>
    <h1 id="gmrc-dm-forbidden-title">This desk is reserved for Dungeon Masters.</h1>
    <p>
        Your Guild account can use the Player Companion, but it does not hold the
        Dungeon Master capability required to open these private campaign ledgers.
    </p>
    <a class="gmrc-dm-forbidden__return" href="<?php echo esc_url(home_url('/companion/')); ?>">
        Return to the Companion
    </a>
</section>
