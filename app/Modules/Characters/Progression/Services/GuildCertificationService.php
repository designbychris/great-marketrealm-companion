<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Services;

use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CallingPath;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Models\PathProgressionCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\AdvancementHistoryRepository;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories\PendingAdvancementRepository;
use RuntimeException;

defined('ABSPATH') || exit;

final class GuildCertificationService
{
    public function __construct(
        private CharacterRepositoryInterface $characters,
        private PendingAdvancementRepository $pending,
        private AdvancementHistoryRepository $history,
        private ?HitPointGainResolver $hitPoints = null
    ) {
        $this->hitPoints ??=
            new HitPointGainResolver();
    }

    /** @return array<string,mixed> */
    public function certify(
        Character $character
    ): array {
        $pending = $this->pending->find(
            $character->id()
        );

        if ($pending === null) {
            throw new RuntimeException(
                'No pending advancement is waiting for certification.'
            );
        }

        $fromLevel = $character
            ->level()
            ->value();

        $targetLevel = $fromLevel + 1;

        if (! $pending->matches(
            $fromLevel,
            $targetLevel
        )) {
            throw new RuntimeException(
                'This advancement paperwork is stale and cannot be certified.'
            );
        }

        $advancement = (
            new AdvancementLedgerPresenter()
        )->present(
            $character,
            $pending->choices()
        );

        $seal = (
            new AdvancementSealPresenter()
        )->present(
            $character,
            $advancement
        );

        if (empty($seal['ready'])) {
            throw new RuntimeException(
                'Every required folio must be ready before Guild certification.'
            );
        }

        $oldMaximum = $character
            ->hitPoints()
            ->maximum();

        $hpGain = $this->hitPoints->resolve(
            $character,
            $pending->choices()
        );

        $character->learnArcana(
            $pending->choices()['wizard-spells']
                ?? [],
            $pending->choices()['wizard-cantrips']
                ?? []
        );

        $pathDefinition = (
            new PathProgressionCatalogue()
        )->forClass(
            $character->characterClass()
        );

        if (
            is_array($pathDefinition)
            && ! $character
                ->callingPath()
                ->isChosen()
            && $targetLevel >= (int) (
                $pathDefinition[
                    'selection_level'
                ] ?? 0
            )
        ) {
            $pathKey = (string) (
                $pathDefinition['choice_key']
                ?? ''
            );

            $pathValue = (string) (
                $pending
                    ->choices()[$pathKey][0]
                ?? ''
            );

            if ($pathValue === '') {
                throw new RuntimeException(
                    'The required Path of Calling has not been recorded.'
                );
            }

            $character->chooseCallingPath(
                CallingPath::fromString(
                    $pathValue
                )
            );
        }

        $pathGiftsGranted = [];

        if ($character->callingPath()->isChosen()) {
            $pathGiftsGranted = (new PathGiftCatalogue())->unlocked(
                $character->callingPath()->value(),
                $targetLevel,
                $character->pathGifts()
            );

            $character->grantPathGifts(array_values(array_map(
                static fn (array $gift): string =>
                    (string) ($gift['key'] ?? ''),
                $pathGiftsGranted
            )));
        }

        $character->certifyAdvancement(
            $hpGain
        );

        /*
         * Save the Character before clearing the pending record. If cleanup
         * is interrupted, the from-level contract above makes a replay
         * invalid, so certification remains idempotent.
         */
        $this->characters->save(
            $character
        );

        $result = [
            'certification_key' =>
                $character->id()->value()
                . ':'
                . $fromLevel
                . ':'
                . $targetLevel,
            'from_level' => $fromLevel,
            'target_level' => $targetLevel,
            'hit_point_gain' => $hpGain,
            'old_maximum_hp' => $oldMaximum,
            'new_maximum_hp' =>
                $character
                    ->hitPoints()
                    ->maximum(),
            'proficiency' =>
                $character
                    ->proficiencyBonus()
                    ->signed(),
            'choices' =>
                $pending->choices(),
            'spellbook' =>
                $character->spellbook()->toArray(),
            'calling_path' =>
                $character
                    ->callingPath()
                    ->value(),
            'path_gifts' =>
                $character->pathGifts()->values(),
            'path_gifts_granted' =>
                $pathGiftsGranted,
            'calling' => is_array(
                $advancement[
                    'class_progression'
                ] ?? null
            )
                ? $advancement[
                    'class_progression'
                ]
                : [],
            'certified_at' =>
                gmdate('c'),
        ];

        $this->history->append(
            $character->id(),
            $result
        );

        $this->pending->clear(
            $character->id()
        );

        return $result;
    }
}
