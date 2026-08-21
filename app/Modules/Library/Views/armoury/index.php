<?php

defined('ABSPATH') || exit;

$entries = is_array($entries ?? null)
    ? $entries
    : [];

$groups = is_array($groups ?? null)
    ? $groups
    : [];

$libraryUrl = add_query_arg(
    'gmrc_route',
    'library',
    home_url('/companion/')
);

$groupLabels = [
    'weapon' => 'Weapons',
    'armour' => 'Armour',
    'shield' => 'Shields',
    'gear' => 'Adventuring Gear',
];
?>

<section
    class="gmrc-armoury"
    aria-labelledby="gmrc-armoury-title"
>
    <header class="gmrc-armoury__hero">
        <p class="gmrc-eyebrow">
            Phase III.13.4 · The Guild Library
        </p>
        <h1 id="gmrc-armoury-title">
            The Marketrealm Armoury
        </h1>
        <p>
            The Guild Quartermaster’s mundane shelves: practical weapons,
            armour, shields and travelling gear for Marketrealm adventurers.
        </p>
        <p>
            Magical relics and artefacts are deliberately absent here.
            Those remain sealed for Phase III.13.5.
        </p>
    </header>

    <nav
        class="gmrc-spellbook__breadcrumb"
        aria-label="Guild Library breadcrumb"
    >
        <a href="<?php echo esc_url($libraryUrl); ?>">
            Guild Library
        </a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">The Marketrealm Armoury</span>
    </nav>

    <aside
        class="gmrc-armoury__provenance"
        aria-labelledby="gmrc-armoury-provenance-title"
    >
        <h2 id="gmrc-armoury-provenance-title">
            Quartermaster’s provenance marks
        </h2>
        <p>
            <strong>Handbook-mentioned</strong> means the equipment name
            appears in the Great Marketrealm Player’s Handbook’s class
            equipment or proficiency material.
        </p>
        <p>
            <strong>Standard-compatible</strong> means mundane equipment
            added to broaden the Companion’s practical choices. It is not
            presented as Marketrealm-handbook canon.
        </p>
    </aside>

    <p
        class="gmrc-armoury__count"
        aria-live="polite"
    >
        <?php echo esc_html((string) count($entries)); ?>
        mundane Quartermaster records available.
    </p>

    <?php foreach ($groupLabels as $groupKey => $groupLabel) : ?>
        <?php
        $records = is_array($groups[$groupKey] ?? null)
            ? $groups[$groupKey]
            : [];

        if ($records === []) {
            continue;
        }
        ?>
        <section
            class="gmrc-armoury-group"
            aria-labelledby="gmrc-armoury-<?php echo esc_attr($groupKey); ?>"
        >
            <header class="gmrc-armoury-group__heading">
                <h2 id="gmrc-armoury-<?php echo esc_attr($groupKey); ?>">
                    <?php echo esc_html($groupLabel); ?>
                </h2>
                <span>
                    <?php echo esc_html((string) count($records)); ?>
                    records
                </span>
            </header>

            <div class="gmrc-armoury__grid">
                <?php foreach ($records as $item) : ?>
                    <article
                        class="gmrc-armoury-card"
                        data-armoury-item="<?php echo esc_attr(
                            (string) ($item['id'] ?? '')
                        ); ?>"
                    >
                        <header class="gmrc-armoury-card__heading">
                            <div>
                                <p class="gmrc-eyebrow">
                                    <?php echo esc_html(
                                        ucfirst(
                                            (string) ($item['category'] ?? '')
                                        )
                                    ); ?>
                                </p>
                                <h3>
                                    <?php echo esc_html(
                                        (string) ($item['label'] ?? '')
                                    ); ?>
                                </h3>
                            </div>
                            <span
                                class="gmrc-armoury-card__source"
                                data-armoury-provenance="<?php echo esc_attr(
                                    (string) ($item['provenance'] ?? '')
                                ); ?>"
                            >
                                <?php echo esc_html(
                                    ($item['provenance'] ?? '')
                                        === 'handbook-mentioned'
                                            ? 'Handbook-mentioned'
                                            : 'Standard-compatible'
                                ); ?>
                            </span>
                        </header>

                        <p>
                            <?php echo esc_html(
                                (string) ($item['description'] ?? '')
                            ); ?>
                        </p>

                        <dl class="gmrc-definition-list">
                            <?php if (! empty($item['damage_die'])) : ?>
                                <div>
                                    <dt>Damage</dt>
                                    <dd>
                                        <?php echo esc_html(
                                            (string) $item['damage_die']
                                        ); ?>
                                        <?php echo esc_html(
                                            (string) ($item['damage_type'] ?? '')
                                        ); ?>
                                    </dd>
                                </div>
                            <?php endif; ?>

                            <?php if (! empty($item['range'])) : ?>
                                <div>
                                    <dt>Reach / range</dt>
                                    <dd>
                                        <?php echo esc_html(
                                            (string) $item['range']
                                        ); ?>
                                    </dd>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($item['armour_base'])) : ?>
                                <div>
                                    <dt>Armour base</dt>
                                    <dd>
                                        <?php echo esc_html(
                                            (string) $item['armour_base']
                                        ); ?>
                                    </dd>
                                </div>
                            <?php endif; ?>

                            <?php if (
                                (int) ($item['armour_bonus'] ?? 0) !== 0
                            ) : ?>
                                <div>
                                    <dt>Armour bonus</dt>
                                    <dd>
                                        +<?php echo esc_html(
                                            (string) $item['armour_bonus']
                                        ); ?>
                                    </dd>
                                </div>
                            <?php endif; ?>

                            <div>
                                <dt>Weight</dt>
                                <dd>
                                    <?php echo esc_html(
                                        (string) ($item['weight'] ?? 0)
                                    ); ?>
                                    lb
                                </dd>
                            </div>
                        </dl>

                        <?php if (! empty($item['properties'])) : ?>
                            <ul
                                class="gmrc-armoury-card__properties"
                                aria-label="<?php echo esc_attr(
                                    (string) ($item['label'] ?? '')
                                    . ' properties'
                                ); ?>"
                            >
                                <?php foreach ($item['properties'] as $property) : ?>
                                    <li>
                                        <?php echo esc_html(
                                            ucwords(
                                                str_replace(
                                                    '-',
                                                    ' ',
                                                    (string) $property
                                                )
                                            )
                                        ); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</section>
