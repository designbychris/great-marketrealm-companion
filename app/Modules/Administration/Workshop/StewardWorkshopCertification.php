<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

defined('ABSPATH') || exit;

/**
 * Read-only health projection for the complete Steward content pipeline.
 */
final class StewardWorkshopCertification
{
    public function __construct(
        private MonsterWorkshop $monsters,
        private SpellWorkshop $spells,
        private BackgroundWorkshop $backgrounds,
        private EquipmentWorkshop $equipment,
        private CallingWorkshop $callings,
        private FolkWorkshop $folk
    ) {}

    /** @return array<string,mixed> */
    public function report(): array
    {
        $families = [
            $this->family(
                'Monsters',
                'monster-workshop',
                $this->monsters->all(),
                $this->monsters->published()
            ),
            $this->family(
                'Spells',
                'spell-workshop',
                $this->spells->all(),
                $this->spells->published()
            ),
            $this->family(
                'Backgrounds',
                'background-workshop',
                $this->backgrounds->all(),
                $this->backgrounds->published()
            ),
            $this->family(
                'Equipment & Items',
                'equipment-workshop',
                $this->equipment->all(),
                $this->equipment->published()
            ),
            $this->family(
                'Callings & Paths',
                'calling-workshop',
                $this->callings->all(),
                $this->callings->published()
            ),
        ];

        $folkRecords = $this->folk->all();
        $publishedFolk = $this->folk->published();

        $families[] = $this->family(
            'Folk',
            'folk-workshop',
            $folkRecords,
            $publishedFolk
        );
        $families[] = $this->heritageFamily(
            $folkRecords,
            $publishedFolk
        );

        $totals = [
            'records' => 0,
            'draft' => 0,
            'published' => 0,
            'archived' => 0,
            'attention' => 0,
        ];

        foreach ($families as $family) {
            $totals['records'] += (int) ($family['records'] ?? 0);
            $totals['draft'] += (int) ($family['draft'] ?? 0);
            $totals['published'] += (int) ($family['published'] ?? 0);
            $totals['archived'] += (int) ($family['archived'] ?? 0);

            if (empty($family['healthy'])) {
                ++$totals['attention'];
            }
        }

        $certified = count($families) === 7
            && $totals['attention'] === 0;

        return [
            'certified' => $certified,
            'family_count' => count($families),
            'workshop_count' => 6,
            'statuses' => ['Draft', 'Published', 'Archived'],
            'rows' => $families,
            'totals' => $totals,
            'policy' => 'Draft content remains private to the Steward. Published content may enter Companion catalogues. Archived content is retired without destructive loss. Permanent deletion remains dependency-guarded.',
            'seal' => $certified
                ? 'Content pipeline certified'
                : 'Content pipeline needs attention',
        ];
    }

    /**
     * @param array<string,array<string,mixed>> $records
     * @param array<string,array<string,mixed>> $published
     * @return array<string,mixed>
     */
    private function family(
        string $label,
        string $section,
        array $records,
        array $published
    ): array {
        $counts = [
            'draft' => 0,
            'published' => 0,
            'archived' => 0,
        ];
        $invalid = 0;

        foreach ($records as $record) {
            $status = is_array($record)
                ? (string) ($record['status'] ?? '')
                : '';

            if (isset($counts[$status])) {
                ++$counts[$status];
                continue;
            }

            ++$invalid;
        }

        $projectionCount = count($published);
        $healthy = $invalid === 0
            && $projectionCount === $counts['published'];

        return [
            'label' => $label,
            'section' => $section,
            'records' => count($records),
            'projection' => $projectionCount,
            'invalid' => $invalid,
            'healthy' => $healthy,
            'detail' => $healthy
                ? 'Lifecycle and published projection agree.'
                : 'Lifecycle data or published projection needs review.',
        ] + $counts;
    }

    /**
     * Heritages inherit the lifecycle of their parent Folk.
     *
     * @param array<string,array<string,mixed>> $folkRecords
     * @param array<string,array<string,mixed>> $publishedFolk
     * @return array<string,mixed>
     */
    private function heritageFamily(
        array $folkRecords,
        array $publishedFolk
    ): array {
        $counts = [
            'draft' => 0,
            'published' => 0,
            'archived' => 0,
        ];
        $invalid = 0;
        $records = 0;

        foreach ($folkRecords as $folk) {
            if (! is_array($folk)) {
                continue;
            }

            $status = (string) ($folk['status'] ?? '');
            $heritages = is_array($folk['heritages'] ?? null)
                ? $folk['heritages']
                : [];
            $heritageCount = count($heritages);
            $records += $heritageCount;

            if (isset($counts[$status])) {
                $counts[$status] += $heritageCount;
                continue;
            }

            $invalid += $heritageCount;
        }

        $projectionCount = 0;
        foreach ($publishedFolk as $folk) {
            if (! is_array($folk)) {
                continue;
            }

            $projectionCount += count(
                is_array($folk['heritages'] ?? null)
                    ? $folk['heritages']
                    : []
            );
        }

        $healthy = $invalid === 0
            && $projectionCount === $counts['published'];

        return [
            'label' => 'Heritages',
            'section' => 'folk-workshop',
            'records' => $records,
            'projection' => $projectionCount,
            'invalid' => $invalid,
            'healthy' => $healthy,
            'detail' => $healthy
                ? 'Heritages inherit their parent Folk lifecycle cleanly.'
                : 'A Heritage lifecycle inheritance mismatch needs review.',
        ] + $counts;
    }
}
