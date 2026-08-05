<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Repository-backed SVG portrait asset library.
 */
final class PortraitSvgAssetLibrary
{
    public function __construct(
        private readonly string $basePath
    ) {
    }

    public function has(string $layerId): bool
    {
        return isset($this->assets()[$layerId]);
    }

    public function symbolId(string $layerId): string
    {
        return 'gmrc-portrait-asset-' . sanitize_key($layerId);
    }

    public function definitions(): string
    {
        $symbols = [];

        foreach ($this->assets() as $layerId => $relativePath) {
            $symbols[] = sprintf(
                '<symbol id="%s" viewBox="0 0 480 600">%s</symbol>',
                esc_attr($this->symbolId($layerId)),
                $this->innerMarkup($relativePath, $layerId)
            );
        }

        return implode("\n", $symbols);
    }

    /**
     * @return array<string,string>
     */
    private function assets(): array
    {
        return [
            'background-parchment-01' => 'Backgrounds/parchment.svg',
            'background-market-arch-01' => 'Backgrounds/market-arch.svg',
            'background-guild-hall-01' => 'Backgrounds/guild-hall.svg',
            'fructan-body-01' => 'Bodies/Fructan/body.svg',
            'rootkin-body-01' => 'Bodies/Rootkin/body.svg',
            'fungifolk-body-01' => 'Bodies/Fungifolk/body.svg',
            'eyes-round-01' => 'Faces/Eyes/round.svg',
            'eyes-bright-01' => 'Faces/Eyes/bright.svg',
            'eyes-determined-01' => 'Faces/Eyes/determined.svg',
            'mouth-neutral-01' => 'Faces/Mouths/neutral.svg',
            'mouth-smile-01' => 'Faces/Mouths/smile.svg',
            'mouth-grin-01' => 'Faces/Mouths/grin.svg',
            'fighter-outfit-01' => 'Classes/Fighter/outfit.svg',
            'fighter-equipment-01' => 'Classes/Fighter/equipment.svg',
            'wizard-outfit-01' => 'Classes/Wizard/outfit.svg',
            'wizard-equipment-01' => 'Classes/Wizard/equipment.svg',
            'rogue-outfit-01' => 'Classes/Rogue/outfit.svg',
            'rogue-equipment-01' => 'Classes/Rogue/equipment.svg',
            'effects-gold-motes-01' => 'Effects/gold-motes.svg',
            'frame-guild-gold-01' => 'Frames/guild-gold.svg',
        ];
    }

    private function innerMarkup(
        string $relativePath,
        string $layerId
    ): string
    {
        $path = rtrim($this->basePath, '/\\')
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        $svg = is_readable($path) ? file_get_contents($path) : false;

        if (! is_string($svg) || $svg === '') {
            throw new RuntimeException(
                sprintf('Portrait SVG asset could not be read: %s', $relativePath)
            );
        }

        $svg = preg_replace('/<title\b[^>]*>.*?<\/title>/si', '', $svg) ?? $svg;
        $svg = preg_replace('/^.*?<svg\b[^>]*>/si', '', $svg, 1) ?? $svg;
        $svg = preg_replace('/<\/svg>\s*$/si', '', $svg, 1) ?? $svg;

        $prefix = $this->symbolId($layerId) . '-';
        preg_match_all('/\bid="([A-Za-z][A-Za-z0-9_.:-]*)"/', $svg, $matches);

        foreach (array_unique($matches[1] ?? []) as $id) {
            $safeId = $prefix . sanitize_key($id);
            $svg = str_replace('id="' . $id . '"', 'id="' . $safeId . '"', $svg);
            $svg = str_replace('url(#' . $id . ')', 'url(#' . $safeId . ')', $svg);
            $svg = str_replace('href="#' . $id . '"', 'href="#' . $safeId . '"', $svg);
            $svg = str_replace('xlink:href="#' . $id . '"', 'xlink:href="#' . $safeId . '"', $svg);
        }

        return trim($svg);
    }
}
