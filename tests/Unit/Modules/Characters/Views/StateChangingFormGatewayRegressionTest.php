<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Views;

use PHPUnit\Framework\TestCase;

final class StateChangingFormGatewayRegressionTest extends TestCase
{
    public function testInventoryFormsUseApplicationRequestGateway(): void
    {
        $root = dirname(__DIR__, 5);

        $view = file_get_contents(
            $root
            . '/app/Modules/Characters/Views/show.php'
        );

        self::assertIsString($view);

        self::assertStringContainsString(
            "admin_url(\n    'admin-post.php'",
            $view
        );

        self::assertGreaterThanOrEqual(
            4,
            substr_count(
                $view,
                'name="action" value="gmrc_app_request"'
            )
        );

        self::assertGreaterThanOrEqual(
            4,
            substr_count(
                $view,
                'name="gmrc_route"'
            )
        );

        self::assertStringNotContainsString(
            'action="<?php echo esc_url($inventoryUrl); ?>"',
            $view
        );

        self::assertStringNotContainsString(
            'action="<?php echo esc_url($itemUrl); ?>"',
            $view
        );
    }

    public function testPortraitFormsUseApplicationRequestGateway(): void
    {
        $root = dirname(__DIR__, 5);

        $component = file_get_contents(
            $root
            . '/app/Views/components/media/'
            . 'illuminator-workbench.php'
        );

        self::assertIsString($component);

        self::assertStringContainsString(
            "admin_url(\n    'admin-post.php'",
            $component
        );

        self::assertSame(
            2,
            substr_count(
                $component,
                'name="action"'
            )
        );

        self::assertSame(
            2,
            substr_count(
                $component,
                'value="gmrc_app_request"'
            )
        );

        self::assertSame(
            2,
            substr_count(
                $component,
                'name="gmrc_route"'
            )
        );
    }
}
