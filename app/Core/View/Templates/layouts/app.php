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
