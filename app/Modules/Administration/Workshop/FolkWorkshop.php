<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use RuntimeException;

defined('ABSPATH') || exit;

/** Steward-authored playable Folk and their Heritages. */
final class FolkWorkshop
{
    public const OPTION = 'gmrc_steward_folk';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

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
        $records = $this->all();
        $key = sanitize_key($key);
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    public function delete(string $key): void
    {
        $key = sanitize_key($key);
        $records = $this->all();
        if ($key === '' || ! isset($records[$key])) {
            throw new RuntimeException('The Steward Folk record could not be found.');
        }
        unset($records[$key]);
        update_option(self::OPTION, $records, false);
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records = $this->all();
        $existing = $key !== '' ? ($records[sanitize_key($key)] ?? null) : null;
        $name = sanitize_text_field((string) ($input['name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('A Steward Folk requires a name.');
        }

        $key = is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name, $records);
        $status = sanitize_key((string) ($input['status'] ?? self::STATUS_DRAFT));
        if (! in_array($status, [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_ARCHIVED], true)) {
            $status = self::STATUS_DRAFT;
        }

        $speed = (int) ($input['speed'] ?? 30);
        $size = sanitize_text_field((string) ($input['size'] ?? 'Medium'));
        $creatureType = sanitize_text_field((string) ($input['creature_type'] ?? 'Humanoid'));
        $description = sanitize_textarea_field((string) ($input['description'] ?? ''));
        $darkvision = max(0, min(300, (int) ($input['darkvision'] ?? 0)));
        $languages = $this->lines((string) ($input['languages'] ?? ''));
        $traits = $this->lines((string) ($input['traits'] ?? ''));
        $portraitUrl = esc_url_raw(
            trim((string) ($input['portrait_url'] ?? ''))
        );
        $abilityModifiers = [];
        foreach (['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'] as $ability) {
            $abilityModifiers[$ability] = max(
                0,
                min(4, (int) ($input['ability_' . $ability] ?? 0))
            );
        }
        $skillProficiencies = $this->lines((string) ($input['skill_proficiencies'] ?? ''));
        $toolProficiencies = $this->lines((string) ($input['tool_proficiencies'] ?? ''));
        $automaticLanguages = $this->lines((string) ($input['automatic_languages'] ?? ''));
        $resistances = $this->lines((string) ($input['resistances'] ?? ''));
        $existingHeritageMechanics = [];
        foreach ((array) ($records[$key]['heritages'] ?? []) as $existingHeritage) {
            if (! is_array($existingHeritage) || empty($existingHeritage['key'])) {
                continue;
            }
            $existingHeritageMechanics[(string) $existingHeritage['key']] =
                is_array($existingHeritage['mechanics'] ?? null)
                    ? $existingHeritage['mechanics']
                    : [];
        }

        $heritages = $this->heritages(
            (string) ($input['heritages'] ?? ''),
            $key,
            $existingHeritageMechanics,
            $input
        );

        if ($status === self::STATUS_PUBLISHED && (
            $description === ''
            || $creatureType === ''
            || ! in_array($speed, range(0, 120, 5), true)
            || ! in_array($size, ['Small', 'Medium', 'Small or Medium'], true)
        )) {
            throw new RuntimeException('Published Steward Folk require a complete description, recognised size, creature type and a walking speed from 0–120 feet in five-foot increments.');
        }

        $record = [
            'key' => $key,
            'origin' => 'steward',
            'status' => $status,
            'name' => $name,
            'description' => $description,
            'speed' => $speed,
            'size' => $size,
            'creature_type' => $creatureType,
            'darkvision' => $darkvision,
            'languages' => $languages,
            'traits' => $traits,
            'portrait_url' => $portraitUrl,
            'mechanics' => [
                'ability_modifiers' => $abilityModifiers,
                'skill_proficiencies' => $skillProficiencies,
                'tool_proficiencies' => $toolProficiencies,
                'automatic_languages' => $automaticLanguages,
                'chosen_language_count' => max(0, min(4, (int) ($input['chosen_language_count'] ?? 0))),
                'resistances' => $resistances,
            ],
            'heritages' => $heritages,
            'steward_notes' => sanitize_textarea_field((string) ($input['steward_notes'] ?? '')),
            'updated_at' => gmdate('c'),
        ];

        if ($status === self::STATUS_PUBLISHED) {
            $certificationErrors = (new CustomFolkCertification())->errors($record);
            if ($certificationErrors !== []) {
                throw new RuntimeException(implode(' ', $certificationErrors));
            }
        }

        $records[$key] = $record;
        update_option(self::OPTION, $records, false);
        return $key;
    }

    /** @return array<int,string> */
    private function lines(string $text): array
    {
        $values = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $value = sanitize_text_field(trim($line));
            if ($value !== '') {
                $values[] = $value;
            }
        }
        return array_values(array_unique($values));
    }

