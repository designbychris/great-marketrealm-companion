<?php

namespace GreatMarketrealmCompanion\Core\View;

use GreatMarketrealmCompanion\Core\Session\FlashStore;

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
     * Create the view factory.
     */
    public function __construct(
        protected ViewFinder $finder,
        protected FlashStore $flash
    ) {
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
            array_merge(
                $this->sharedData(),
                $view->data()
            ),
            EXTR_SKIP
        );

        ob_start();

        require $path;

        return ob_get_clean();
    }

    /**
     * Retrieve data shared with every view.
     *
     * @return array<string, mixed>
     */
    protected function sharedData(): array
    {
        return [
            'old' => $this->flash->old(),
            'errors' => $this->flash->errors(),
            'flash' => [
                'success' => $this->flash->success(),
                'error' => $this->flash->error(),
            ],
        ];
    }
}
