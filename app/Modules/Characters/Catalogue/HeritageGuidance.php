<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue;

defined('ABSPATH') || exit;

/**
 * Normalises canonical and Steward Heritage records into one presentation contract.
 *
 * The source record remains authoritative. This class only makes equivalent field
 * shapes predictable for the Character Builder and future Ledger consumers.
 */
final class HeritageGuidance
{
    /** @return array<string,mixed> */
    public static function normalize(array $heritage): array
    {
        $mechanics = is_array($heritage['mechanics'] ?? null)
            ? $heritage['mechanics']
            : [];

        $abilities = self::abilityModifiers($mechanics, $heritage);
        $features = self::features($mechanics, $heritage);
        $choices = self::proficiencyChoices($mechanics, $heritage);

        return [
            'ability_modifiers' => $abilities,
            'skill_proficiencies' => self::stringList(
                $mechanics['skill_proficiencies'] ?? $mechanics['proficiencies'] ?? []
            ),
            'tool_proficiencies' => self::stringList($mechanics['tool_proficiencies'] ?? []),
            'automatic_languages' => self::stringList(
                $mechanics['automatic_languages'] ?? $heritage['languages'] ?? []
            ),
            'chosen_language_count' => max(0, (int) ($mechanics['chosen_language_count'] ?? 0)),
            'resistances' => self::stringList($mechanics['resistances'] ?? []),
            'size' => self::scalarText($mechanics['size'] ?? $heritage['size'] ?? null),
            'speed' => self::speed($mechanics['speed'] ?? $heritage['speed'] ?? null),
            'features' => $features,
            'proficiency_choices' => $choices,
        ];
    }

    /** @return array<int,string> */
    public static function traits(array $record): array
    {
        return self::stringList($record['traits'] ?? []);
    }

    /** @return array<string,int> */
    private static function abilityModifiers(array $mechanics, array $heritage): array
    {
        $raw = $mechanics['ability_modifiers']
            ?? $mechanics['abilities']
            ?? $heritage['ability_modifiers']
            ?? $heritage['ability_scores']
            ?? [];

        if (! is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $ability => $bonus) {
            if (! is_scalar($ability) || ! is_numeric($bonus)) {
                continue;
            }
            $key = strtolower(trim((string) $ability));
            if ($key !== '') {
                $result[$key] = (int) $bonus;
            }
        }

        return $result;
    }

    /** @return array<int,array{name:string,description:string}> */
    private static function features(array $mechanics, array $heritage): array
    {
        $raw = $mechanics['features'] ?? $mechanics['core_traits'] ?? $heritage['core_traits'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $features = [];
        foreach ($raw as $key => $feature) {
            $name = '';
            $description = '';

            if (is_array($feature)) {
                $name = self::scalarText($feature['name'] ?? $key);
                $description = self::scalarText(
                    $feature['description'] ?? $feature['text'] ?? $feature['effect'] ?? null
                );
            } elseif (is_string($key) && is_scalar($feature)) {
                $name = trim($key);
                $description = trim((string) $feature);
            }

            if ($name !== '' && $description !== '') {
                $features[] = ['name' => $name, 'description' => $description];
            }
        }

        return $features;
    }

    /** @return array<int,array{name:string,choose:int,from:array<int,string>}> */
    private static function proficiencyChoices(array $mechanics, array $heritage): array
    {
        $raw = $mechanics['proficiency_choices'] ?? $heritage['proficiency_choices'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $choices = [];
        foreach ($raw as $choice) {
            if (! is_array($choice)) {
                continue;
            }
            $from = self::stringList($choice['from'] ?? $choice['options'] ?? []);
            $choose = max(1, (int) ($choice['choose'] ?? 1));
            if ($from === []) {
                continue;
            }
            $choices[] = [
                'name' => self::scalarText($choice['name'] ?? 'Proficiency choice'),
                'choose' => min($choose, count($from)),
                'from' => $from,
            ];
        }

        return $choices;
    }

    /** @return array<int,string> */
    private static function stringList(mixed $value): array
    {
        if (is_scalar($value)) {
            $value = [trim((string) $value)];
        }
        if (! is_array($value)) {
            return [];
        }

        $strings = [];
        foreach ($value as $item) {
            if (! is_scalar($item)) {
                continue;
            }
            $item = trim((string) $item);
            if ($item !== '') {
                $strings[] = $item;
            }
        }

        return array_values(array_unique($strings));
    }

    private static function scalarText(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private static function speed(mixed $value): string
    {
        if (! is_scalar($value) || trim((string) $value) === '') {
            return '';
        }

        $text = trim((string) $value);
        return is_numeric($text) ? $text . ' ft' : $text;
    }
}
