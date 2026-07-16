<?php

defined('ABSPATH') || exit;

use GreatMarketrealmCompanion\View\View;

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
            $page_title ?? get_bloginfo('name')
        );

        ?>

    </title>

    <?php wp_head(); ?>

</head>

<body <?php body_class('gmrc-app'); ?>>

<?php wp_body_open(); ?>

<div class="gmrc-app">

    <?php View::component('header'); ?>

    <div class="gmrc-container">

        <?php View::component('sidebar'); ?>

        <main class="gmrc-content">

            <?php

            echo $content;

            ?>

        </main>

    </div>

    <?php View::component('footer'); ?>

</div>

<?php wp_footer(); ?>

</body>

</html>
