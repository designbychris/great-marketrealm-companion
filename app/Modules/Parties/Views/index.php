<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

$parties = is_array($parties ?? null)
    ? $parties
    : [];

$companionUrl = home_url('/companion/');
$createUrl = add_query_arg(
    'gmrc_route',
    'parties/create',
    $companionUrl
);
?>

<section class="gmrc-parties-scaffold">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Register</p>
        <h1>Your Fellowships</h1>
        <p>
            This is the functional Party scaffold. The illuminated Fellowship
            Register arrives in Phase III.11.1E.
        </p>
    </header>

    <?php if ($parties === []) : ?>
        <p>No Fellowships have been registered yet.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($parties as $party) : ?>
                <?php if (! $party instanceof Party) { continue; } ?>
                <li>
                    <a href="<?php echo esc_url(
                        add_query_arg(
                            'gmrc_route',
                            'parties/' . rawurlencode(
                                $party->id()->value()
                            ),
                            $companionUrl
                        )
                    ); ?>">
                        <?php echo esc_html(
                            $party->name()->value()
                        ); ?>
                    </a>
                    <span>
                        <?php echo esc_html(
                            (string) $party->memberCount()
                        ); ?>
                        members
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>
        <a href="<?php echo esc_url($createUrl); ?>">
            Create Fellowship
        </a>
    </p>
</section>
