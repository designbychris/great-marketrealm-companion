<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Contracts\PathProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/** Generic path-choice definition for mechanically certified Steward Callings. */
final class StewardCallingPathProgression implements PathProgressionDefinitionInterface
{
    public function supports(CharacterClass $class): bool { return $this->record($class) !== null; }

    /** @return array<string,mixed> */
    public function definition(CharacterClass $class): array
    {
        $record = $this->record($class);
        if ($record === null) throw new InvalidArgumentException('Steward Calling Path progression cannot resolve this Calling.');
        return [
            'class'=>$class->value(), 'label'=>(string) ($record['path_label'] ?? 'Calling Path'),
            'folio_label'=>(string) ($record['path_label'] ?? 'Calling Path') . ' Folio',
            'choice_key'=>$class->value() . '-calling-path', 'selection_level'=>(int) ($record['path_level'] ?? 3),
            'description'=>'Choose the Steward-certified Calling Path that shapes this adventurer’s specialist identity.',
        ];
    }

    /** @return array<string,mixed>|null */
    private function record(CharacterClass $class): ?array
    {
        if (! function_exists('get_option')) return null;
        $records = get_option('gmrc_steward_callings', []);
        $record = is_array($records) ? ($records[$class->value()] ?? null) : null;
        return is_array($record) && ($record['status'] ?? '') === 'published' && (array) ($record['paths'] ?? []) !== [] ? $record : null;
    }
}
