<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\Contracts;

use GreatMarketrealmCompanion\Modules\Parties\Contracts\PartyRepositoryInterface;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

final class PartyRepositoryContractTest extends TestCase
{
    public function testRepositoryContractKeepsOwnershipScopedReads(): void
    {
        $reflection = new ReflectionClass(
            PartyRepositoryInterface::class
        );

        self::assertTrue(
            $reflection->hasMethod(
                'allForOwner'
            )
        );

        self::assertTrue(
            $reflection->hasMethod(
                'findForOwner'
            )
        );

        self::assertTrue(
            $reflection->hasMethod(
                'save'
            )
        );

        self::assertTrue(
            $reflection->hasMethod(
                'delete'
            )
        );
    }
}
