<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\View;

use GreatMarketrealmCompanion\Core\View\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testViewCanBeCreated(): void
    {
        $view = View::make(
            'dashboard.index'
        );

        $this->assertInstanceOf(
            View::class,
            $view
        );
    }

    public function testViewStoresItsName(): void
    {
        $view = View::make(
            'characters.index'
        );

        $this->assertSame(
            'characters.index',
            $view->name()
        );
    }

    public function testViewDefaultsToEmptyData(): void
    {
        $view = View::make(
            'dashboard.index'
        );

        $this->assertSame(
            [],
            $view->data()
        );
    }

    public function testViewStoresData(): void
    {
        $view = View::make(
            'characters.show',
            [
                'name' => 'Auby',
                'level' => 5,
            ]
        );

        $this->assertSame(
            [
                'name' => 'Auby',
                'level' => 5,
            ],
            $view->data()
        );
    }

    public function testViewPreservesNestedData(): void
    {
        $view = View::make(
            'characters.show',
            [
                'character' => [
                    'name' => 'Auby',
                    'attributes' => [
                        'strength' => 12,
                        'wisdom' => 16,
                    ],
                ],
            ]
        );

        $this->assertSame(
            [
                'character' => [
                    'name' => 'Auby',
                    'attributes' => [
                        'strength' => 12,
                        'wisdom' => 16,
                    ],
                ],
            ],
            $view->data()
        );
    }

    public function testViewPreservesObjectData(): void
    {
        $character = new ViewTestCharacter(
            'Auby'
        );

        $view = View::make(
            'characters.show',
            [
                'character' => $character,
            ]
        );

        $this->assertSame(
            $character,
            $view->data()['character']
        );
    }

    public function testEachMakeCallCreatesANewViewInstance(): void
    {
        $first = View::make(
            'dashboard.index'
        );

        $second = View::make(
            'dashboard.index'
        );

        $this->assertNotSame(
            $first,
            $second
        );
    }

    public function testViewNameAndDataRemainIndependentBetweenInstances(): void
    {
        $first = View::make(
            'characters.index',
            [
                'title' => 'Characters',
            ]
        );

        $second = View::make(
            'dashboard.index',
            [
                'title' => 'Dashboard',
            ]
        );

        $this->assertSame(
            'characters.index',
            $first->name()
        );

        $this->assertSame(
            [
                'title' => 'Characters',
            ],
            $first->data()
        );

        $this->assertSame(
            'dashboard.index',
            $second->name()
        );

        $this->assertSame(
            [
                'title' => 'Dashboard',
            ],
            $second->data()
        );
    }
}

final class ViewTestCharacter
{
    public function __construct(
        public readonly string $name
    ) {
    }
}
