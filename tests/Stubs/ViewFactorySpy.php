<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;

/**
 * Test view factory that records rendered views.
 */
final class ViewFactorySpy extends ViewFactory
{
    /**
     * Recorded views.
     *
     * @var array<int, View>
     */
    private array $views = [];

    /**
     * The spy does not require ViewFactory's production dependencies.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render(
        View $view
    ): string {
        $this->views[] = $view;

        return '<view />';
    }

    /**
     * Return every rendered view.
     *
     * @return array<int, View>
     */
    public function views(): array
    {
        return $this->views;
    }

    /**
     * Return the last rendered view.
     */
    public function lastView(): ?View
    {
        if ($this->views === []) {
            return null;
        }

        return $this->views[
            array_key_last($this->views)
        ];
    }
}
