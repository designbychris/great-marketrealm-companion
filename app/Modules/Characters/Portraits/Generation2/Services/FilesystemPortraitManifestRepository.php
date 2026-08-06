<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestRepositoryInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts\PortraitManifestValidatorInterface;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitAssetDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

defined('ABSPATH') || exit;

final class FilesystemPortraitManifestRepository implements PortraitManifestRepositoryInterface
{
    /**
     * @var array<int,PortraitManifest>|null
     */
    private ?array $manifests = null;

    public function __construct(
        private string $libraryPath,
        private PortraitManifestValidatorInterface $validator
    ) {
    }

    public function all(): array
    {
        if ($this->manifests !== null) {
            return $this->manifests;
        }

        $manifests = [];

        foreach ($this->manifestFiles() as $file) {
            $manifest = $this->load($file);

            if ($manifest !== null) {
                $manifests[] = $manifest;
            }
        }

        return $this->manifests = $manifests;
    }

    public function find(string $manifestId): ?PortraitManifest
    {
        foreach ($this->all() as $manifest) {
            if ($manifest->id() === $manifestId) {
                return $manifest;
            }
        }

        return null;
    }

    /**
     * @return array<int,string>
     */
    private function manifestFiles(): array
    {
        if (! is_dir($this->libraryPath)) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->libraryPath,
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        $files = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'manifest.json') {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    private function load(string $file): ?PortraitManifest
    {
        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException(
                sprintf('Unable to read portrait manifest "%s".', $file)
            );
        }

        $data = json_decode($json, true);

        if (! is_array($data)) {
            return null;
        }

        $directory = dirname($file);

        if ($this->validator->validate($data, $directory) !== []) {
            return null;
        }

        $assets = array_map(
            static fn (array $asset): PortraitAssetDefinition =>
                new PortraitAssetDefinition(
                    (string) $asset['id'],
                    (string) $asset['slot'],
                    $directory . '/' . (string) $asset['file'],
                    (string) $asset['label'],
                    (string) ($asset['variant'] ?? ''),
                    is_array($asset['tags'] ?? null)
                        ? array_values($asset['tags'])
                        : [],
                    is_array($asset['requires'] ?? null)
                        ? array_values($asset['requires'])
                        : [],
                    is_array($asset['excludes'] ?? null)
                        ? array_values($asset['excludes'])
                        : [],
                    (bool) ($asset['animated'] ?? false)
                ),
            $data['assets']
        );

        return new PortraitManifest(
            (string) $data['id'],
            (string) $data['type'],
            (string) $data['label'],
            $directory,
            is_array($data['defaults'] ?? null)
                ? $data['defaults']
                : [],
            is_array($data['compatible_with'] ?? null)
                ? array_values($data['compatible_with'])
                : [],
            $assets
        );
    }
}
