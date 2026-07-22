<?php

defined('ABSPATH') || exit;

$sidebar = GMRC_PATH
    . 'app/Core/View/Templates/components/sidebar.php';

?>

<div class="gmrc-app-shell">

    <?php
    if (file_exists($sidebar)) {
        require $sidebar;
    }
    ?>

    <div class="gmrc-app-main">

        <header class="gmrc-topbar">

            <div>

                <p class="gmrc-topbar__eyebrow">
                    Great Marketrealm Companion
                </p>

                <h1 class="gmrc-topbar__title">
                    <?php
                    echo esc_html(
                        $pageTitle ?? 'Dashboard'
                    );
                    ?>
                </h1>

            </div>

        </header>

        <main class="gmrc-content">

            <?php echo $content ?? ''; ?>

        </main>

    </div>

</div>
