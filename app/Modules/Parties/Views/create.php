<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$old = is_array($old ?? null)
    ? $old
    : [];

$name = isset($old['name']) && is_scalar($old['name'])
    ? (string) $old['name']
    : '';

$flash = is_array($flash ?? null)
    ? $flash
    : [];
?>

<section class="gmrc-fellowship-form-page">
    <header class="gmrc-page-header">
        <p class="gmrc-eyebrow">The Fellowship Register</p>
        <h1>Form a Fellowship</h1>
        <p>
            Give the company a Guild name. Adventurers can be assembled
            once the Fellowship record has been inscribed.
        </p>
    </header>

    <aside class="gmrc-fellowship-auby-note">
        <span class="gmrc-fellowship-auby-note__seal" aria-hidden="true">🍆</span>
        <div>
            <strong>Auby advises</strong>
            <p>
                “Choose something heroic. ‘People Who Happened To Be Nearby’
                tested poorly with the Registrar.”
            </p>
        </div>
    </aside>

    <?php if (! empty($flash['error'])) : ?>
        <div class="gmrc-register-notice gmrc-register-notice--error" role="alert">
            <p><?php echo esc_html($flash['error']); ?></p>
        </div>
    <?php endif; ?>

    <form
        class="gmrc-fellowship-form"
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
        method="post"
    >
        <input type="hidden" name="action" value="gmrc_app_request">
        <input type="hidden" name="gmrc_route" value="parties">

        <?php wp_nonce_field(
            'gmrc_create_party',
            'gmrc_nonce'
        ); ?>

        <label class="gmrc-fellowship-field">
            <span>Fellowship name</span>
            <input
                type="text"
                name="name"
                value="<?php echo esc_attr($name); ?>"
                minlength="2"
                maxlength="80"
                autocomplete="organization"
                required
            >
            <small>
                This name will appear throughout the Guild Fellowship Archive.
            </small>
        </label>

        <div class="gmrc-fellowship-form__actions">
            <button
                class="gmrc-fellowship-button gmrc-fellowship-button--primary"
                type="submit"
            >
                Inscribe Fellowship
            </button>

            <a
                class="gmrc-fellowship-button gmrc-fellowship-button--quiet"
                href="<?php echo esc_url(
                    add_query_arg(
                        'gmrc_route',
                        'parties',
                        home_url('/companion/')
                    )
                ); ?>"
            >
                Return to Register
            </a>
        </div>
    </form>
</section>
