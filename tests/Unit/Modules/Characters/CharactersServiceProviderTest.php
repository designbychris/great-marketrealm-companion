<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\Characters\Actions\CreateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\DeleteCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\Actions\UpdateCharacterAction;
use GreatMarketrealmCompanion\Modules\Characters\CharactersServiceProvider;
use GreatMarketrealmCompanion\Modules\Characters\Contracts\CharacterRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\Characters\Rules\CharacterCreationRules;
use GreatMarketrealmCompanion\Modules\Characters\Services\CharacterFactory;
use PHPUnit\Framework\TestCase;

final class CharactersServiceProviderTest extends TestCase
{
    public function testRegistersCharacterCreationRules(): void
    {
        $container = $this->registeredContainer();

        self::assertInstanceOf(
            CharacterCreationRules::class,
            $container->make(
                CharacterCreationRules::class
            )
        );
    }

    public function testRegistersCharacterFactory(): void
    {
        $container = $this->registeredContainer();

        self::assertInstanceOf(
            CharacterFactory::class,
            $container->make(
                CharacterFactory::class
            )
        );
    }

    public function testCharacterFactoryIsASingleton(): void
    {
        $container = $this->registeredContainer();

        self::assertSame(
            $container->make(
                CharacterFactory::class
            ),
            $container->make(
                CharacterFactory::class
            )
        );
    }

    public function testRegistersConcreteCharacterRepository(): void
    {
        $container = $this->registeredContainer();

        self::assertInstanceOf(
            CharacterRepository::class,
            $container->make(
                CharacterRepository::class
            )
        );
    }

    public function testRepositoryInterfaceResolvesToConcreteRepository(): void
    {
        $container = $this->registeredContainer();

        $concrete = $container->make(
            CharacterRepository::class
        );

        $contract = $container->make(
            CharacterRepositoryInterface::class
        );

        self::assertSame(
            $concrete,
            $contract
        );
    }

    public function testRegistersCharacterActions(): void
    {
        $container = $this->registeredContainer();

        self::assertInstanceOf(
            CreateCharacterAction::class,
            $container->make(
                CreateCharacterAction::class
            )
        );

        self::assertInstanceOf(
            UpdateCharacterAction::class,
            $container->make(
                UpdateCharacterAction::class
            )
        );

        self::assertInstanceOf(
            DeleteCharacterAction::class,
            $container->make(
                DeleteCharacterAction::class
            )
        );
    }

    public function testRegistersCharacterController(): void
    {
        $container = $this->registeredContainer();

        self::assertTrue(
            $container->has(
                CharacterController::class
            )
        );
    }

    private function registeredContainer(): Container
    {
        $container = new Container();

        $application = $this->createMock(
            Application::class
        );

        $application
            ->method('container')
            ->willReturn($container);

        $provider = new CharactersServiceProvider(
            $application
        );

        $provider->register();

        return $container;
    }
}
