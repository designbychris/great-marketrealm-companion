<?php

namespace GreatMarketrealmCompanion\Core\View;

defined('ABSPATH') || exit;

/**
 * View Finder.
 *
 * Locates application view files.
 *
 * @package MarketrealmCompanion
 * @since 0.3.0
 */
class ViewFinder
{
    /**
     * Find a view.
     *
     * @throws \RuntimeException
     */
    public function find(
        string $view
    ): string {

        [$module, $template] = explode(
            '.',
            $view,
            2
        );

        $path = sprintf(
            '%sapp/Modules/%s/Views/%s.php',
            GMRC_PATH,
            ucfirst($module),
            $template
        );

        if (! file_exists($path)) {
            throw new \RuntimeException(
                sprintf(
                    'View [%s] not found.',
                    $view
                )
            );
        }

        return $path;
    }
}
