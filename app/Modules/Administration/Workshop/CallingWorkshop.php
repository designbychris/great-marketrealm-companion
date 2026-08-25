<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use RuntimeException;

defined('ABSPATH') || exit;

/** Steward-authored playable Callings and their Calling Paths. */
final class CallingWorkshop
{
    public const OPTION = 'gmrc_steward_callings';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';
    private const ABILITIES = ['strength','dexterity','constitution','intelligence','wisdom','charisma'];
    private const HIT_DICE = [6,8,10,12];

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        $value = function_exists('get_option') ? get_option(self::OPTION, []) : [];
        return is_array($value) ? $value : [];
    }

    /** @return array<string,array<string,mixed>> */
    public function published(): array
    {
        return array_filter($this->all(), static fn (array $record): bool => ($record['status'] ?? '') === self::STATUS_PUBLISHED);
    }

    /** @return array<string,mixed>|null */
    public function find(string $key): ?array
    {
        $records = $this->all(); $key = sanitize_key($key);
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records = $this->all();
        $existing = $key !== '' ? ($records[sanitize_key($key)] ?? null) : null;
        $name = sanitize_text_field((string) ($input['name'] ?? ''));
        if ($name === '') throw new RuntimeException('A Steward Calling requires a name.');
        $key = is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name, $records);
        $status = sanitize_key((string) ($input['status'] ?? self::STATUS_DRAFT));
        if (! in_array($status, [self::STATUS_DRAFT,self::STATUS_PUBLISHED,self::STATUS_ARCHIVED], true)) $status = self::STATUS_DRAFT;
        $hitDie = (int) ($input['hit_die'] ?? 0);
        $saves = array_values(array_unique(array_filter(array_map('sanitize_key', (array) ($input['saving_throws'] ?? [])), static fn (string $v): bool => in_array($v, self::ABILITIES, true))));
        $description = sanitize_textarea_field((string) ($input['description'] ?? ''));
        $pathLabel = sanitize_text_field((string) ($input['path_label'] ?? 'Calling Path'));
        $pathLevel = max(1, min(20, (int) ($input['path_level'] ?? 3)));
        $paths = $this->paths((string) ($input['paths'] ?? ''), $key, $pathLevel);
        if ($status === self::STATUS_PUBLISHED && (! in_array($hitDie, self::HIT_DICE, true) || count($saves) !== 2 || $description === '')) {
            throw new RuntimeException('Published Steward Callings require a d6/d8/d10/d12 hit die, exactly two saving throws and a complete Calling description.');
        }
        $records[$key] = [
            'key'=>$key,'origin'=>'steward','status'=>$status,'name'=>$name,'description'=>$description,
            'hit_die'=>$hitDie,'saving_throws'=>$saves,'path_label'=>$pathLabel !== '' ? $pathLabel : 'Calling Path',
            'path_level'=>$pathLevel,'paths'=>$paths,'steward_notes'=>sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
            'updated_at'=>gmdate('c'),
        ];
        update_option(self::OPTION, $records, false);
        return $key;
    }

    /** @return array<int,array<string,mixed>> */
    private function paths(string $text, string $classKey, int $defaultLevel): array
    {
        $paths = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 5));
            if (($parts[0] ?? '') === '') continue;
            $name = sanitize_text_field($parts[0]);
            $key = 'steward-path-' . sanitize_key($classKey . '-' . $name);
            $level = isset($parts[1]) && ctype_digit($parts[1]) ? max(1, min(20, (int) $parts[1])) : $defaultLevel;
            $paths[] = ['key'=>$key,'parent'=>$classKey,'name'=>$name,'selection_level'=>$level,'description'=>sanitize_textarea_field($parts[2] ?? ''),'identity'=>sanitize_text_field($parts[3] ?? ''),'playstyle'=>sanitize_text_field($parts[4] ?? ''),'origin'=>'steward'];
        }
        return $paths;
    }

    /** @param array<string,array<string,mixed>> $records */
    private function uniqueKey(string $name, array $records): string
    {
        $base = sanitize_key($name) ?: 'calling'; $key = 'steward-calling-' . $base; $i = 2;
        while (isset($records[$key]) || CharacterClass::canonicalSupports($key)) $key = 'steward-calling-' . $base . '-' . $i++;
        return $key;
    }
}
