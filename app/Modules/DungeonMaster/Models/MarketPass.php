<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

final class MarketPass
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REVOKED = 'revoked';
    public const DEFAULT_LIFETIME = 604800;

    private function __construct(
        private string $code,
        private int $issuedAt,
        private int $expiresAt,
        private string $status
    ) {
        if (strlen(self::normalise($code)) !== 8) {
            throw new InvalidArgumentException('A Market Pass requires an eight-character invite code.');
        }

        if ($issuedAt < 1 || $expiresAt <= $issuedAt) {
            throw new InvalidArgumentException('A Market Pass requires a valid issue and expiry window.');
        }
    }

    public static function issue(string $code, ?int $issuedAt = null): self
    {
        $issuedAt ??= time();

        return new self(
            self::format(self::normalise($code)),
            $issuedAt,
            $issuedAt + self::DEFAULT_LIFETIME,
            self::STATUS_ACTIVE
        );
    }

    public static function restore(
        string $code,
        int $issuedAt,
        int $expiresAt,
        string $status
    ): self {
        return new self(
            self::format(self::normalise($code)),
            $issuedAt,
            $expiresAt,
            $status === self::STATUS_REVOKED ? self::STATUS_REVOKED : self::STATUS_ACTIVE
        );
    }

    public function revoke(): void
    {
        $this->status = self::STATUS_REVOKED;
    }

    public function isRedeemable(?int $now = null): bool
    {
        $now ??= time();

        return $this->status === self::STATUS_ACTIVE && $now < $this->expiresAt;
    }

    public function isExpired(?int $now = null): bool
    {
        $now ??= time();

        return $now >= $this->expiresAt;
    }

    public function code(): string { return $this->code; }
    public function lookupCode(): string { return self::normalise($this->code); }
    public function issuedAt(): int { return $this->issuedAt; }
    public function expiresAt(): int { return $this->expiresAt; }
    public function status(): string { return $this->status; }

    public static function normalise(string $code): string
    {
        return strtoupper((string) preg_replace('/[^A-Z0-9]/i', '', trim($code)));
    }

    private static function format(string $code): string
    {
        return substr($code, 0, 4) . '-' . substr($code, 4, 4);
    }
}
