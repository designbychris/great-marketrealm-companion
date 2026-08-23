<?php

defined('ABSPATH') || exit;

$backgrounds = is_array($backgrounds ?? null)
    ? $backgrounds
    : [];

$libraryUrl = add_query_arg(
    'gmrc_route',
    'library',
    home_url('/companion/')
);
?>

<section
    class="gmrc-background-register"
    aria-labelledby="gmrc-background-register-title"
>
    <header class="gmrc-background-register__hero">
        <p class="gmrc-eyebrow">
            The Guild Library
        </p>
        <h1 id="gmrc-background-register-title">
            The Background Register
        </h1>
        <p>
            Five optional Marketrealm histories recorded exactly from
            The Great Marketrealm - Players Handbook.
        </p>
    </header>

    <nav
        class="gmrc-spellbook__breadcrumb"
        aria-label="Guild Library breadcrumb"
    >
        <a href="<?php echo esc_url($libraryUrl); ?>">Guild Library</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Background Register</span>
    </nav>

    <div class="gmrc-background-register__grid">
        <?php foreach ($backgrounds as $background) : ?>
            <article
                class="gmrc-background-register-card"
                data-background-reference="<?php echo esc_attr(
                    (string) ($background['key'] ?? '')
                ); ?>"
            >
                <header>
                    <p class="gmrc-eyebrow">Optional Marketrealm Background</p>
                    <h2><?php echo esc_html((string) ($background['name'] ?? '')); ?></h2>
                </header>

                <dl class="gmrc-definition-list">
                    <div>
                        <dt>Skills</dt>
                        <dd>
                            <?php echo esc_html(
                                implode(
                                    ', ',
                                    array_map(
                                        static fn (string $skill): string =>
                                            ucwords(str_replace('-', ' ', $skill)),
                                        $background['skills'] ?? []
                                    )
                                )
                            ); ?>
                        </dd>
                    </div>
                    <div>
                        <dt>Tool</dt>
                        <dd><?php echo esc_html((string) ($background['tool_label'] ?? '')); ?></dd>
                    </div>
                </dl>

                <section class="gmrc-background-register-card__feature">
                    <h3><?php echo esc_html((string) ($background['feature_name'] ?? '')); ?></h3>
                    <p><?php echo esc_html((string) ($background['feature_detail'] ?? '')); ?></p>
                </section>

                <p class="gmrc-background-register-card__source">
                    The handbook does not state languages or starting
                    equipment for this optional background.
                </p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
