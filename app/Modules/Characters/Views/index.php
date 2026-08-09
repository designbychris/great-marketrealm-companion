<?php

defined('ABSPATH') || exit;

$characters = $characters ?? [];

$portraits = is_array($portraits ?? null)
    ? $portraits
    : [];

$companionUrl = home_url('/companion/');

$flash = is_array($flash ?? null)
    ? $flash
    : [];

/*
 * Capture the Character Register content.
 */
ob_start();
?>

<section class="gmrc-characters">
    <?php if (! empty($flash['success'])) : ?>
        <div
            class="gmrc-register-notice gmrc-register-notice--success"
            role="status"
        >
            <span
                class="gmrc-register-notice__seal"
                aria-hidden="true"
            >✦</span>

            <p>
                <?php echo esc_html(
                    $flash['success']
                ); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (! empty($flash['error'])) : ?>
        <div
            class="gmrc-register-notice gmrc-register-notice--error"
            role="alert"
        >
            <span
                class="gmrc-register-notice__seal"
                aria-hidden="true"
            >!</span>

            <p>
                <?php echo esc_html(
                    $flash['error']
                ); ?>
            </p>
        </div>
    <?php endif; ?>

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
	echo $this->component(
	    'components.furniture.auby-note',
	    [
	        'quote' => $aubyQuote,
	    ]
	);
	?>

        <?php if ($characters === []) : ?>
		
        <?php
        echo $this->component(
            'components.entries.register-adventurer-prompt',
            [
                'companionUrl' => $companionUrl,
                'empty' => true,
            ]
        );
        ?>

    <?php else : ?>

        <div class="adventurer-register">
		    <?php foreach ($characters as $character) : ?>
		        <?php
		        $characterId = $character
		            ->id()
		            ->value();
		
		        $characterPortrait =
		            $portraits[$characterId]
		                ?? null;
		
		        echo $this->component(
		            'components.entries.adventurer-entry',
		            [
		                'character' => $character,
		                'portrait' => $characterPortrait,
		                'sealRegistry' => $sealRegistry,
		                'companionUrl' => $companionUrl,
		            ]
		        );
		        ?>
		    <?php endforeach; ?>
		
            <?php
            echo $this->component(
                'components.entries.register-adventurer-prompt',
                [
                    'companionUrl' => $companionUrl,
                    'empty' => false,
                ]
            );
            ?>
		</div>

    <?php endif; ?>
</section>
<?php

$registerContent = ob_get_clean();

/*
 * Wrap the Character Register in a Ledger Section.
 */
$characterContent = $this->component(
    'components.furniture.ledger-section',
    [
        'eyebrow'     => 'Guild Register',
        'title'       => 'Your Adventurers',
        'description' => 'Every recorded hero has a page waiting to be opened.',
        'ornament'    => '✦',
        'content'     => $registerContent,
    ]
);

/*
 * Capture the rendered Guild Page.
 */
ob_start();

$content = $characterContent;
$side    = 'single';
$class   = 'gmrc-character-register';
$spine   = true;

require GMRC_PATH
    . 'app/Views/components/furniture/guild-page.php';

$ledgerContent = ob_get_clean();

/*
 * Render the outer Guild Ledger.
 */
$content = $ledgerContent;
$layout  = 'single';
$class   = 'gmrc-character-ledger';

require GMRC_PATH
    . 'app/Views/components/furniture/guild-ledger.php';
