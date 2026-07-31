<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Core\Http;

use GreatMarketrealmCompanion\Core\Http\RouteResolver;
use PHPUnit\Framework\TestCase;

final class RouteResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
    }

    public function testDefaultsToDashboard(): void
    {
        $resolver = new RouteResolver();

        self::assertSame(
            'dashboard',
            $resolver->current()
        );
    }

    public function testReadsGetRoute(): void
    {
        $_GET['gmrc_route'] = 'characters';

        $resolver = new RouteResolver();

        self::assertSame(
            'characters',
            $resolver->current()
        );
    }

    public function testReadsPostRoute(): void
    {
        $_POST['gmrc_route'] = 'dashboard';

        $resolver = new RouteResolver();

        self::assertSame(
            'dashboard',
            $resolver->current()
        );
    }

    public function testPostOverridesGet(): void
    {
        $_GET['gmrc_route'] = 'dashboard';
        $_POST['gmrc_route'] = 'characters';

        $resolver = new RouteResolver();

        self::assertSame(
            'characters',
            $resolver->current()
        );
    }

    public function testTrimsSlashes(): void
    {
        $_GET['gmrc_route'] = '/characters/';

        $resolver = new RouteResolver();

        self::assertSame(
            'characters',
            $resolver->current()
        );
    }

    public function testEmptyRouteFallsBackToDashboard(): void
    {
        $_GET['gmrc_route'] = '';

        $resolver = new RouteResolver();

        self::assertSame(
            'dashboard',
            $resolver->current()
        );
    }

    public function testRemovesInvalidCharacters(): void
    {
        $_GET['gmrc_route'] = '../../characters<script>';

        $resolver = new RouteResolver();

        self::assertSame(
            'charactersscript',
            $resolver->current()
        );
    }
}
