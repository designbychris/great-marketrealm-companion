<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Spells\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * One canonical Spell Register identity with one or more handbook variants.
 */
final class SpellRecord
{
    /** @param array<string,mixed> $record */
    private function __construct(
        private array $record
    ) {
    }

    /** @param array<string,mixed> $record */
    public static function fromArray(array $record): self
    {
        foreach (['key', 'name', 'kind', 'variants'] as $required) {
            if (! array_key_exists($required, $record)) {
                throw new InvalidArgumentException(
                    sprintf('Spell record is missing "%s".', $required)
                );
            }
        }

        if (! in_array($record['kind'], ['renamed', 'marketrealm-original'], true)) {
            throw new InvalidArgumentException('Spell record kind is not recognised.');
        }

        if (! is_array($record['variants']) || $record['variants'] === []) {
            throw new InvalidArgumentException('Spell record requires a source variant.');
        }

        return new self($record);
    }

    public function key(): string
    {
        return (string) $this->record['key'];
    }

    public function name(): string
    {
        return (string) $this->record['name'];
    }

    public function kind(): string
    {
        return (string) $this->record['kind'];
    }

    public function originalSpell(): ?string
    {
        $original = $this->record['original_spell'] ?? null;

        return is_string($original) && $original !== ''
            ? $original
            : null;
    }

    public function level(): ?int
    {
        $level = $this->record['level'] ?? null;

        return is_int($level) ? $level : null;
    }

    public function school(): ?string
    {
        $school = $this->record['school'] ?? null;

        return is_string($school) && $school !== ''
            ? $school
            : null;
    }

    /** @return array<int,string> */
    public function accessLabels(): array
    {
        return array_values(
            array_filter(
                $this->record['access_labels'] ?? [],
                'is_string'
            )
        );
    }

    /** @return array<int,string> */
    public function sourceIssues(): array
    {
        return array_values(
            array_filter(
                $this->record['source_issues'] ?? [],
                'is_string'
            )
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function variants(): array
    {
        return $this->record['variants'];
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key(),
            'name' => $this->name(),
            'kind' => $this->kind(),
            'original_spell' => $this->originalSpell(),
            'level' => $this->level(),
            'school' => $this->school(),
            'access_labels' => $this->accessLabels(),
            'source_issues' => $this->sourceIssues(),
            'variant_count' => count($this->variants()),
            'variants' => $this->variants(),
        ];
    }
}
