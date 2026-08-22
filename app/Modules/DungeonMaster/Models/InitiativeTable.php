<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

defined('ABSPATH') || exit;

final class InitiativeTable
{
    /**
     * @param array<int,array<string,mixed>> $combatants
     * @param array<int,array<string,mixed>> $log
     */
    private function __construct(
        private int $round,
        private int $turnIndex,
        private array $combatants,
        private array $log
    ) {}

    /**
     * @param array<int,array<string,mixed>> $combatants
     * @param array<int,array<string,mixed>> $log
     */
    public static function restore(
        int $round,
        int $turnIndex,
        array $combatants,
        array $log = []
    ): self {
        $combatants = array_values($combatants);

        return new self(
            max(1, $round),
            $combatants === []
                ? 0
                : min(max(0, $turnIndex), count($combatants) - 1),
            $combatants,
            array_values(array_slice($log, -80))
        );
    }

    /** @param array<int,array<string,mixed>> $combatants */
    public static function fresh(array $combatants): self
    {
        return self::restore(1, 0, $combatants, []);
    }

    public function round(): int { return $this->round; }
    public function turnIndex(): int { return $this->turnIndex; }
    /** @return array<int,array<string,mixed>> */
    public function combatants(): array { return $this->combatants; }
    /** @return array<int,array<string,mixed>> */
    public function log(): array { return $this->log; }
    public function isEmpty(): bool { return $this->combatants === []; }
}
