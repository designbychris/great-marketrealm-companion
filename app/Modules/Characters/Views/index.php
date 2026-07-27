<?php

defined('ABSPATH') || exit;

$characters = $characters ?? [];

$companionUrl = home_url('/companion/');

ob_start();
?>

<section class="gmrc-characters">
    <?php
    $ledger = 'The Guild Ledger';
    $volume = 'Volume I';
    $title = "The Adventurer's Register";
    $description =
        'Every hero recorded within these pages has a story waiting '
        . 'to unfold. Open an existing entry or inscribe a new '
        . 'adventurer before their journey begins.';
    $level = 1;
    $ornament = '✦';
    
    require GMRC_PATH
        . 'app/Views/components/furniture/chapter-heading.php';
    ?>

    <?php
	(function (): void {
		$quote =
			'Every hero begins with a name, and every legend begins '
			. 'with a page.';
		$author = 'Auby';
		$image = '';
		$variant = 'default';

		require GMRC_PATH
			. 'app/Views/components/furniture/auby-note.php';
	})();
	?>

        <?php if ($characters === []) : ?>

        <section class="gmrc-empty-state">
            <div
                class="gmrc-empty-state__icon"
                aria-hidden="true"
            >
                ♙
            </div>

            <div>
                <h2>No adventurers have arrived yet</h2>

                <p>
                    Create your first hero and begin their journey through
                    the Great Marketrealm.
                </p>

                <a
                    class="gmrc-button"
                    href="<?php echo esc_url(
                        add_query_arg(
                            'gmrc_route',
                            'characters/create',
                            $companionUrl
                        )
                    ); ?>"
                >
                    Create your first character
                </a>
            </div>
        </section>

    <?php else : ?>

        <div class="adventurer-register">
            <?php foreach ($characters as $character) : ?>
                <?php
                require GMRC_PATH
                    . 'app/Views/components/entries/adventurer-entry.php';
                ?>
            <?php endforeach; ?>

            <a
                class="adventurer-create-entry"
                href="<?php echo esc_url(
                    add_query_arg(
                        'gmrc_route',
                        'characters/create',
                        $companionUrl
                    )
                ); ?>"
            >
                <span
                    class="adventurer-create-entry__icon"
                    aria-hidden="true"
                >
                    ✒
                </span>

                <span class="adventurer-create-entry__content">
                    <strong>Inscribe a New Adventurer</strong>

                    <small>
                        Prepare a fresh page for another hero of the
                        Great Marketrealm.
                    </small>
                </span>
            </a>
        </div>

    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
$side = 'single';
$class = 'guild-page--single';
$spine = true;
require GMRC_PATH
    . 'app/Views/components/furniture/guild-page.php'; 
