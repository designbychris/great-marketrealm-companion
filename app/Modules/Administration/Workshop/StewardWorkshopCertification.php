<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

defined('ABSPATH') || exit;

/**
 * Read-only health projection for the complete Steward Workshop family.
 */
final class StewardWorkshopCertification
{
    public function __construct(
        private MonsterWorkshop $monsters,
        private SpellWorkshop $spells,
        private BackgroundWorkshop $backgrounds,
        private EquipmentWorkshop $equipment,
        private CallingWorkshop $callings
    ) {}

    /** @return array<string,mixed> */
    public function report(): array
    {
        $workshops = [
            'Monsters' => $this->monsters->all(),
            'Spells' => $this->spells->all(),
            'Backgrounds' => $this->backgrounds->all(),
            'Equipment' => $this->equipment->all(),
            'Callings & Paths' => $this->callings->all(),
        ];

        $totals = ['draft' => 0, 'published' => 0, 'archived' => 0, 'records' => 0];
        $rows = [];
        foreach ($workshops as $label => $records) {
            $counts = ['draft' => 0, 'published' => 0, 'archived' => 0];
            foreach ($records as $record) {
                $status = is_array($record) ? (string) ($record['status'] ?? '') : '';
                if (isset($counts[$status])) {
                    ++$counts[$status];
                    ++$totals[$status];
                }
            }
            $count = count($records);
            $totals['records'] += $count;
            $rows[] = ['label' => $label, 'records' => $count] + $counts;
        }

        return [
            'certified' => count($rows) === 5,
            'workshop_count' => count($rows),
            'statuses' => ['Draft', 'Published', 'Archived'],
            'rows' => $rows,
            'totals' => $totals,
            'policy' => 'Archive for normal retirement. Permanent deletion remains dependency-guarded.',
        ];
    }
}
