<?php

namespace GreatMarketrealmCompanion\Core\View;

defined('ABSPATH') || exit;

/**
 * View Factory.
 *
 * Renders application views.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class ViewFactory
{
    /**
     * View finder.
     */
    protected ViewFinder $finder;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFinder $finder
    ) {
        $this->finder = $finder;
    }

    /**
     * Render a view.
     */
    public function render(
        View $view
    ): string {

        $path = $this->finder->find(
            $view->name()
        );

        extract(
            $view->data(),
            EXTR_SKIP
        );

        ob_start();

        require $path;

        return ob_get_clean();
    }
}
