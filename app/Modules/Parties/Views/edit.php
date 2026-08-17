<?php

declare(strict_types=1);

use GreatMarketrealmCompanion\Modules\Parties\Models\Party;

defined('ABSPATH') || exit;

if (! isset($party) || ! $party instanceof Party) {
    return;
}

$id = $party->id()->value();
$companionUrl = home_url('/companion/');
$partyUrl = add_query_arg(
    'gmrc_route',
    'parties/' . rawurlencode($id),
    $companionUrl
);
?>

<section class="gmrc-fellowship-form-page">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">Fellowship Administration</p>
        <h1>
            Amend <?php echo esc_html($party->name()->value()); ?>
        </h1>
        <p>
            The Guild permits the company name to change without disturbing
            its membership or the Characters it references.
        </p>
    </header>

    <form
        class="gmrc-fellowship-form"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input
            type="hidden"
            name="gmrc_route"
            value="<?php echo esc_attr('parties/' . $id); ?>"
        >
        <input type="hidden" name="_method" value="PUT">

        <?php wp_nonce_field(
            'gmrc_party_' . $id,
            'gmrc_nonce'
        ); ?>

        <label class="gmrc-fellowship-field">
            <span>Fellowship name</span>
            <input
                type="text"
                name="name"
                value="<?php echo esc_attr($party->name()->value()); ?>"
                minlength="2"
                maxlength="80"
                required
            >
        </label>

        <div class="gmrc-fellowship-form__actions">
            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--primary"
                type="submit"
            >
                Save Fellowship
            </button>

            <a
                class="gmrc-fellowship-button gmrc-fellowship-button--quiet"
                href="<?php echo esc_url($partyUrl); ?>"
            >
                Cancel
            </a>
        </div>
    </form>

    <section class="gmrc-fellowship-standard-editor">
        <p class="gmrc-eyebrow">The Fellowship Standard</p>
        <h2>Company colours & heraldry</h2>
        <p>
            Choose the palette, emblem and ornament carried by this
            Fellowship throughout the Hall and Register.
        </p>

        <form
            class="gmrc-fellowship-form"
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
            method="post"
        >
            <input type="hidden" name="action" value="gmrc_app_request">
            <input
                type="hidden"
                name="gmrc_route"
                value="<?php echo esc_attr(
                    'parties/' . $id . '/standard'
                ); ?>"
            >
            <input type="hidden" name="_method" value="PUT">

            <?php wp_nonce_field(
                'gmrc_party_' . $id,
                'gmrc_nonce'
            ); ?>

            <div
                class="gmrc-fellowship-standard-preview"
                data-standard-palette="<?php echo esc_attr(
                    $party->standard()->palette()
                ); ?>"
            >
                <span class="gmrc-fellowship-standard-preview__emblem">
                    <?php echo esc_html(
                        $party->standard()->emblemGlyph()
                    ); ?>
                </span>
                <strong><?php echo esc_html($party->name()->value()); ?></strong>
                <span><?php echo esc_html(
                    $party->standard()->ornamentGlyph()
                ); ?></span>
            </div>

            <label class="gmrc-fellowship-field">
                <span>Palette</span>
                <select name="palette">
                    <?php foreach ([
                        'aubergine-gold' => 'Aubergine & Gold',
                        'pantry-green' => 'Pantry Green',
                        'frost-blue' => 'Frost Blue',
                        'berry-red' => 'Berry Red',
                        'cheddar-gold' => 'Cheddar Gold',
                    ] as $value => $label) : ?>
                        <option
                            value="<?php echo esc_attr($value); ?>"
                            <?php selected(
                                $party->standard()->palette(),
                                $value
                            ); ?>
                        >
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gmrc-fellowship-field">
                <span>Emblem</span>
                <select name="emblem">
                    <?php foreach ([
                        'guild-star' => 'Guild Star',
                        'market-leaf' => 'Market Leaf',
                        'company-crown' => 'Company Crown',
                        'adventurers-cross' => 'Adventurers Cross',
                        'guild-cart' => 'Guild Cart',
                    ] as $value => $label) : ?>
                        <option
                            value="<?php echo esc_attr($value); ?>"
                            <?php selected(
                                $party->standard()->emblem(),
                                $value
                            ); ?>
                        >
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="gmrc-fellowship-field">
                <span>Ornament</span>
                <select name="ornament">
                    <?php foreach ([
                        'flourish' => 'Guild Flourish',
                        'laurels' => 'Laurels',
                        'stars' => 'Three Stars',
                        'diamond' => 'Diamond',
                        'plain' => 'Plain',
                    ] as $value => $label) : ?>
                        <option
                            value="<?php echo esc_attr($value); ?>"
                            <?php selected(
                                $party->standard()->ornament(),
                                $value
                            ); ?>
                        >
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--primary"
                type="submit"
            >
                Save Fellowship Standard
            </button>
        </form>
    </section>

    <section class="gmrc-fellowship-danger">
        <p class="gmrc-eyebrow">Registrar’s red ink</p>
        <h2>Disband this Fellowship</h2>
        <p>
            This removes only the Fellowship record. Its adventurers remain
            safely registered as independent Characters.
        </p>

        <form
            action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
            method="post"
        >
            <input type="hidden" name="action" value="gmrc_app_request">
            <input
                type="hidden"
                name="gmrc_route"
                value="<?php echo esc_attr('parties/' . $id); ?>"
            >
            <input type="hidden" name="_method" value="DELETE">

            <?php wp_nonce_field(
                'gmrc_party_' . $id,
                'gmrc_nonce'
            ); ?>

            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--danger"
                type="submit"
            >
                Disband Fellowship
            </button>
        </form>
    </section>
</section>
