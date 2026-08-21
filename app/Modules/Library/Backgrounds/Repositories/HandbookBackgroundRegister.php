<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories;

use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Models\BackgroundRecord;

defined('ABSPATH') || exit;

final class HandbookBackgroundRegister
{
    private ?array $records = null;

    public function all(): array
    {
        return array_values($this->records());
    }

    public function find(string $key): ?BackgroundRecord
    {
        return $this->records()[sanitize_key($key)] ?? null;
    }

    public function supports(string $key): bool
    {
        return $this->find($key) instanceof BackgroundRecord;
    }

    private function records(): array
    {
        if ($this->records !== null) {
            return $this->records;
        }

        $source = require dirname(__DIR__) . '/Data/handbook-backgrounds.php';
        $records = [];

        foreach ($source as $entry) {
            $record = new BackgroundRecord(
                (string) $entry['key'],
                (string) $entry['name'],
                (string) $entry['feature_name'],
                (string) $entry['feature_detail'],
                is_array($entry['skills'] ?? null) ? $entry['skills'] : [],
                is_array($entry['tools'] ?? null) ? $entry['tools'] : [],
                (string) ($entry['tool_label'] ?? ''),
                is_array($entry['source_issues'] ?? null) ? $entry['source_issues'] : []
            );
            $records[$record->key()] = $record;
        }

        return $this->records = $records;
    }
}
