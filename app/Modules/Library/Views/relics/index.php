<?php

defined('ABSPATH') || exit;

$filters = $filters ?? [];
$results = $results ?? [];
$rarities = $rarities ?? [];
$groups = $groups ?? [];

$libraryUrl = add_query_arg(
    'gmrc_route',
    'library',
    home_url('/companion/')
);

$relicsUrl = add_query_arg(
    'gmrc_route',
    'library/relics',
    home_url('/companion/')
);
?>

<section
    class="gmrc-relics"
    aria-labelledby="gmrc-relics-title"
>
    <header class="gmrc-relics__hero">
        <div class="gmrc-relics__hero-copy">
            <p class="gmrc-eyebrow">Restricted Archive</p>
            <h1 id="gmrc-relics-title">Relics of the Marketrealm</h1>
            <p>
                Sage and Auby keep the Marketrealm’s enchanted equipment,
                strange wonders and legendary armaments under careful record.
            </p>
            <p>
                These records reproduce the Player’s Handbook mechanics.
                They are reference entries only: special powers are not
                silently automated by the Character Inventory.
            </p>
        </div>
    </header>

    <nav
        class="gmrc-spellbook__breadcrumb"
        aria-label="Guild Library breadcrumb"
    >
        <a href="<?php echo esc_url($libraryUrl); ?>">Guild Library</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Relics of the Marketrealm</span>
    </nav>

    <form
        class="gmrc-relic-filters"
        action="<?php echo esc_url($relicsUrl); ?>"
        method="get"
        role="search"
    >
        <input type="hidden" name="gmrc_route" value="library/relics">

        <label class="gmrc-relic-filters__search">
            <span>Search the restricted archive</span>
            <input
                type="search"
                name="q"
                value="<?php echo esc_attr((string) ($filters['q'] ?? '')); ?>"
                placeholder="Relic name, effect, rarity…"
            >
        </label>

        <label>
            <span>Rarity</span>
            <select name="rarity">
                <option value="">All rarities</option>
                <?php foreach ($rarities as $rarity) : ?>
                    <option
                        value="<?php echo esc_attr((string) $rarity); ?>"
                        <?php selected(($filters['rarity'] ?? ''), $rarity); ?>
                    >
                        <?php echo esc_html((string) $rarity); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <span>Archive shelf</span>
            <select name="group">
                <option value="">All shelves</option>
                <?php foreach ($groups as $key => $label) : ?>
                    <option
                        value="<?php echo esc_attr((string) $key); ?>"
                        <?php selected(($filters['group'] ?? ''), $key); ?>
                    >
                        <?php echo esc_html((string) $label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="gmrc-relic-filters__actions">
            <button type="submit">Search the archive</button>
            <a href="<?php echo esc_url($relicsUrl); ?>">Clear filters</a>
        </div>
    </form>

    <div class="gmrc-relics__summary" aria-live="polite">
        <strong><?php echo esc_html((string) ($result_count ?? 0)); ?></strong>
        of
        <strong><?php echo esc_html((string) ($total_count ?? 0)); ?></strong>
        relic records shown.
    </div>

    <?php if ($results === []) : ?>
        <section class="gmrc-relics__empty">
            <h2>No relic matches those seals.</h2>
            <p>Try a broader search or clear one of the archive filters.</p>
        </section>
    <?php else : ?>
        <div class="gmrc-relics__grid">
            <?php foreach ($results as $relic) : ?>
                <article
                    class="gmrc-relic-card"
                    data-relic-rarity="<?php echo esc_attr(
                        strtolower(
                            str_replace(' ', '-', (string) ($relic['rarity'] ?? ''))
                        )
                    ); ?>"
                >
                    <header class="gmrc-relic-card__heading">
                        <div>
                            <p class="gmrc-eyebrow">
                                <?php echo esc_html(
                                    (string) ($groups[$relic['group'] ?? ''] ?? 'Relic')
                                ); ?>
                            </p>
                            <h2><?php echo esc_html((string) ($relic['name'] ?? '')); ?></h2>
                        </div>
                        <span class="gmrc-relic-card__rarity">
                            <?php echo esc_html((string) ($relic['rarity'] ?? '')); ?>
                        </span>
                    </header>

                    <dl class="gmrc-definition-list">
                        <div>
                            <dt>Type</dt>
                            <dd><?php echo esc_html((string) ($relic['item_type'] ?? '')); ?></dd>
                        </div>

                        <?php if (! empty($relic['base_profile'])) : ?>
                            <div>
                                <dt>Base profile</dt>
                                <dd><?php echo esc_html((string) $relic['base_profile']); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($relic['attunement'])) : ?>
                            <div>
                                <dt>Attunement</dt>
                                <dd><?php echo esc_html((string) $relic['attunement']); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>

                    <section class="gmrc-relic-card__mechanics">
                        <h3>Recorded powers</h3>
                        <ul>
                            <?php foreach (($relic['mechanics'] ?? []) as $mechanic) : ?>
                                <li><?php echo esc_html((string) $mechanic); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                    <?php if (! empty($relic['flavour'])) : ?>
                        <blockquote class="gmrc-relic-card__flavour">
                            <?php echo esc_html((string) $relic['flavour']); ?>
                        </blockquote>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
