<?php

defined('ABSPATH') || exit;

$componentsPath = GMRC_PATH . 'resources/views/components/';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        <?php
        echo esc_html(
            $pageTitle ?? get_bloginfo('name')
        );
        ?>
    </title>

    <?php wp_head(); ?>

</head>

<body <?php body_class('gmrc-app'); ?>>

<?php wp_body_open(); ?>

<div class="gmrc-app">

    <?php
    $header = $componentsPath . 'header.php';

    if (file_exists($header)) {
        require $header;
    }
    ?>

    <div class="gmrc-container">

        <?php
        $sidebar = $componentsPath . 'sidebar.php';

        if (file_exists($sidebar)) {
            require $sidebar;
        }
        ?>

        <main class="gmrc-content">

            <?php echo $content; ?>

        </main>

    </div>

    <?php
    $footer = $componentsPath . 'footer.php';

    if (file_exists($footer)) {
        require $footer;
    }
    ?>

</div>

<?php wp_footer(); ?>

</body>

</html>
