<?php

namespace GreatMarketrealmCompanion\Core\Actions;

defined('ABSPATH') || exit;

/**
 * Base application Action.
 *
 * Actions encapsulate a single reusable business operation.
 *
 * @package MarketrealmCompanion
 * @since 0.5.0
 */
abstract class Action
{
    /**
     * Execute the Action.
     */
    abstract public function handle(
        mixed ...$arguments
    ): mixed;
}
