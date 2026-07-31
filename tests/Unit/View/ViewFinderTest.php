<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\View;

use GreatMarketrealmCompanion\Core\View\ViewFinder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ViewFinderTest extends TestCase
{
    private string $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = realpath(
            __DIR__ . '/../../Fixtures/Views'
        ) . DIRECTORY_SEPARATOR;
    }

    public function testUsesCustomBasePath(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $this->assertSame(
            $this->fixtures,
            $finder->basePath()
        );
    }

    public function testFindsSharedView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'welcome'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Views/welcome.php',
            $path
        );
    }

    public function testFindsNestedSharedView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'nested.example'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Views/nested/example.php',
            $path
        );
    }

    public function testFindsComponentView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'components.furniture.auby-note'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Views/components/furniture/auby-note.php',
            $path
        );
    }

    public function testFindsModuleView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'characters.index'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Modules/Characters/Views/index.php',
            $path
        );
    }

    public function testFindsAnotherModuleView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'characters.show'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Modules/Characters/Views/show.php',
            $path
        );
    }

    public function testFindsNestedModuleView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'inventory.items.list'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Modules/Inventory/Views/items/list.php',
            $path
        );
    }

    public function testFindsDeeplyNestedModuleView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'characters.nested.profile'
        );

        $this->assertSame(
            $this->fixtures .
            'app/Modules/Characters/Views/nested/profile.php',
            $path
        );
    }

    public function testModuleNameIsNormalised(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $path = $finder->find(
            'characters.index'
        );

        $this->assertStringContainsString(
            'Modules/Characters/',
            str_replace(
                '\\',
                '/',
                $path
            )
        );
    }

    public function testThrowsExceptionForMissingSharedView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $this->expectException(
            RuntimeException::class
        );

        $finder->find(
            'missing'
        );
    }

    public function testThrowsExceptionForMissingModuleView(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $this->expectException(
            RuntimeException::class
        );

        $finder->find(
            'characters.missing'
        );
    }

    public function testExceptionContainsViewName(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        try {
            $finder->find(
                'does.not.exist'
            );

            $this->fail(
                'Expected RuntimeException was not thrown.'
            );
        } catch (RuntimeException $e) {

            $this->assertStringContainsString(
                'does.not.exist',
                $e->getMessage()
            );
        }
    }

    public function testExceptionContainsSharedPath(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        try {
            $finder->find(
                'missing'
            );

            $this->fail();
        } catch (RuntimeException $e) {

            $this->assertStringContainsString(
                'app/Views/missing.php',
                str_replace(
                    '\\',
                    '/',
                    $e->getMessage()
                )
            );
        }
    }

    public function testExceptionContainsModulePath(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        try {
            $finder->find(
                'characters.unknown'
            );

            $this->fail();
        } catch (RuntimeException $e) {

            $this->assertStringContainsString(
                'app/Modules/Characters/Views/unknown.php',
                str_replace(
                    '\\',
                    '/',
                    $e->getMessage()
                )
            );
        }
    }

    public function testMultipleCallsReturnConsistentResults(): void
    {
        $finder = new ViewFinder(
            $this->fixtures
        );

        $first = $finder->find(
            'welcome'
        );

        $second = $finder->find(
            'welcome'
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
