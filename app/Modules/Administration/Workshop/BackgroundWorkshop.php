<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\SkillProficiencies;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\ToolProficiency;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\HandbookBackgroundRegister;
use InvalidArgumentException;
use RuntimeException;

defined('ABSPATH') || exit;

/** Persistent Steward-authored backgrounds kept separate from protected Handbook canon. */
final class BackgroundWorkshop
{
    public const OPTION = 'gmrc_steward_backgrounds';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public function __construct(private ?HandbookBackgroundRegister $canonical = null)
    {
        $this->canonical ??= new HandbookBackgroundRegister();
    }

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        $records = \function_exists('get_option') ? \get_option(self::OPTION, []) : [];
        return is_array($records) ? $records : [];
    }

    /** @return array<string,array<string,mixed>> */
    public function published(): array
    {
        return array_filter($this->all(), static fn (array $record): bool => ($record['status'] ?? '') === self::STATUS_PUBLISHED);
    }

    /** @return array<string,mixed>|null */
    public function find(string $key): ?array
    {
        $records = $this->all();
        $key = sanitize_key($key);
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records = $this->all();
        $existing = $key !== '' ? ($records[sanitize_key($key)] ?? null) : null;
        $name = sanitize_text_field((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('A Steward background requires a name.');
        }

        $key = is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name, $records);
        $status = sanitize_key((string) ($input['status'] ?? self::STATUS_DRAFT));
        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED], true)) {
            $status = self::STATUS_DRAFT;
        }

        $skills = $this->skills($input['skills'] ?? []);
        $tools = $this->tools($input['tools'] ?? []);
        $featureName = sanitize_text_field((string) ($input['feature_name'] ?? ''));
        $featureDetail = sanitize_textarea_field((string) ($input['feature_detail'] ?? ''));

        if ($status === self::STATUS_PUBLISHED && ($featureName === '' || $featureDetail === '' || count($skills) !== 2 || count($tools) !== 1)) {
            throw new RuntimeException('Published Steward backgrounds require a feature, complete feature rules, exactly two recognised skills and one recognised tool proficiency.');
        }

        $records[$key] = [
            'key' => $key,
            'origin' => 'steward',
            'status' => $status,
            'name' => $name,
            'feature_name' => $featureName,
            'feature_detail' => $featureDetail,
            'skills' => $skills,
            'tools' => $tools,
            'tool_label' => count($tools) === 1 ? ToolProficiency::fromString($tools[0])->label() : '',
            'languages' => sanitize_text_field((string) ($input['languages'] ?? '')),
            'starting_equipment' => sanitize_textarea_field((string) ($input['starting_equipment'] ?? '')),
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
            'source_issues' => [],
            'updated_at' => gmdate('c'),
        ];

        update_option(self::OPTION, $records, false);
        return $key;
    }

    /** @return array<int,string> */
    private function skills(mixed $value): array
    {
        $skills = is_array($value) ? array_values(array_unique(array_map('sanitize_key', $value))) : [];
        if ($skills === []) {
            return [];
        }
        try {
            return SkillProficiencies::proficient($skills)->proficiencies();
        } catch (InvalidArgumentException) {
            throw new RuntimeException('Choose recognised Character skill proficiencies.');
        }
    }

    /** @return array<int,string> */
    private function tools(mixed $value): array
    {
        $tools = is_array($value) ? array_values(array_unique(array_map('sanitize_key', $value))) : [];
        foreach ($tools as $tool) {
            if (! ToolProficiency::supports($tool)) {
                throw new RuntimeException('Choose recognised Character tool proficiencies.');
            }
        }
        return $tools;
    }

    /** @param array<string,array<string,mixed>> $records */
    private function uniqueKey(string $name, array $records): string
    {
        $base = sanitize_key($name) ?: 'background';
        $key = 'steward-background-' . $base;
        $i = 2;
        while (isset($records[$key]) || $this->canonical->find($key) !== null) {
            $key = 'steward-background-' . $base . '-' . $i++;
        }
        return $key;
    }
}
