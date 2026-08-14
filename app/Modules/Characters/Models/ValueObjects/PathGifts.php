<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects;

defined('ABSPATH') || exit;

final class PathGifts
{
    /** @param array<int,string> $values */
    private function __construct(private array $values)
    {
        $this->values = $this->normalise($values);
    }

    public static function none(): self
    {
        return new self([]);
    }

    /** @param array<int,mixed> $values */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    /** @param array<int,string> $values */
    public function grant(array $values): self
    {
        return new self(array_merge($this->values, $values));
    }

    public function has(string $giftKey): bool
    {
        return in_array(sanitize_key($giftKey), $this->values, true);
    }

    /** @return array<int,string> */
    public function values(): array
    {
        return $this->values;
    }

    public function count(): int
    {
        return count($this->values);
    }

    /** @param array<int,mixed> $values @return array<int,string> */
    private function normalise(array $values): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn (mixed $value): string => sanitize_key((string) $value),
            $values
        ))));
    }
}
