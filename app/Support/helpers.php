<?php

use GreatMarketrealmCompanion\Core\Application;

defined('ABSPATH') || exit;

if (! function_exists('gmrc')) {

    /**
     * Return the Marketrealm Companion application.
     */
    function gmrc(): Application
    {
        global $gmrc;

        return $gmrc;
    }
}
