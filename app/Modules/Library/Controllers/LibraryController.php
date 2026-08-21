<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Library\Models\ReferenceLibraryRegistry;

defined('ABSPATH') || exit;

final class LibraryController
{
    public function __construct(
        private ReferenceLibraryRegistry $library,
        private ViewFactory $views
    ) {
    }

    public function index(): string
    {
        return $this->views->render(
            View::make(
                'library.index',
                [
                    'domains' =>
                        $this->library->summaries(),
                ]
            )
        );
    }
}
