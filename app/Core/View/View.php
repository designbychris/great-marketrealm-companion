<?php

namespace GreatMarketrealmCompanion\Core\View;

defined('ABSPATH') || exit;

/**
 * View.
 *
 * Represents a renderable application view.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class View
{
    /**
     * View name.
     */
    protected string $name;

    /**
     * View data.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Constructor.
     */
    protected function __construct(
        string $name,
        array $data = []
    ) {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Create a new view instance.
     */
    public static function make(
        string $name,
        array $data = []
    ): self {
        return new self(
            $name,
            $data
        );
    }

    /**
     * Return the view name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return the view data.
     */
    public function data(): array
    {
        return $this->data;
    }
}
