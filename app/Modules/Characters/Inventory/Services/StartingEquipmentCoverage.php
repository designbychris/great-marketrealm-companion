<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Services;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;

defined('ABSPATH') || exit;

final class StartingEquipmentCoverage
{
    /** @var string[] */
    public const CALLINGS = [
        'artificer', 'barbarian', 'bard', 'cleric', 'druid', 'fighter', 'monk',
        'paladin', 'ranger', 'rogue', 'sorcerer', 'warlock', 'wizard',
    ];

    public function __construct(
        private readonly StartingEquipmentPackageRegister $register,
        private readonly ItemCatalogue $catalogue = new ItemCatalogue()
    ) {}

    /** @return array<string,mixed> */
    public function report(): array
    {
        $packages = $this->register->all();
        $seen = [];
        $packageReports = [];
        $missingLinks = 0;
        $sourceCounts = [];

        foreach ($packages as $package) {
            $issues = [];
            if (isset($seen[$package->id()])) { $issues[] = 'Duplicate package ID.'; }
            $seen[$package->id()] = true;
            if (! in_array($package->classKey(), self::CALLINGS, true)) { $issues[] = 'Unknown Calling.'; }
            if ($package->items() === []) { $issues[] = 'Package is empty.'; }
            foreach ($package->items() as $itemId => $quantity) {
                if ($this->catalogue->find($itemId) === null) { $issues[] = 'Unknown Armoury item: ' . $itemId; $missingLinks++; }
                if ((int) $quantity < 1) { $issues[] = 'Invalid quantity for ' . $itemId . '.'; }
            }
            $source = trim($package->source());
            if ($source === '') { $issues[] = 'Missing source provenance.'; }
            $sourceCounts[$source !== '' ? $source : 'Unspecified'] = ($sourceCounts[$source !== '' ? $source : 'Unspecified'] ?? 0) + 1;
            $packageReports[$package->id()] = [
                'certified' => $issues === [],
                'issues' => $issues,
                'is_default' => $this->register->defaultForClass($package->classKey())?->id() === $package->id(),
                'source' => $source,
            ];
        }

        $callingReports = [];
        foreach (self::CALLINGS as $calling) {
            $callingPackages = $this->register->forClass($calling);
            $issues = count($callingPackages) < 2 ? ['Fewer than two starting-kit choices.'] : [];
            if ($this->register->defaultForClass($calling) === null) { $issues[] = 'No deterministic default package.'; }
            foreach ($callingPackages as $package) {
                if (! ($packageReports[$package->id()]['certified'] ?? false)) { $issues[] = 'Contains an uncertified package.'; break; }
            }
            $callingReports[$calling] = [
                'certified' => $issues === [],
                'package_count' => count($callingPackages),
                'default_package' => $this->register->defaultForClass($calling)?->id() ?? '',
                'issues' => $issues,
            ];
        }

        $certifiedCallings = count(array_filter($callingReports, static fn (array $entry): bool => $entry['certified']));
        $certifiedPackages = count(array_filter($packageReports, static fn (array $entry): bool => $entry['certified']));

        return [
            'certified' => $certifiedCallings === count(self::CALLINGS) && $certifiedPackages === count($packages),
            'calling_count' => count(self::CALLINGS),
            'certified_callings' => $certifiedCallings,
            'package_count' => count($packages),
            'certified_packages' => $certifiedPackages,
            'missing_armoury_links' => $missingLinks,
            'source_counts' => $sourceCounts,
            'callings' => $callingReports,
            'packages' => $packageReports,
            'background_policy' => 'No background equipment is granted unless a canonical source defines it.',
        ];
    }
}
