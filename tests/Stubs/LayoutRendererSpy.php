<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\View\LayoutRenderer;

final class LayoutRendererSpy extends LayoutRenderer
{
    /**
     * @var array<string,mixed>
     */
    public array $data = [];

    public function __construct()
    {
    }

    public function render(array $data): string
    {
        $this->data = $data;

        return '<layout>' . ($data['content'] ?? '') . '</layout>';
    }
}
