<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;

defined('ABSPATH') || exit;

final class InitiativeTable
{
    /** @param array<int,array<string,mixed>> $combatants */
    private function __construct(private int $round, private int $turnIndex, private array $combatants) {}

    /** @param array<int,array<string,mixed>> $combatants */
    public static function restore(int $round, int $turnIndex, array $combatants): self
    {
        $combatants = array_values($combatants);
        return new self(max(1, $round), $combatants === [] ? 0 : min(max(0, $turnIndex), count($combatants) - 1), $combatants);
    }

    /** @param array<int,array<string,mixed>> $combatants */
    public static function fresh(array $combatants): self { return self::restore(1, 0, $combatants); }
    public function round(): int { return $this->round; }
    public function turnIndex(): int { return $this->turnIndex; }
    /** @return array<int,array<string,mixed>> */ public function combatants(): array { return $this->combatants; }
    public function isEmpty(): bool { return $this->combatants === []; }
}
