<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\View;

defined('ABSPATH') || exit;

/**
 * View Finder.
 *
 * Locates module views and shared application views.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class ViewFinder
{
    /**
     * Base application path.
     */
    protected string $basePath;

    /**
     * Create the view finder.
     */
    public function __construct(
        ?string $basePath = null
    ) {
        $this->basePath = rtrim(
            $basePath ?? GMRC_PATH,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR;
    }

    /**
     * Find a view.
     *
     * Module view example:
     *
     * characters.index
     *
     * Resolves to:
     *
     * app/Modules/Characters/Views/index.php
     *
     * Shared view example:
     *
     * components.furniture.auby-note
     *
     * Resolves to:
     *
     * app/Views/components/furniture/auby-note.php
     *
     * @throws \RuntimeException
     */
    public function find(string $view): string
    {
        $sharedPath = $this->sharedViewPath($view);

        if (file_exists($sharedPath)) {
            return $sharedPath;
        }

        $modulePath = $this->moduleViewPath($view);

        if (
            $modulePath !== null
            && file_exists($modulePath)
        ) {
            return $modulePath;
        }

        throw new \RuntimeException(
            sprintf(
                'View [%s] not found. Checked [%s]%s.',
                $view,
                $sharedPath,
                $modulePath !== null
                    ? sprintf(
                        ' and [%s]',
                        $modulePath
                    )
                    : ''
            )
        );
    }

    /**
     * Build a shared application view path.
     */
    protected function sharedViewPath(
        string $view
    ): string {
        return sprintf(
            '%sapp/Views/%s.php',
            $this->basePath,
            str_replace(
                '.',
                DIRECTORY_SEPARATOR,
                $view
            )
        );
    }

    /**
     * Build a module view path.
     */
    protected function moduleViewPath(
        string $view
    ): ?string {
        if (! str_contains($view, '.')) {
            return null;
        }

        [$module, $template] = explode(
            '.',
            $view,
            2
        );

        return sprintf(
            '%sapp/Modules/%s/Views/%s.php',
            $this->basePath,
            ucfirst($module),
            str_replace(
                '.',
                DIRECTORY_SEPARATOR,
                $template
            )
        );
    }

    /**
     * Return the configured base path.
     */
    public function basePath(): string
    {
        return $this->basePath;
    }
}
