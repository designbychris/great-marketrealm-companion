<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\View\LayoutRenderer;

final class LayoutRendererSpy extends LayoutRenderer
{
    /**
     * @var array<string,mixed>
     */
    private array $data = [];

    public function __construct()
    {
    }

    public function render(array $data): string
    {
        $this->data = $data;

        return '<layout>' . ($data['content'] ?? '') . '</layout>';
    }
    
    /**
     * Return the last layout data.
     *
     * @return array<string,mixed>
     */
    public function lastData(): array
    {
        return $this->data;
    }
}
