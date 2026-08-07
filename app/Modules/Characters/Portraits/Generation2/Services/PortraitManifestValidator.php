<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestValidatorInterface;

defined('ABSPATH') || exit;

final class PortraitManifestValidator implements PortraitManifestValidatorInterface
{
    private const TYPES = ['shared', 'race', 'class', 'collection'];

    private const SLOTS = [
        'background',
        'ground_shadow',
        'body_base',
        'body_shadow',
        'body_highlight',
        'heritage',
        'eyes',
        'mouth',
        'face_overlay',
        'outfit_base',
        'outfit_shadow',
        'outfit_highlight',
        'equipment',
        'accessory',
        'class_effects',
        'ambient_effects',
        'guild_ornament',
        'frame',
    ];

    public function validate(array $manifest, string $directory): array
    {
        $errors = [];

        foreach (['schema_version', 'id', 'type', 'label', 'assets'] as $key) {
            if (! array_key_exists($key, $manifest)) {
                $errors[] = sprintf('Missing required key "%s".', $key);
            }
        }

        if (($manifest['schema_version'] ?? '') !== '2.0') {
            $errors[] = 'Unsupported schema version.';
        }

        if (! in_array($manifest['type'] ?? '', self::TYPES, true)) {
            $errors[] = 'Manifest type is invalid.';
        }

        if (! is_array($manifest['assets'] ?? null)) {
            return array_merge($errors, ['Assets must be an array.']);
        }

        $ids = [];

        foreach ($manifest['assets'] as $index => $asset) {
            if (! is_array($asset)) {
                $errors[] = sprintf('Asset %d must be an object.', $index);
                continue;
            }

            foreach (['id', 'slot', 'file', 'label'] as $key) {
                if (! is_string($asset[$key] ?? null) || $asset[$key] === '') {
                    $errors[] = sprintf(
                        'Asset %d requires a non-empty "%s".',
                        $index,
                        $key
                    );
                }
            }

            $id = (string) ($asset['id'] ?? '');

            if ($id !== '' && ! str_starts_with($id, 'g2-')) {
                $errors[] = sprintf('Asset "%s" must begin with g2-.', $id);
            }

            if ($id !== '' && in_array($id, $ids, true)) {
                $errors[] = sprintf('Duplicate asset ID "%s".', $id);
            }

            $ids[] = $id;

            if (! in_array($asset['slot'] ?? '', self::SLOTS, true)) {
                $errors[] = sprintf('Asset "%s" has an invalid slot.', $id);
            }

            $file = (string) ($asset['file'] ?? '');

            if (
                $file !== ''
                && (
                    str_starts_with($file, '/')
                    || str_contains($file, '..')
                    || strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'svg'
                )
            ) {
                $errors[] = sprintf('Asset "%s" has an unsafe file path.', $id);
            }

            if ($file !== '' && ! is_file($directory . '/' . $file)) {
                $errors[] = sprintf(
                    'Asset "%s" references a missing SVG file.',
                    $id
                );
            }
        }

        return $errors;
    }
}
