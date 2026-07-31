<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\View;

use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Core\View\ViewFinder;
use GreatMarketrealmCompanion\Tests\Stubs\SessionStoreStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ViewFactoryTest extends TestCase
{
    private string $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtures = realpath(
            __DIR__ . '/../../Fixtures/Views'
        ) . DIRECTORY_SEPARATOR;
    }

    private function makeFactory(): ViewFactory
    {
        $session = new SessionStoreStub();

        $flash = new FlashStore(
            $session
        );

        return new ViewFactory(
            new ViewFinder(
                $this->fixtures
            ),
            $flash
        );
    }

    private function makeFlash(): FlashStore
    {
        return new FlashStore(
            new SessionStoreStub()
        );
    }

    public function testFactoryCanBeCreated(): void
    {
        $this->assertInstanceOf(
            ViewFactory::class,
            $this->makeFactory()
        );
    }

    public function testRendersSharedView(): void
    {
        $html = $this->makeFactory()->render(
            View::make(
                'welcome'
            )
        );

        $this->assertSame(
            'Welcome Guest',
            trim($html)
        );
    }

    public function testRendersSharedViewWithData(): void
    {
        $html = $this->makeFactory()->render(
            View::make(
                'welcome',
                [
                    'name' => 'Auby',
                ]
            )
        );

        $this->assertSame(
            'Welcome Auby',
            trim($html)
        );
    }

    public function testRendersModuleView(): void
    {
        $html = $this->makeFactory()->render(
            View::make(
                'characters.show',
                [
                    'name' => 'Auby',
                ]
            )
        );

        $this->assertSame(
            'Character: Auby',
            trim($html)
        );
    }

    public function testComponentRendering(): void
    {
        $html = $this->makeFactory()->component(
            'components.furniture.auby-note',
            [
                'quote' => 'Fresh today!',
            ]
        );

        $this->assertSame(
            'Quote: Fresh today!',
            trim($html)
        );
    }

    public function testMissingViewThrowsException(): void
    {
        $this->expectException(
            RuntimeException::class
        );

        $this->makeFactory()->render(
            View::make(
                'missing'
            )
        );
    }

    public function testOldInputIsShared(): void
    {
        $flash = $this->makeFlash();

        $flash->flashOldInput([
            'name' => 'Auby',
        ]);

        $flash->age();

        $factory = new ViewFactory(
            new ViewFinder(
                $this->fixtures
            ),
            $flash
        );

        $html = $factory->render(
            View::make('shared')
        );

        $this->assertStringContainsString(
            'Old Name: Auby',
            $html
        );
    }

    public function testSuccessMessageIsShared(): void
    {
        $flash = $this->makeFlash();

        $flash->success(
            'Saved!'
        );

        $flash->age();

        $factory = new ViewFactory(
            new ViewFinder(
                $this->fixtures
            ),
            $flash
        );

        $html = $factory->render(
            View::make('shared')
        );

        $this->assertStringContainsString(
            'Success: Saved!',
            $html
        );
    }

    public function testErrorMessageIsShared(): void
    {
        $flash = $this->makeFlash();

        $flash->error(
            'Failed!'
        );

        $flash->age();

        $factory = new ViewFactory(
            new ViewFinder(
                $this->fixtures
            ),
            $flash
        );

        $html = $factory->render(
            View::make('shared')
        );

        $this->assertStringContainsString(
            'Error: Failed!',
            $html
        );
    }

    public function testErrorsAreShared(): void
    {
        $flash = $this->makeFlash();

        $flash->flashErrors([
            'name' => 'Required',
        ]);

        $flash->age();

        $factory = new ViewFactory(
            new ViewFinder(
                $this->fixtures
            ),
            $flash
        );

        $html = $factory->render(
            View::make('shared')
        );

        $this->assertStringContainsString(
            'name',
            $html
        );
    }

    public function testConsecutiveRendersRemainIndependent(): void
    {
        $factory = $this->makeFactory();

        $first = $factory->render(
            View::make(
                'welcome',
                ['name' => 'Auby']
            )
        );

        $second = $factory->render(
            View::make('welcome')
        );

        $this->assertSame(
            'Welcome Auby',
            trim($first)
        );

        $this->assertSame(
            'Welcome Guest',
            trim($second)
        );
    }
}
