<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\View;

defined('ABSPATH') || exit;

/**
 * Renders the Companion application layout.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
final class LayoutRenderer
{
    /**
     * Layout file.
     */
    private string $layout;

    public function __construct(
        ?string $layout = null
    ) {
        $this->layout = $layout
            ?? GMRC_PATH .
            'app/Core/View/Templates/layouts/app.php';
    }

    /**
     * Render the application layout.
     *
     * @param array<string,mixed> $data
     */
    public function render(
        array $data
    ): string {

        if (! file_exists($this->layout)) {
            return '<p>Layout not found.</p>';
        }

        extract(
            $data,
            EXTR_SKIP
        );

        ob_start();

        require $this->layout;

        return (string) ob_get_clean();
    }

    /**
     * Return the layout path.
     */
    public function layout(): string
    {
        return $this->layout;
    }
}
