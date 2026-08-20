<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Ranger;

use PHPUnit\Framework\TestCase;

final class RangerFieldArtsDiceworksRegressionTest extends TestCase
{
    public function testRangerChoiceFormulaUsesSharedDiceworksFormulaContract(): void
    {
        $source = $this->showSource();

        self::assertMatchesRegularExpression(
            '/data-guild-roll="damage"\s+'
            . 'data-roll-kind="damage"\s+'
            . 'data-roll-formula="<\?php echo esc_attr\(\s*'
            . '\(string\) \(\s*'
            . '\$choice\\[\'formula\'\\]/s',
            $source
        );
    }

    public function testRangerDirectRollUsesSharedDiceworksFormulaContract(): void
    {
        $source = $this->showSource();

        self::assertMatchesRegularExpression(
            '/data-guild-roll="damage"\s+'
            . 'data-roll-kind="damage"\s+'
            . 'data-roll-formula="<\?php echo esc_attr\(\s*'
            . '\(string\) \(\s*'
            . '\$roll\\[\'formula\'\\]/s',
            $source
        );
    }

    public function testRangerFieldArtsDoNotPutDiceFormulaInGuildRollAttribute(): void
    {
        $source = $this->rangerFieldArtsSource();

        self::assertStringNotContainsString(
            'data-guild-roll="<?php echo esc_attr(',
            $source
        );
    }

    private function rangerFieldArtsSource(): string
    {
        $source = $this->showSource();

        $start = strpos(
            $source,
            'data-ranger-field-arts'
        );

        self::assertNotFalse($start);

        $end = strpos(
            $source,
            'gmrc-ranger-field-arts__note',
            $start
        );

        self::assertNotFalse($end);

        return substr(
            $source,
            $start,
            $end - $start
        );
    }

    private function showSource(): string
    {
        $source = file_get_contents(
            dirname(__DIR__, 6)
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($source);

        return $source;
    }
}
