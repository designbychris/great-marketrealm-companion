<?php

defined('ABSPATH') || exit;

/**
 * Dashboard View
 *
 * Variables available:
 *
 * @var WP_User $user
 * @var array   $characters
 * @var int     $character_count
 */

?>

<div class="gmrc-dashboard">

    <header class="gmrc-page-header">

        <div>

            <h1>
                <?php esc_html_e('Great Marketrealm Companion', 'great-marketrealm-companion'); ?>
            </h1>

            <p class="gmrc-page-subtitle">

                <?php

                printf(
                    esc_html__(
                        'Welcome back, %s!',
                        'great-marketrealm-companion'
                    ),
                    esc_html($user->display_name)
                );

                ?>

            </p>

        </div>

    </header>

    <section class="gmrc-dashboard-grid">

        <article class="gmrc-card">

            <h2>
                <?php esc_html_e('Characters', 'great-marketrealm-companion'); ?>
            </h2>

            <div class="gmrc-stat">

                <?php echo esc_html($character_count); ?>

            </div>

            <p>

                <?php

                echo esc_html(
                    _n(
                        'Character',
                        'Characters',
                        $character_count,
                        'great-marketrealm-companion'
                    )
                );

                ?>

            </p>

        </article>

        <article class="gmrc-card">

            <h2>
                <?php esc_html_e('Inventory', 'great-marketrealm-companion'); ?>
            </h2>

            <p>

                <?php esc_html_e(
                    'Coming Soon',
                    'great-marketrealm-companion'
                ); ?>

            </p>

        </article>

        <article class="gmrc-card">

            <h2>
                <?php esc_html_e('Campaigns', 'great-marketrealm-companion'); ?>
            </h2>

            <p>

                <?php esc_html_e(
                    'Coming Soon',
                    'great-marketrealm-companion'
                ); ?>

            </p>

        </article>

        <article class="gmrc-card">

            <h2>
                <?php esc_html_e('Journal', 'great-marketrealm-companion'); ?>
            </h2>

            <p>

                <?php esc_html_e(
                    'Coming Soon',
                    'great-marketrealm-companion'
                ); ?>

            </p>

        </article>

    </section>

    <section class="gmrc-dashboard-actions">

        <a href="#"
           class="button button-primary gmrc-button">

            <?php esc_html_e(
                'Create Character',
                'great-marketrealm-companion'
            ); ?>

        </a>

    </section>

    <?php if (empty($characters)) : ?>

        <section class="gmrc-empty-state">

            <h2>

                <?php esc_html_e(
                    'No Characters Yet',
                    'great-marketrealm-companion'
                ); ?>

            </h2>

            <p>

                <?php esc_html_e(
                    'Your adventure begins here. Create your first Great Marketrealm hero!',
                    'great-marketrealm-companion'
                ); ?>

            </p>

        </section>

    <?php else : ?>

        <section class="gmrc-character-list">

            <h2>

                <?php esc_html_e(
                    'Your Characters',
                    'great-marketrealm-companion'
                ); ?>

            </h2>

            <?php foreach ($characters as $character) : ?>

                <article class="gmrc-character-card">

                    <h3>

                        <?php echo esc_html($character['name']); ?>

                    </h3>

                    <p>

                        <?php echo esc_html($character['race']); ?>

                        •

                        <?php echo esc_html($character['class']); ?>

                    </p>

                </article>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

</div>
