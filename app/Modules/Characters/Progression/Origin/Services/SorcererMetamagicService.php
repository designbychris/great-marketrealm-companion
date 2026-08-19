<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Origin\Services;

use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Models\ActiveClassResourceState;
use GreatMarketrealmCompanion\Modules\Characters\ActivePlay\Services\SorcererSorceryReserveService;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Sorcerer Metamagic selection and active-use policy.
 */
final class SorcererMetamagicService
{
    public function __construct(
        private ?SorcererOriginPolicy $origin = null,
        private ?SorcererMetamagicCatalogue $catalogue = null,
        private ?SorcererSorceryReserveService $sorcery = null
    ) {
        $this->origin ??=
            new SorcererOriginPolicy();

        $this->catalogue ??=
            new SorcererMetamagicCatalogue();

        $this->sorcery ??=
            new SorcererSorceryReserveService();
    }

    public function allowance(
        Character $character
    ): int {
        return $this->origin
            ->metamagicKnown(
                $character
            );
    }

    /**
     * @param array<int,string> $choices
     * @return array<int,string>
     */
    public function validateChoices(
        Character $character,
        array $choices
    ): array {
        $allowance =
            $this->allowance(
                $character
            );

        if ($allowance < 1) {
            throw new InvalidArgumentException(
                'Metamagic is certified from Sorcerer Level 3.'
            );
        }

        $normalised = [];

        foreach ($choices as $choice) {
            $key = sanitize_key(
                (string) $choice
            );

            $this->catalogue->find($key);

            if (
                ! in_array(
                    $key,
                    $normalised,
                    true
                )
            ) {
                $normalised[] = $key;
            }
        }

        if (
            count($normalised)
            !== $allowance
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Choose exactly %d Metamagic option%s for this Sorcerer level.',
                    $allowance,
                    $allowance === 1
                        ? ''
                        : 's'
                )
            );
        }

        sort($normalised);

        return $normalised;
    }

    /**
     * @param array<int,string> $selected
     */
    public function use(
        Character $character,
        ActiveClassResourceState $state,
        array $selected,
        string $metamagic,
        int $spellLevel = 0
    ): ActiveClassResourceState {
        $metamagic =
            sanitize_key(
                $metamagic
            );

        if (
            ! in_array(
                $metamagic,
                $selected,
                true
            )
        ) {
            throw new InvalidArgumentException(
                'That Metamagic Art is not certified for this Sorcerer.'
            );
        }

        $cost = $this->catalogue
            ->cost(
                $metamagic,
                $spellLevel
            );

        return $this->sorcery
            ->spend(
                $character,
                $state,
                $cost
            );
    }
}
