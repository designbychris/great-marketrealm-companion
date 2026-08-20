<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Cleric\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SharedSpellSlotReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;

defined('ABSPATH') || exit;

/**
 * Read-only Sacred Domain Register for Cleric active-play orientation.
 */
final class ClericSacredDomainRegisterPresenter
{
    /**
     * @var array<int,array{level:int,label:string,detail:string}>
     */
    private const MILESTONES = [
        2 => [
            'level' => 2,
            'label' => 'Channel Divinity & Turn Undead',
            'detail' =>
                'The Cleric gains Channel Divinity and the sacred rite of Turn Undead.',
        ],
        5 => [
            'level' => 5,
            'label' => 'Destroy Undead',
            'detail' =>
                'Turn Undead begins destroying sufficiently weak undead.',
        ],
        6 => [
            'level' => 6,
            'label' => 'Channel Divinity Improvement',
            'detail' =>
                'Channel Divinity increases to two uses between rests.',
        ],
        8 => [
            'level' => 8,
            'label' => 'Destroy Undead & Domain Gift',
            'detail' =>
                'Destroy Undead improves and the Divine Domain reaches another sacred milestone.',
        ],
        10 => [
            'level' => 10,
            'label' => 'Divine Intervention',
            'detail' =>
                'The Cleric may call directly upon their divine power for extraordinary aid.',
        ],
        11 => [
            'level' => 11,
            'label' => 'Destroy Undead Improvement',
            'detail' =>
                'Destroy Undead threshold rises to CR 2.',
        ],
        14 => [
            'level' => 14,
            'label' => 'Destroy Undead Improvement',
            'detail' =>
                'Destroy Undead threshold rises to CR 3.',
        ],
        17 => [
            'level' => 17,
            'label' => 'Final Domain Gift & Destroy Undead',
            'detail' =>
                'The Domain reaches its final specialist gift and Destroy Undead reaches CR 4.',
        ],
        18 => [
            'level' => 18,
            'label' => 'Channel Divinity Improvement',
            'detail' =>
                'Channel Divinity increases to three uses between rests.',
        ],
        20 => [
            'level' => 20,
            'label' => 'Divine Intervention Improvement',
            'detail' =>
                'Divine Intervention reaches its final Calling threshold.',
        ],
    ];

    public function __construct(
        private ?ClericSacredPolicy $policy = null,
        private ?PathCandidateCatalogue $domains = null,
        private ?PathGiftCatalogue $gifts = null
    ) {
        $this->policy ??=
            new ClericSacredPolicy();

        $this->domains ??=
            new PathCandidateCatalogue();

        $this->gifts ??=
            new PathGiftCatalogue();
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
            !== 'cleric'
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

        $domain = $character
            ->callingPath()
            ->value();

        $candidates =
            $this->domains->forClass(
                $character
                    ->characterClass()
            );

        $giftCount = $domain !== ''
            && $this->gifts->supports($domain)
                ? count(
                    $this->gifts->all($domain)
                )
                : 0;

        return [
            'supported' => true,
            'level' => $level,
            'domain' => [
                'selection_level' => 1,
                'chosen' => $domain !== '',
                'key' => $domain,
                'label' =>
                    $this->domainLabel(
                        $domain,
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
                        ? 'Domain Gifts certified'
                        : 'Domain Gifts await their dedicated phase',
            ],
            'channel_divinity' => [
                'unlocked' => $level >= 2,
                'maximum' =>
                    $this->policy
                        ->channelDivinityMaximum(
                            $character
                        ),
                'next_improvement_level' =>
                    match (true) {
                        $level < 2 => 2,
                        $level < 6 => 6,
                        $level < 18 => 18,
                        default => null,
                    },
                'resource_tracking' =>
                    false,
            ],
            'destroy_undead' => [
                'unlocked' => $level >= 5,
                'threshold' =>
                    $this->policy
                        ->destroyUndeadThreshold(
                            $character
                        ),
            ],
            'divine_intervention' => [
                'unlocked' => $level >= 10,
                'improved' => $level >= 20,
            ],
            'spellcasting' => [
                'unlocked' => true,
                'model' =>
                    'prepared-spells',
                'ability' =>
                    'Wisdom',
                'prepared_maximum' =>
                    $this->policy
                        ->preparedSpellMaximum(
                            $character
                        ),
                'prepared_formula' =>
                    'Cleric level + Wisdom modifier',
                'cantrips_known' =>
                    $this->policy
                        ->cantripsKnown(
                            $character
                        ),
                'maximum_spell_level' =>
                    $this->policy
                        ->maximumSpellLevel(
                            $character
                        ),
                'save_dc' =>
                    $this->policy
                        ->spellSaveDc(
                            $character
                        ),
                'spell_attack' =>
                    $this->policy
                        ->spellAttackBonus(
                            $character
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
    private function domainLabel(
        string $domain,
        array $candidates
    ): string {
        if ($domain === '') {
            return 'Domain not yet chosen';
        }

        foreach ($candidates as $candidate) {
            if (
                ($candidate['key'] ?? '')
                === $domain
            ) {
                return (string) (
                    $candidate['label']
                    ?? $domain
                );
            }
        }

        return ucwords(
            str_replace(
                '-',
                ' ',
                $domain
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
