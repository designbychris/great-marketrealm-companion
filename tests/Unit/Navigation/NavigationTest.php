<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Navigation;

use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;
use PHPUnit\Framework\TestCase;

final class NavigationTest extends TestCase
{
    private function makeItem(
        string $key = 'characters',
        string $label = 'Characters'
    ): MenuItem {
        return MenuItem::make(
            $key,
            $label,
            'users',
            '/' . $key
        );
    }

    public function testNavigationCanBeCreated(): void
    {
        $this->assertInstanceOf(
            Navigation::class,
            new Navigation()
        );
    }

    public function testNavigationIsEmptyInitially(): void
    {
        $navigation = new Navigation();

        $this->assertSame(
            [],
            $navigation->items()
        );
    }

    public function testCountReturnsZeroInitially(): void
    {
        $navigation = new Navigation();

        $this->assertSame(
            0,
            $navigation->count()
        );
    }

    public function testCanAddMenuItem(): void
    {
        $navigation = new Navigation();
        $item = $this->makeItem();

        $navigation->add(
            $item
        );

        $this->assertSame(
            $item,
            $navigation->get('characters')
        );
    }

    public function testHasReturnsTrueForExistingItem(): void
    {
        $navigation = new Navigation();

        $navigation->add(
            $this->makeItem()
        );

        $this->assertTrue(
            $navigation->has('characters')
        );
    }

    public function testHasReturnsFalseForMissingItem(): void
    {
        $navigation = new Navigation();

        $this->assertFalse(
            $navigation->has('missing')
        );
    }

    public function testGetReturnsNullForMissingItem(): void
    {
        $navigation = new Navigation();

        $this->assertNull(
            $navigation->get('missing')
        );
    }

    public function testItemsReturnsAllRegisteredItems(): void
    {
        $navigation = new Navigation();

        $characters = $this->makeItem(
            'characters',
            'Characters'
        );

        $inventory = $this->makeItem(
            'inventory',
            'Inventory'
        );

        $navigation->add(
            $characters
        );

        $navigation->add(
            $inventory
        );

        $this->assertSame(
            [
                'characters' => $characters,
                'inventory' => $inventory,
            ],
            $navigation->items()
        );
    }

    public function testAddingSameKeyReplacesExistingItem(): void
    {
        $navigation = new Navigation();

        $first = $this->makeItem(
            'characters',
            'Characters'
        );

        $replacement = $this->makeItem(
            'characters',
            'Heroes'
        );

        $navigation->add(
            $first
        );

        $navigation->add(
            $replacement
        );

        $this->assertSame(
            $replacement,
            $navigation->get('characters')
        );

        $this->assertSame(
            1,
            $navigation->count()
        );
    }

    public function testRemoveDeletesExistingItem(): void
    {
        $navigation = new Navigation();

        $navigation->add(
            $this->makeItem()
        );

        $navigation->remove(
            'characters'
        );

        $this->assertFalse(
            $navigation->has('characters')
        );
    }

    public function testRemoveMissingItemDoesNotCauseError(): void
    {
        $navigation = new Navigation();

        $navigation->remove(
            'missing'
        );

        $this->assertSame(
            0,
            $navigation->count()
        );
    }

    public function testClearRemovesAllItems(): void
    {
        $navigation = new Navigation();

        $navigation->add(
            $this->makeItem('characters')
        );

        $navigation->add(
            $this->makeItem('inventory')
        );

        $navigation->clear();

        $this->assertSame(
            [],
            $navigation->items()
        );

        $this->assertSame(
            0,
            $navigation->count()
        );
    }

    public function testCountReturnsNumberOfRegisteredItems(): void
    {
        $navigation = new Navigation();

        $navigation->add(
            $this->makeItem('characters')
        );

        $navigation->add(
            $this->makeItem('inventory')
        );

        $this->assertSame(
            2,
            $navigation->count()
        );
    }
}
