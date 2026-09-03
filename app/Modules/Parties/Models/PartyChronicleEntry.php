<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models;

use DateTimeImmutable;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleEntryId;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleEntryType;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyChronicleProvenance;
use InvalidArgumentException;

defined('ABSPATH') || exit;

final class PartyChronicleEntry
{
    private const MAX_TITLE = 120;
    private const MAX_CONTENT = 3000;

    private function __construct(
        private PartyChronicleEntryId $id,
        private PartyChronicleEntryType $type,
        private string $title,
        private string $content,
        private PartyChronicleProvenance $provenance,
        private int $authorUserId,
        private bool $certified,
        private DateTimeImmutable $recordedAt,
        private array $source = []
    ) {
        $this->guardText(
            $title,
            self::MAX_TITLE,
            'Chronicle title'
        );

        $this->guardText(
            $content,
            self::MAX_CONTENT,
            'Chronicle content'
        );

        if ($title === '' || $content === '') {
            throw new InvalidArgumentException(
                'A Chronicle entry requires both a title and content.'
            );
        }

        if ($authorUserId < 1) {
            throw new InvalidArgumentException(
                'A Chronicle entry requires a valid author.'
            );
        }

        if (
            $type->requiresCertification()
            && (
                ! $certified
                || ! $provenance->isDungeonMaster()
            )
        ) {
            throw new InvalidArgumentException(
                'Company Deeds and Fellowship Honours require Dungeon Master certification.'
            );
        }

        if (
            $type->isNote()
            && $certified
        ) {
            throw new InvalidArgumentException(
                'Adventure Notes are not certified records.'
            );
        }
    }

    public static function adventureNote(
        string $title,
        string $content,
        int $authorUserId,
        ?DateTimeImmutable $recordedAt = null
    ): self {
        return new self(
            PartyChronicleEntryId::generate(),
            PartyChronicleEntryType::note(),
            trim($title),
            trim($content),
            PartyChronicleProvenance::player(),
            $authorUserId,
            false,
            $recordedAt ?? new DateTimeImmutable('now'),
            []
        );
    }

    public static function certifiedRecord(
        PartyChronicleEntryType $type,
        string $title,
        string $content,
        int $dungeonMasterUserId,
        ?DateTimeImmutable $recordedAt = null,
        array $source = []
    ): self {
        if (! $type->requiresCertification()) {
            throw new InvalidArgumentException(
                'Only Company Deeds and Fellowship Honours may be certified.'
            );
        }

        return new self(
            PartyChronicleEntryId::generate(),
            $type,
            trim($title),
            trim($content),
            PartyChronicleProvenance::dungeonMaster(),
            $dungeonMasterUserId,
            true,
            $recordedAt ?? new DateTimeImmutable('now'),
            $source
        );
    }

    public function refreshCertifiedRecord(
        string $title,
        string $content,
        DateTimeImmutable $recordedAt
    ): void {
        if (! $this->isCertified()) {
            throw new InvalidArgumentException(
                'Only certified Chronicle records may be refreshed.'
            );
        }
        $this->guardText($title, self::MAX_TITLE, 'Chronicle title');
        $this->guardText($content, self::MAX_CONTENT, 'Chronicle content');
        $this->title = trim($title);
        $this->content = trim($content);
        $this->recordedAt = $recordedAt;
    }

    public function sourceValue(string $key): string
    {
        return trim((string) ($this->source[$key] ?? ''));
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            PartyChronicleEntryId::fromString(
                (string) ($data['id'] ?? '')
            ),
            PartyChronicleEntryType::fromString(
                (string) ($data['type'] ?? '')
            ),
            trim((string) ($data['title'] ?? '')),
            trim((string) ($data['content'] ?? '')),
            PartyChronicleProvenance::fromString(
                (string) ($data['provenance'] ?? '')
            ),
            (int) ($data['author_user_id'] ?? 0),
            (bool) ($data['certified'] ?? false),
            new DateTimeImmutable(
                (string) ($data['recorded_at'] ?? '')
            ),
            is_array($data['source'] ?? null) ? $data['source'] : []
        );
    }

    public function id(): PartyChronicleEntryId
    {
        return $this->id;
    }

    public function type(): PartyChronicleEntryType
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function provenance(): PartyChronicleProvenance
    {
        return $this->provenance;
    }

    public function authorUserId(): int
    {
        return $this->authorUserId;
    }

    public function isCertified(): bool
    {
        return $this->certified;
    }

    public function recordedAt(): DateTimeImmutable
    {
        return $this->recordedAt;
    }

    /**
     * @return array{
     *   id:string,type:string,title:string,content:string,
     *   provenance:string,author_user_id:int,certified:bool,
     *   recorded_at:string,source:array<string,mixed>
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'type' => $this->type->value(),
            'title' => $this->title,
            'content' => $this->content,
            'provenance' => $this->provenance->value(),
            'author_user_id' => $this->authorUserId,
            'certified' => $this->certified,
            'recorded_at' => $this->recordedAt->format(DATE_ATOM),
            'source' => $this->source,
        ];
    }

    private function guardText(
        string $value,
        int $maximum,
        string $label
    ): void {
        if (mb_strlen($value) > $maximum) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot contain more than %d characters.',
                    $label,
                    $maximum
                )
            );
        }

        if (
            preg_match(
                '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
                $value
            ) === 1
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot contain control characters.',
                    $label
                )
            );
        }
    }
}
