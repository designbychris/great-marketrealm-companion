<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Spells\Repositories;

use GreatMarketrealmCompanion\Modules\Library\Spells\Models\SpellRecord;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Read-only transcription of the Player's Handbook Spellbook section.
 */
final class HandbookSpellRegister
{
    /** @var array<string,SpellRecord>|null */
    private ?array $records = null;

    /** @return array<int,SpellRecord> */
    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $key): ?SpellRecord
    {
        return $this->records()[sanitize_key($key)] ?? null;
    }

    /** @return array<int,SpellRecord> */
    public function byKind(string $kind): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (SpellRecord $spell): bool =>
                    $spell->kind() === $kind
            )
        );
    }

    /** @return array<int,SpellRecord> */
    public function byLevel(?int $level): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (SpellRecord $spell): bool =>
                    $spell->level() === $level
            )
        );
    }

    /** @return array<int,SpellRecord> */
    public function withSourceIssues(): array
    {
        return array_values(
            array_filter(
                $this->all(),
                static fn (SpellRecord $spell): bool =>
                    $spell->sourceIssues() !== []
            )
        );
    }

    public function sourceVariantCount(): int
    {
        return array_sum(
            array_map(
                static fn (SpellRecord $spell): int =>
                    count($spell->variants()),
                $this->all()
            )
        );
    }

    /** @return array<string,SpellRecord> */
    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $definitions = require dirname(__DIR__)
            . '/Data/handbook-spells.php';

        if (! is_array($definitions)) {
            throw new InvalidArgumentException(
                'Handbook spell definitions must return an array.'
            );
        }

        $records = [];

        foreach ($definitions as $definition) {
            if (! is_array($definition)) {
                continue;
            }

            $spell = SpellRecord::fromArray($definition);

            if (isset($records[$spell->key()])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Canonical spell key "%s" is duplicated.',
                        $spell->key()
                    )
                );
            }

            $records[$spell->key()] = $spell;
        }

        $this->records = $records;

        return $this->records;
    }
}
