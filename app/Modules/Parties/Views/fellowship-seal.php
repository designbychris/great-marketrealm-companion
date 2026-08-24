<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

$characters = is_array($characters ?? null) ? $characters : [];
$flash = is_array($flash ?? null) ? $flash : [];
$action = admin_url('admin-post.php');
$returnUrl = add_query_arg('gmrc_route', 'parties', home_url('/companion/'));
$createCharacterUrl = add_query_arg(
    'gmrc_route',
    'characters/create',
    home_url('/companion/')
);
?>
<section class="gmrc-fellowship-seal" aria-labelledby="gmrc-fellowship-seal-title">
    <header class="gmrc-fellowship-seal__hero">
        <p class="gmrc-eyebrow">Guild Fellowship Invitation</p>
        <div class="gmrc-fellowship-seal__mark" aria-hidden="true">✦</div>
        <h1 id="gmrc-fellowship-seal-title">Redeem a Fellowship Seal</h1>
        <p>
            A Fellowship Seal lets one of your registered adventurers join an
            existing company without sharing usernames or email addresses.
        </p>
    </header>

    <?php if (! empty($flash['success'])) : ?>
        <div class="gmrc-fellowship-seal__notice is-success" role="status">
            <?php echo esc_html((string) $flash['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-fellowship-seal__notice is-error" role="alert">
            <?php echo esc_html((string) $flash['error']); ?>
        </div>
    <?php endif; ?>

    <div class="gmrc-fellowship-seal__paper">
        <?php if ($characters === []) : ?>
            <h2>An adventurer is required</h2>
            <p>
                Fellowship membership belongs to an adventurer. Register a
                Character first, then return here with the Seal.
            </p>
            <a class="gmrc-fellowship-button" href="<?php echo esc_url($createCharacterUrl); ?>">
                Create an Adventurer
            </a>
        <?php else : ?>
            <form method="post" action="<?php echo esc_url($action); ?>">
                <input type="hidden" name="action" value="gmrc_app_request">
                <input type="hidden" name="gmrc_route" value="fellowship-seal">
                <?php wp_nonce_field('gmrc_fellowship_seal_redeem', 'gmrc_nonce'); ?>

                <label for="gmrc-fellowship-seal-code">Fellowship Seal</label>
                <input
                    id="gmrc-fellowship-seal-code"
                    name="fellowship_seal"
                    type="text"
                    maxlength="12"
                    autocomplete="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    placeholder="ABCD-EFGH"
                    required
                >

                <label for="gmrc-fellowship-seal-character">Adventurer joining the company</label>
                <select id="gmrc-fellowship-seal-character" name="character_id" required>
                    <?php foreach ($characters as $character) : ?>
                        <?php if (! $character instanceof Character) { continue; } ?>
                        <option value="<?php echo esc_attr($character->id()->value()); ?>">
                            <?php echo esc_html(
                                sprintf(
                                    '%s — %s %s, Level %d',
                                    $character->name()->value(),
                                    $character->race()->label(),
                                    $character->characterClass()->label(),
                                    $character->level()->value()
                                )
                            ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="gmrc-fellowship-button gmrc-fellowship-button--primary" type="submit">
                    Join Fellowship
                </button>
                <p class="gmrc-fellowship-seal__hint">
                    Codes are case-insensitive. Spaces and the hyphen are ignored.
                </p>
            </form>
        <?php endif; ?>
    </div>

    <a class="gmrc-fellowship-seal__return" href="<?php echo esc_url($returnUrl); ?>">
        ← Return to the Fellowship Register
    </a>
</section>
