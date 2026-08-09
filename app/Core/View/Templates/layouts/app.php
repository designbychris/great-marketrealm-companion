<?php

defined('ABSPATH') || exit;

$sidebar = GMRC_PATH
    . 'app/Core/View/Templates/components/sidebar.php';

$navigationCssPath =
    GMRC_PATH
    . 'assets/css/components/navigation/'
    . 'guild-navigation.css';

$navigationJsPath =
    GMRC_PATH
    . 'assets/js/components/navigation/'
    . 'guild-navigation.js';

$navigationCssVersion =
    file_exists($navigationCssPath)
        ? (string) filemtime($navigationCssPath)
        : GMRC_VERSION;

$navigationJsVersion =
    file_exists($navigationJsPath)
        ? (string) filemtime($navigationJsPath)
        : GMRC_VERSION;
?>

<link
    rel="stylesheet"
    href="<?php echo esc_url(
        GMRC_URL
        . 'assets/css/components/navigation/'
        . 'guild-navigation.css?ver='
        . rawurlencode($navigationCssVersion)
    ); ?>"
>

<div class="gmrc-app-shell gmrc-app-shell--guild-navigation">

    <header class="gmrc-topbar gmrc-topbar--guild-navigation">

        <?php
        if (file_exists($sidebar)) {
            require $sidebar;
        }
        ?>

    </header>

    <div class="gmrc-app-main">

        <main class="gmrc-content">

            <?php echo $content ?? ''; ?>

        </main>

    </div>

    <footer
        class="gmrc-guild-footer"
        aria-label="<?php echo esc_attr__(
            'Great Marketrealm Companion footer',
            'great-marketrealm-companion'
        ); ?>"
    >
        <div
            class="gmrc-guild-footer__garden"
            aria-hidden="true"
        ></div>

        <div class="gmrc-guild-footer__inscription">
            <strong>
                Where adventure meets ingredients.
            </strong>

            <span>
                Every adventure. Every hero. Every ingredient.
                Every story.
            </span>
        </div>
    </footer>

</div>

<script
    src="<?php echo esc_url(
        GMRC_URL
        . 'assets/js/components/navigation/'
        . 'guild-navigation.js?ver='
        . rawurlencode($navigationJsVersion)
    ); ?>"
    defer
></script>
