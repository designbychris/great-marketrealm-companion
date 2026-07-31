<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Core\View;

use GreatMarketrealmCompanion\Core\View\LayoutRenderer;
use PHPUnit\Framework\TestCase;

final class LayoutRendererTest extends TestCase
{
    public function testReturnsMessageWhenLayoutMissing(): void
    {
        $renderer = new LayoutRenderer(
            '/does/not/exist.php'
        );

        self::assertSame(
            '<p>Layout not found.</p>',
            $renderer->render([])
        );
    }

    public function testReturnsInjectedLayoutPath(): void
    {
        $renderer = new LayoutRenderer(
            '/tmp/layout.php'
        );

        self::assertSame(
            '/tmp/layout.php',
            $renderer->layout()
        );
    }
}
