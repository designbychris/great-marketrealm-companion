<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Parties\BridgeSeal;

use GreatMarketrealmCompanion\Core\Http\Validation\ValidatedInput;
use PHPUnit\Framework\TestCase;

final class TreasuryValidatedInputContractRegressionTest extends TestCase
{
    public function testValidatedInputExposesIntegerAccessor(): void
    {
        $validated = new ValidatedInput([
            'gold' => '12',
        ]);

        self::assertSame(
            12,
            $validated->integer('gold')
        );
    }

    public function testTreasuryRequestsUseSupportedIntegerAccessor(): void
    {
        foreach ([
            'DepositPartyTreasuryRequest.php',
            'WithdrawPartyTreasuryRequest.php',
            'TransferPartyCoinRequest.php',
        ] as $request) {
            $source = file_get_contents(
                $this->root()
                . '/app/Modules/Parties/Requests/'
                . $request
            );

            self::assertIsString($source);

            self::assertStringContainsString(
                "->integer('gold')",
                $source
            );

            self::assertStringContainsString(
                "->integer('silver')",
                $source
            );

            self::assertStringContainsString(
                "->integer('copper')",
                $source
            );

            self::assertStringNotContainsString(
                '->int(',
                $source
            );
        }
    }

    public function testValidatedInputDoesNotClaimLegacyIntAccessor(): void
    {
        $source = file_get_contents(
            $this->root()
            . '/app/Core/Http/Validation/'
            . 'ValidatedInput.php'
        );

        self::assertIsString($source);

        self::assertStringContainsString(
            'public function integer(',
            $source
        );

        self::assertStringNotContainsString(
            'public function int(',
            $source
        );
    }

    private function root(): string
    {
        return dirname(__DIR__, 5);
    }
}
