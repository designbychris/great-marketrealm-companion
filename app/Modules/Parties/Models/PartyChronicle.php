<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use DateTimeImmutable;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleEntryType;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class PartyChronicle
{
    /**
     * @param PartyChronicleEntry[] $entries
     */
    private function __construct(
        private array $entries
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param PartyChronicleEntry[] $entries
     */
    public static function reconstitute(array $entries): self
    {
        foreach ($entries as $entry) {
            if (! $entry instanceof PartyChronicleEntry) {
                throw new InvalidArgumentException(
                    'A Company Chronicle may only contain Chronicle entries.'
                );
            }
        }

        return new self(array_values($entries));
    }

    public function addAdventureNote(
        string $title,
        string $content,
        int $authorUserId
    ): PartyChronicleEntry {
        $entry = PartyChronicleEntry::adventureNote(
            $title,
            $content,
            $authorUserId
        );

        $this->entries[] = $entry;

        return $entry;
    }

    /** @param array<string,mixed> $source */
    public function addSessionNote(
        string $title,
        string $content,
        int $authorUserId,
        string $tabletopSessionId,
        array $source = []
    ): PartyChronicleEntry {
        $entry = PartyChronicleEntry::adventureNote(
            $title,
            $content,
            $authorUserId,
            null,
            array_merge(
                $source,
                [
                    'kind' => 'tabletop-session-note',
                    'tabletop_session_id' => trim($tabletopSessionId),
                ]
            )
        );
        $this->entries[] = $entry;
        return $entry;
    }

    public function addCertifiedRecord(
        PartyChronicleEntry $entry
    ): void {
        if (! $entry->isCertified()) {
            throw new InvalidArgumentException(
                'Only certified Chronicle records may use this boundary.'
            );
        }

        $this->entries[] = $entry;
    }

    /** @param array<string,mixed> $source */
    public function upsertCertifiedRecord(
        PartyChronicleEntryType $type,
        string $title,
        string $content,
        int $dungeonMasterUserId,
        DateTimeImmutable $recordedAt,
        array $source
    ): PartyChronicleEntry {
        $sourceId = trim((string) ($source['tabletop_session_id'] ?? ''));

        if ($sourceId !== '') {
            foreach ($this->entries as $entry) {
                if ($entry->sourceValue('tabletop_session_id') === $sourceId) {
                    $entry->refreshCertifiedRecord($title, $content, $recordedAt);
                    return $entry;
                }
            }
        }

        $entry = PartyChronicleEntry::certifiedRecord(
            $type,
            $title,
            $content,
            $dungeonMasterUserId,
            $recordedAt,
            $source
        );
        $this->addCertifiedRecord($entry);
        return $entry;
    }

    /**
     * @return PartyChronicleEntry[]
     */
    public function entries(): array
    {
        return $this->entries;
    }

    /**
     * @return PartyChronicleEntry[]
     */
    public function newestFirst(): array
    {
        return array_reverse($this->entries);
    }

    public function count(): int
    {
        return count($this->entries);
    }
}
