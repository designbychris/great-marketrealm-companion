<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Choices;

use InvalidArgumentException;

defined('ABSPATH') || exit;

final class ChoiceRequirement
{
    /**
     * @param array<int,string> $allowed
     */
    public function __construct(
        private string $key,
        private string $mode,
        private array $allowed,
        private int $minimum = 1,
        private ?int $maximum = null
    ) {
        $this->mode = ChoiceMode::validate($mode);

        if ($this->allowed === []) {
            throw new InvalidArgumentException(
                'A Choice Folio must provide at least one option.'
            );
        }

        $this->allowed = array_values(
            array_unique(
                array_map(
                    'sanitize_key',
                    $this->allowed
                )
            )
        );

        $this->maximum ??= $this->mode === ChoiceMode::SINGLE
            ? 1
            : count($this->allowed);

        if (
            $this->minimum < 0
            || $this->maximum < $this->minimum
        ) {
            throw new InvalidArgumentException(
                'Invalid advancement choice cardinality.'
            );
        }
    }

    public function key(): string
    {
        return $this->key;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function minimum(): int
    {
        return $this->minimum;
    }

    public function maximum(): int
    {
        return (int) $this->maximum;
    }

    /**
     * @param array<int,string> $selections
     */
    public function satisfiedBy(array $selections): bool
    {
        $normalised = $this->normalise($selections);
        $count = count($normalised);

        return $count >= $this->minimum()
            && $count <= $this->maximum();
    }

    /**
     * @param array<int,string> $selections
     * @return array<int,string>
     */
    public function normalise(array $selections): array
    {
        $normalised = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'sanitize_key',
                        $selections
                    ),
                    fn (string $selection): bool =>
                        in_array(
                            $selection,
                            $this->allowed,
                            true
                        )
                )
            )
        );

        if ($this->mode === ChoiceMode::SINGLE) {
            return array_slice(
                $normalised,
                0,
                1
            );
        }

        return array_slice(
            $normalised,
            0,
            $this->maximum()
        );
    }
}