    /**
     * @param array<string,array<string,mixed>> $existingMechanics
     * @param array<string,mixed> $input
     * @return array<int,array<string,mixed>>
     */
    private function heritages(
        string $text,
        string $folkKey,
        array $existingMechanics = [],
        array $input = []
    ): array {
        $items = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 4));
            if (($parts[0] ?? '') === '') {
                continue;
            }

            $name = sanitize_text_field($parts[0]);
            $heritageKey = 'steward-heritage-'
                . sanitize_key($folkKey . '-' . $name);
            $mechanics = $this->heritageMechanics(
                $heritageKey,
                $existingMechanics[$heritageKey] ?? [],
                $input
            );

            $items[] = [
                'key' => $heritageKey,
                'parent' => $folkKey,
                'name' => $name,
                'description' => sanitize_textarea_field($parts[1] ?? ''),
                'identity' => sanitize_text_field($parts[2] ?? ''),
                'traits' => sanitize_text_field($parts[3] ?? ''),
                'origin' => 'steward',
                'mechanics' => $mechanics,
            ];
        }

        return $items;
    }

    /**
     * Resolve the structured mechanical additions for one Heritage.
     *
     * @param array<string,mixed> $existing
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function heritageMechanics(
        string $heritageKey,
        array $existing,
        array $input
    ): array {
        $posted = is_array($input['heritage_mechanics'][$heritageKey] ?? null)
            ? $input['heritage_mechanics'][$heritageKey]
            : null;

        if ($posted === null) {
            return $existing;
        }

        $abilityModifiers = [];
        $rawAbilities = is_array($posted['ability_modifiers'] ?? null)
            ? $posted['ability_modifiers']
            : [];

        foreach (['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'] as $ability) {
            $abilityModifiers[$ability] = max(
                0,
                min(4, (int) ($rawAbilities[$ability] ?? 0))
            );
        }

        return [
            'ability_modifiers' => $abilityModifiers,
            'skill_proficiencies' => $this->lines(
                (string) ($posted['skill_proficiencies'] ?? '')
            ),
            'tool_proficiencies' => $this->lines(
                (string) ($posted['tool_proficiencies'] ?? '')
            ),
            'automatic_languages' => $this->lines(
                (string) ($posted['automatic_languages'] ?? '')
            ),
            'chosen_language_count' => max(
                0,
                min(4, (int) ($posted['chosen_language_count'] ?? 0))
            ),
            'resistances' => $this->lines(
                (string) ($posted['resistances'] ?? '')
            ),
            'size' => sanitize_text_field((string) ($posted['size'] ?? '')),
            'speed' => ($posted['speed'] ?? '') === ''
                ? ''
                : max(0, min(120, (int) $posted['speed'])),
            'features' => $this->heritageFeatures(
                (string) ($posted['features'] ?? '')
            ),
            'proficiency_choices' => $this->heritageProficiencyChoices(
                (string) ($posted['proficiency_choices'] ?? '')
            ),
        ];
    }

    /** @return array<int,array{name:string,description:string}> */
    private function heritageFeatures(string $text): array
    {
        $features = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 2));
            $name = sanitize_text_field((string) ($parts[0] ?? ''));
            $description = sanitize_textarea_field((string) ($parts[1] ?? ''));
            if ($name !== '' || $description !== '') {
                $features[] = ['name' => $name, 'description' => $description];
            }
        }

        return $features;
    }

    /** @return array<int,array{name:string,choose:int,from:array<int,string>}> */
    private function heritageProficiencyChoices(string $text): array
    {
        $choices = [];
        foreach (preg_split('/\R/', $text) ?: [] as $line) {
            $parts = array_map('trim', explode('|', $line, 3));
            $name = sanitize_text_field((string) ($parts[0] ?? ''));
            $choose = max(1, min(6, (int) ($parts[1] ?? 1)));
            $from = array_values(array_filter(array_map(
                'sanitize_text_field',
                array_map('trim', explode(',', (string) ($parts[2] ?? '')))
            )));
            if ($name !== '' || $from !== []) {
                $choices[] = ['name' => $name, 'choose' => $choose, 'from' => $from];
            }
        }

        return $choices;
    }

    /** @param array<string,array<string,mixed>> $records */
    private function uniqueKey(string $name, array $records): string
    {
        $base = sanitize_key($name) ?: 'folk';
        $key = 'steward-folk-' . $base;
        $index = 2;
        while (isset($records[$key]) || Race::canonicalSupports($key)) {
            $key = 'steward-folk-' . $base . '-' . $index++;
        }
        return $key;
    }
}
