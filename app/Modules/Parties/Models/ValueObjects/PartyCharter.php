<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Written identity carried by a Fellowship.
 */
final class PartyCharter
{
    private const MAX_MOTTO = 90;
    private const MAX_DESCRIPTION = 240;
    private const MAX_STATEMENT = 1200;

    private function __construct(
        private readonly string $motto,
        private readonly string $description,
        private readonly string $statement
    ) {
        $this->guard(
            $motto,
            self::MAX_MOTTO,
            'Fellowship motto'
        );

        $this->guard(
            $description,
            self::MAX_DESCRIPTION,
            'Fellowship description'
        );

        $this->guard(
            $statement,
            self::MAX_STATEMENT,
            'Fellowship charter'
        );
    }

    public static function blank(): self
    {
        return new self('', '', '');
    }

    public static function make(
        string $motto,
        string $description,
        string $statement
    ): self {
        return new self(
            trim($motto),
            trim($description),
            trim($statement)
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return self::make(
            (string) ($data['motto'] ?? ''),
            (string) ($data['description'] ?? ''),
            (string) ($data['statement'] ?? '')
        );
    }

    public function motto(): string
    {
        return $this->motto;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function statement(): string
    {
        return $this->statement;
    }

    public function isBlank(): bool
    {
        return $this->motto === ''
            && $this->description === ''
            && $this->statement === '';
    }

    /**
     * @return array{
     *     motto:string,
     *     description:string,
     *     statement:string
     * }
     */
    public function toArray(): array
    {
        return [
            'motto' => $this->motto,
            'description' => $this->description,
            'statement' => $this->statement,
        ];
    }

    private function guard(
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

        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $value) === 1) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s cannot contain control characters.',
                    $label
                )
            );
        }
    }
}
