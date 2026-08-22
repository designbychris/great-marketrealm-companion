<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Controllers;

use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Services\DungeonMasterAccess;

defined('ABSPATH') || exit;

/**
 * Dungeon Master's Desk controller.
 */
final class DungeonMasterController
{
    public function __construct(
        private ViewFactory $views,
        private DungeonMasterAccess $access
    ) {
    }

    public function index(): string
    {
        if (! $this->access->allows()) {
            status_header(403);

            return $this->views->render(
                View::make('dungeonmaster.forbidden')
            );
        }

        $member = wp_get_current_user();

        return $this->views->render(
            View::make(
                'dungeonmaster.index',
                [
                    'displayName' => (string) $member->display_name,
                    'quickLinks' => $this->quickLinks(),
                ]
            )
        );
    }

    /**
     * @return array<int,array{label:string,route:string,description:string}>
     */
    private function quickLinks(): array
    {
        return [
            [
                'label' => 'Character Register',
                'route' => 'characters',
                'description' => 'Review the adventurers already recorded in the Companion.',
            ],
            [
                'label' => 'Fellowships',
                'route' => 'parties',
                'description' => 'Open the existing Fellowship ledgers and party records.',
            ],
            [
                'label' => 'Guild Library',
                'route' => 'library',
                'description' => 'Consult backgrounds, armoury stock, relics, and spell records.',
            ],
        ];
    }
}
