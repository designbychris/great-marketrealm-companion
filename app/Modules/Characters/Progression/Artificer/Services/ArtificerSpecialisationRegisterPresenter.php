<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Artificer\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Spellcasting\Models\SpellcastingProgressionCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Artificer Specialisation Register for active-play orientation.
 */
final class ArtificerSpecialisationRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Infuse Item',
            'detail' =>
                'Repeatable magical infusions enter the Artificer’s workshop practice.',
        ],
        3 => [
            'level' => 3,
            'label' => 'Artificer Specialisation & The Right Tool for the Job',
            'detail' =>
                'Choose a specialist workshop tradition and gain the craft needed to produce the right tools for the work ahead.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Specialisation Gift',
            'detail' =>
                'The chosen Artificer Specialisation reaches its next supplied specialist milestone.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Tool Expertise',
            'detail' =>
                'Proficient tool work receives doubled proficiency.',
        ],
        7 => [
            'level' => 7,
            'label' => 'Flash of Genius',
            'detail' =>
                'Inventive brilliance can reinforce an ability check or saving throw nearby.',
        ],
        9 => [
            'level' => 9,
            'label' => 'Specialisation Gift',
            'detail' =>
                'The chosen workshop tradition reaches another specialist milestone.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Magic Item Adept',
            'detail' =>
                'Working with and attuning to magical items becomes markedly more efficient.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Spell-Storing Item',
            'detail' =>
                'Prepared magic can be stored inside a crafted object for repeated use.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Magic Item Savant',
            'detail' =>
                'The Artificer’s command of magical items expands beyond ordinary limitations.',
        ],
        15 => [
            'level' => 15,
            'label' => 'Final Specialisation Gift',
            'detail' =>
                'The chosen Artificer Specialisation reaches its final supplied specialist milestone.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Magic Item Master',
            'detail' =>
                'Magical item use and attunement reach extraordinary mastery.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Soul of Artifice',
            'detail' =>
                'The bond between inventor and crafted magic reaches its final Calling threshold.',
        ],
    ];

    public function __construct(
        private ?PathCandidateCatalogue $specialisations = null,
        private ?PathGiftCatalogue $gifts = null,
        private ?SpellcastingProgressionCatalogue $spellcasting = null
    ) {
        $this->specialisations ??=
            new PathCandidateCatalogue();

        $this->gifts ??=
            new PathGiftCatalogue();

        $this->spellcasting ??=
            new SpellcastingProgressionCatalogue();
    }

    /** @return array<string,mixed> */
    public function present(
        Character $character,
        ?ActiveClassResourceState $resources = null
    ): array {
        if (
            $character
                ->characterClass()
                ->value()
            !== 'artificer'
        ) {
            return [
                'supported' => false,
            ];
        }

        $resources ??=
            ActiveClassResourceState::fresh();

        $level = $character
            ->level()
            ->value();

        $specialisation = $character
            ->callingPath()
            ->value();

        $candidates =
            $this->specialisations->forClass(
                $character->characterClass()
            );

        $giftCount = $specialisation !== ''
            && $this->gifts->supports($specialisation)
                ? count(
                    $this->gifts->all($specialisation)
                )
                : 0;

        $casting = $level >= 2
            ? $this->spellcasting->forLevel(
                $character->characterClass(),
                $level
            )
            : null;

        return [
            'supported' => true,
            'level' => $level,
            'specialisation' => [
                'selection_level' => 3,
                'available' => $level >= 3,
                'chosen' => $specialisation !== '',
                'key' => $specialisation,
                'label' =>
                    $this->specialisationLabel(
                        $specialisation,
                        $candidates
                    ),
                'candidate_count' =>
                    count($candidates),
                'candidates' =>
                    $candidates,
                'gift_count' =>
                    $giftCount,
                'gift_status' =>
                    $giftCount > 0
                        ? 'Specialisation Gifts certified'
                        : 'Specialisation Gifts await their dedicated phase',
            ],
            'workshop' => [
                'magical_tinkering' => true,
                'infusions_unlocked' => $level >= 2,
                'right_tool_unlocked' => $level >= 3,
                'tool_expertise_unlocked' => $level >= 6,
                'flash_of_genius_unlocked' => $level >= 7,
                'spell_storing_item_unlocked' => $level >= 11,
            ],
            'spellcasting' => [
                'unlocked' => true,
                'model' => 'prepared-spells',
                'ability' => 'Intelligence',
                'cantrips_known' =>
                    (int) (
                        $casting['cantrips_known']
                        ?? 2
                    ),
                'maximum_spell_level' =>
                    (int) (
                        $casting['maximum_spell_level']
                        ?? 1
                    ),
                'prepared_formula' =>
                    (string) (
                        $casting['spells_prepared_formula']
                        ?? 'half-artificer-level + intelligence-modifier'
                    ),
                'slots' => (
                    new SharedSpellSlotReserveService()
                )->present(
                    $character,
                    $resources
                ),
            ],
            'next_milestone' =>
                $this->nextMilestone(
                    $level
                ),
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $candidates
     */
    private function specialisationLabel(
        string $specialisation,
        array $candidates
    ): string {
        if ($specialisation === '') {
            return 'Specialisation not yet chosen';
        }

        foreach ($candidates as $candidate) {
            if (
                ($candidate['key'] ?? '')
                === $specialisation
            ) {
                return (string) (
                    $candidate['label']
                    ?? $specialisation
                );
            }
        }

        return ucwords(
            str_replace(
                '-',
                ' ',
                $specialisation
            )
        );
    }

    /** @return array<string,mixed>|null */
    private function nextMilestone(
        int $level
    ): ?array {
        foreach (self::MILESTONES as $milestone) {
            if (
                $milestone['level']
                > $level
            ) {
                return $milestone;
            }
        }

        return null;
    }
}
