<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;

final class ViewFactorySpy extends ViewFactory
{
    public ?View $view = null;

    public function __construct()
    {
    }

    public function render(
        View $view
    ): string {
        $this->view = $view;

        return '<view />';
    }
}
