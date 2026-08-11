<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services\PortraitAssetCatalogue;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Repository-backed SVG portrait asset library.
 *
 * Supports the mapped Generation 1 library and the discoverable
 * Generation 2 catalogue.
 */
final class PortraitSvgAssetLibrary
{
    public function __construct(
        private readonly string $basePath,
        private readonly ?PortraitAssetCatalogue $generationTwo = null
    ) {
    }

    public function has(string $layerId): bool
    {
        return isset($this->generationOneAssets()[$layerId])
            || $this->generationTwo?->find($layerId)
                instanceof PortraitAssetDefinition;
    }

    public function symbolId(string $layerId): string
    {
        return 'gmrc-portrait-asset-' . sanitize_key($layerId);
    }

    public function useMarkup(
        string $layerId,
        string $classNames = ''
    ): string {
        $classAttribute = $classNames !== ''
            ? sprintf(
                ' class="%s"',
                esc_attr($classNames)
            )
            : '';

        return sprintf(
            '<use href="#%1$s" xlink:href="#%1$s"%3$s '
            . 'data-portrait-asset-id="%2$s"></use>',
            esc_attr($this->symbolId($layerId)),
            esc_attr($layerId),
            $classAttribute
        );
    }

    public function definitions(): string
    {
        $symbols = [];

        foreach ($this->generationOneAssets() as $layerId => $relativePath) {
            $symbols[] = $this->symbol(
                $layerId,
                $this->generationOnePath($relativePath)
            );
        }

        if ($this->generationTwo instanceof PortraitAssetCatalogue) {
            foreach ($this->generationTwo->all() as $asset) {
                $symbols[] = $this->symbol(
                    $asset->id(),
                    $asset->path()
                );
            }
        }

        return implode("\n", array_unique($symbols));
    }

    private function symbol(
        string $layerId,
        string $path
    ): string {
        return sprintf(
            '<symbol id="%s" viewBox="0 0 480 600">%s</symbol>',
            esc_attr($this->symbolId($layerId)),
            $this->innerMarkup($path, $layerId)
        );
    }

    /**
     * @return array<string,string>
     */
    private function generationOneAssets(): array
    {
        return array_merge(
            [
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

            'grocer-outfit-01' => 'Classes/Grocer/outfit.svg',
            'grocer-equipment-01' => 'Classes/Grocer/equipment.svg',
            'grocer-accessory-01' => 'Classes/Grocer/accessory.svg',
            'grocer-effects-01' => 'Classes/Grocer/effects.svg',
            'grocer-ornament-01' => 'Classes/Grocer/ornament.svg',

            'cleaver-saint-outfit-01' => 'Classes/CleaverSaint/outfit.svg',
            'cleaver-saint-equipment-01' => 'Classes/CleaverSaint/equipment.svg',
            'cleaver-saint-accessory-01' => 'Classes/CleaverSaint/accessory.svg',
            'cleaver-saint-effects-01' => 'Classes/CleaverSaint/effects.svg',
            'cleaver-saint-ornament-01' => 'Classes/CleaverSaint/ornament.svg',

            'effects-gold-motes-01' => 'Effects/gold-motes.svg',
            'frame-guild-gold-01' => 'Frames/guild-gold.svg',
            ],
            PortraitRaceAssetMap::assets(),
            PortraitClassAssetMap::assets()
        );
    }

    private function generationOnePath(
        string $relativePath
    ): string {
        return rtrim($this->basePath, '/\\')
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $relativePath
            );
    }

    private function innerMarkup(
        string $path,
        string $layerId
    ): string {
        $svg = is_readable($path)
            ? file_get_contents($path)
            : false;

        if (! is_string($svg) || $svg === '') {
            throw new RuntimeException(
                sprintf(
                    'Portrait SVG asset could not be read: %s',
                    $path
                )
            );
        }

        $svg = preg_replace(
            '/<title\b[^>]*>.*?<\/title>/si',
            '',
            $svg
        ) ?? $svg;

        $svg = preg_replace(
            '/^.*?<svg\b[^>]*>/si',
            '',
            $svg,
            1
        ) ?? $svg;

        $svg = preg_replace(
            '/<\/svg>\s*$/si',
            '',
            $svg,
            1
        ) ?? $svg;

        $prefix = $this->symbolId($layerId) . '-';

        preg_match_all(
            '/\bid="([A-Za-z][A-Za-z0-9_.:-]*)"/',
            $svg,
            $matches
        );

        foreach (array_unique($matches[1] ?? []) as $id) {
            $safeId = $prefix . sanitize_key($id);

            $svg = str_replace(
                'id="' . $id . '"',
                'id="' . $safeId . '"',
                $svg
            );

            $svg = str_replace(
                'url(#' . $id . ')',
                'url(#' . $safeId . ')',
                $svg
            );

            $svg = str_replace(
                'href="#' . $id . '"',
                'href="#' . $safeId . '"',
                $svg
            );

            $svg = str_replace(
                'xlink:href="#' . $id . '"',
                'xlink:href="#' . $safeId . '"',
                $svg
            );
        }

        return trim($svg);
    }
}
