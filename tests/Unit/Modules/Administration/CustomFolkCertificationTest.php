<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use GreatMarketrealmCompanion\Modules\Administration\Workshop\CustomFolkCertification;
use PHPUnit\Framework\TestCase;

final class CustomFolkCertificationTest extends TestCase
{
    public function testCompleteCustomHeritageCanBeCertified(): void
    {
        $folk = [
            'key' => 'steward-folk-piefolk',
            'heritages' => [[
                'name' => 'Deep Dish',
                'parent' => 'steward-folk-piefolk',
                'mechanics' => [
                    'size' => 'Medium',
                    'speed' => 25,
                    'features' => [[
                        'name' => 'Hearty Crust',
                        'description' => 'A complete published mechanical trait.',
                    ]],
                    'proficiency_choices' => [[
                        'name' => 'Kitchen School',
                        'choose' => 1,
                        'from' => ['Cook utensils', 'Brewer supplies'],
                    ]],
                ],
            ]],
        ];

        self::assertSame([], (new CustomFolkCertification())->errors($folk));
    }

    public function testIncompleteChoiceBlocksPublicationCertification(): void
    {
        $folk = [
            'key' => 'steward-folk-piefolk',
            'heritages' => [[
                'name' => 'Unlidded',
                'parent' => 'steward-folk-piefolk',
                'mechanics' => [
                    'proficiency_choices' => [[
                        'name' => 'Open Filling',
                        'choose' => 2,
                        'from' => ['Acrobatics'],
                    ]],
                ],
            ]],
        ];

        self::assertNotSame([], (new CustomFolkCertification())->errors($folk));
    }

    public function testWorkshopExposesCertifiableHeritageFields(): void
    {
        $root = dirname(__DIR__, 4);
        $view = (string) file_get_contents($root . '/app/Modules/Administration/Views/folk-workshop.php');
        $workshop = (string) file_get_contents($root . '/app/Modules/Administration/Workshop/FolkWorkshop.php');

        self::assertStringContainsString('[features]', $view);
        self::assertStringContainsString('[proficiency_choices]', $view);
        self::assertStringContainsString('Publication certification:', $view);
        self::assertStringContainsString('new CustomFolkCertification()', $workshop);
    }
}
