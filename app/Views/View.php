<?php

namespace GreatMarketrealmCompanion\View;

defined('ABSPATH') || exit;

/**
 * Class View
 *
 * Responsible for rendering application views and layouts.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.1
 */
class View
{
    /**
     * Base path to the views directory.
     *
     * @var string
     */
    protected static string $basePath = GMRC_PLUGIN_DIR . 'resources/views/';

    /**
     * Render a view.
     *
     * Example:
     *
     * View::render('dashboard.index', [
     *     'characters' => $characters,
     * ]);
     *
     * @param string $view
     * @param array  $data
     * @param string $layout
     *
     * @return string
     */
    public static function render(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): string {

        $viewFile = self::resolveView($view);
        $layoutFile = self::resolveLayout($layout);

        if (! file_exists($viewFile)) {
            throw new \RuntimeException(
                sprintf('View [%s] could not be found.', $view)
            );
        }

        if (! file_exists($layoutFile)) {
            throw new \RuntimeException(
                sprintf('Layout [%s] could not be found.', $layout)
            );
        }

        protected static function extractData(array $data): void
        {
            extract($data, EXTR_SKIP);
        }

        ob_start();

        include $viewFile;

        $content = ob_get_clean();

        ob_start();

        include $layoutFile;

        return ob_get_clean();
    }

    /**
     * Render a component.
     *
     * @param string $component
     * @param array  $data
     *
     * @return void
     */
    public static function component(
        string $component,
        array $data = []
    ): void {

        $componentFile = self::$basePath . 'components/' .
            str_replace('.', '/', $component) .
            '.php';

        if (! file_exists($componentFile)) {
            throw new \RuntimeException(
                sprintf('Component [%s] could not be found.', $component)
            );
        }

        protected static function extractData(array $data): void
        {
            extract($data, EXTR_SKIP);
        }

        include $componentFile;
    }

    /**
     * Resolve a view path.
     *
     * @param string $view
     *
     * @return string
     */
    protected static function resolveView(string $view): string
    {
        return self::$basePath .
            str_replace('.', '/', $view) .
            '.php';
    }

    /**
     * Resolve a layout path.
     *
     * @param string $layout
     *
     * @return string
     */
    protected static function resolveLayout(string $layout): string
    {
        return self::$basePath .
            'layouts/' .
            $layout .
            '.php';
    }
}
