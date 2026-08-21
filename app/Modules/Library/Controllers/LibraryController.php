<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Controllers;

use GreatMarketrealmCompanion\Core\Http\Request;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Library\Models\ReferenceLibraryRegistry;
use GreatMarketrealmCompanion\Modules\Library\Relics\Services\RelicRegisterPresenter;
use GreatMarketrealmCompanion\Modules\Library\Spells\Services\SpellbookPresenter;

defined('ABSPATH') || exit;

final class LibraryController
{
    public function __construct(
        private ReferenceLibraryRegistry $library,
        private ViewFactory $views,
        private Request $request,
        private ?SpellbookPresenter $spellbook = null,
        private ?RelicRegisterPresenter $relics = null
    ) {
        $this->spellbook ??= new SpellbookPresenter();
        $this->relics ??= new RelicRegisterPresenter();
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

    public function relics(): string
    {
        return $this->views->render(
            View::make(
                'library.relics.index',
                $this->relics->present(
                    [
                        'q' => $this->request->string('q'),
                        'rarity' => $this->request->string('rarity'),
                        'group' => $this->request->string('group'),
                    ]
                )
            )
        );
    }

    public function armoury(): string
    {
        $catalogue = $this->library->get('armoury');
        $entries = $catalogue?->entries() ?? [];

        $groups = [
            'weapon' => [],
            'armour' => [],
            'shield' => [],
            'gear' => [],
        ];

        foreach ($entries as $entry) {
            $category = (string) (
                $entry['category']
                ?? ''
            );

            if (isset($groups[$category])) {
                $groups[$category][] = $entry;
            }
        }

        return $this->views->render(
            View::make(
                'library.armoury.index',
                [
                    'entries' => $entries,
                    'groups' => $groups,
                ]
            )
        );
    }

    public function backgrounds(): string
    {
        $catalogue = $this->library->get('backgrounds');

        return $this->views->render(
            View::make(
                'library.backgrounds.index',
                ['backgrounds' => $catalogue?->entries() ?? []]
            )
        );
    }

    public function spells(): string
    {
        return $this->views->render(
            View::make(
                'library.spells.index',
                $this->spellbook->present(
                    [
                        'q' =>
                            $this->request->string(
                                'q'
                            ),
                        'kind' =>
                            $this->request->string(
                                'kind'
                            ),
                        'level' =>
                            $this->request->string(
                                'level'
                            ),
                        'school' =>
                            $this->request->string(
                                'school'
                            ),
                        'access' =>
                            $this->request->string(
                                'access'
                            ),
                    ]
                )
            )
        );
    }
}
