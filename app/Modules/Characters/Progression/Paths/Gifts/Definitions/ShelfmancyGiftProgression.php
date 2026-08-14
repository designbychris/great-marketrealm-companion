<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;

defined('ABSPATH') || exit;

final class ShelfmancyGiftProgression implements PathGiftProgressionDefinitionInterface
{
    private const PATH = 'school-of-shelfmancy';

    public function supports(string $pathKey): bool
    {
        return sanitize_key($pathKey) === self::PATH;
    }

    public function pathKey(): string
    {
        return self::PATH;
    }

    public function pathLabel(): string
    {
        return 'School of Shelfmancy';
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return [
            [
                'key' => 'spell-stored-container',
                'label' => 'Spell-Stored Container',
                'level' => 2,
                'summary' => 'Bind a 1st-level spell into a Sealed Jar for later release.',
                'detail' => 'During a long rest, a Shelfmancer can bind a 1st-level spell into a Sealed Jar. The jar can later be unsealed as a bonus action to cast the stored spell without expending its usual slot or components. The number of maintained jars is tied to Intelligence.',
                'mode' => 'automatic',
            ],
            [
                'key' => 'packaging-proficiency',
                'label' => 'Packaging Proficiency',
                'level' => 2,
                'summary' => 'Gain specialist packaging tools and enhanced container-handling magic.',
                'detail' => 'Shelfmancy grants proficiency with alchemist’s supplies and cook’s utensils, together with the Path’s enhanced Mage Hand container-handling technique.',
                'mode' => 'automatic',
            ],
            [
                'key' => 'vacuum-lock',
                'label' => 'Vacuum Lock',
                'level' => 6,
                'summary' => 'Seal a nearby creature inside a magical packaging field.',
                'detail' => 'Once per long rest, a Shelfmancer can invoke a magically enhanced holding effect near shelving, crates or displays. The sealed target is protected briefly before the packaging field violently collapses.',
                'mode' => 'automatic',
            ],
            [
                'key' => 'dimensional-pantry',
                'label' => 'Dimensional Pantry',
                'level' => 10,
                'summary' => 'Turn extradimensional shelter magic into a climate-controlled travelling pantry.',
                'detail' => 'The Shelfmancer gains the Path’s dimensional pantry techniques, including a modified magical shelter and access to a grand extradimensional market once per long rest.',
                'mode' => 'automatic',
            ],
            [
                'key' => 'master-of-the-endless-aisles',
                'label' => 'Master of the Endless Aisles',
                'level' => 14,
                'summary' => 'Fold Marketrealm space and escape impossible magical prisons.',
                'detail' => 'The master Shelfmancer can bend familiar Marketrealm distances, open a Portal Aisle, and instinctively find routes through magical effects designed to imprison or isolate.',
                'mode' => 'automatic',
            ],
        ];
    }
}
