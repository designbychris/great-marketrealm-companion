<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Navigation;

use GreatMarketrealmCompanion\Navigation\MenuItem;
use PHPUnit\Framework\TestCase;

final class MenuItemTest extends TestCase
{
    public function testMenuItemCanBeCreated(): void
    {
        $item = new MenuItem(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertInstanceOf(
            MenuItem::class,
            $item
        );
    }

    public function testMakeCreatesMenuItem(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertInstanceOf(
            MenuItem::class,
            $item
        );
    }

    public function testKeyReturnsExpectedValue(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertSame(
            'characters',
            $item->key()
        );
    }

    public function testLabelReturnsExpectedValue(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertSame(
            'Characters',
            $item->label()
        );
    }

    public function testIconReturnsExpectedValue(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertSame(
            'users',
            $item->icon()
        );
    }

    public function testRouteReturnsExpectedValue(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertSame(
            '/characters',
            $item->route()
        );
    }

    public function testSortOrderDefaultsToOneHundred(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertSame(
            100,
            $item->sortOrder()
        );
    }

    public function testSortOrderCanBeSet(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters',
            20
        );

        $this->assertSame(
            20,
            $item->sortOrder()
        );
    }

    public function testParentDefaultsToNull(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertNull(
            $item->parent()
        );
    }

    public function testParentCanBeSet(): void
    {
        $item = MenuItem::make(
            'inventory',
            'Inventory',
            'box',
            '/inventory',
            30,
            'characters'
        );

        $this->assertSame(
            'characters',
            $item->parent()
        );
    }

    public function testHasParentReturnsFalseWhenParentIsNull(): void
    {
        $item = MenuItem::make(
            'characters',
            'Characters',
            'users',
            '/characters'
        );

        $this->assertFalse(
            $item->hasParent()
        );
    }

    public function testHasParentReturnsTrueWhenParentIsSet(): void
    {
        $item = MenuItem::make(
            'inventory',
            'Inventory',
            'box',
            '/inventory',
            30,
            'characters'
        );

        $this->assertTrue(
            $item->hasParent()
        );
    }
}
