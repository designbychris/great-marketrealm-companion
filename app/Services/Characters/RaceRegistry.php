<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Services\Definitions\Definitions;
use GreatMarketrealmCompanion\Services\Registry\Registry;

defined('ABSPATH') || exit;

/**
 * Registry of playable Marketrealm races.
 *
 * @since 0.3.0
 */
final class RaceRegistry extends Registry
{
    /**
     * Create the Race Registry.
     */
    public function __construct(
        private Definitions $definitions
    ) {
        parent::__construct();
    }

    /**
     * Register the playable races.
     */
    protected function register(): void
    {
        $scriptorium = $this->definitions->scriptorium();

        $scriptorium
            ->race(
                key: 'fructan',
                name: 'Fructan'
            )
                ->description(
                    'Fruit-born folk whose forms reflect the produce from which they descend.'
                )
                ->speed(30)
                ->size('Medium')
                ->creatureType('Humanoid')
                ->language('Common')
                ->language('Produce')
                ->done()

            ->race(
                key: 'vegfolk',
                name: 'Vegfolk'
            )
                ->description(
                    'Hardy vegetable folk known for resilience, practicality and deep roots.'
                )
                ->speed(30)
                ->size('Medium')
                ->creatureType('Humanoid')
                ->language('Common')
                ->language('Produce')
                ->done()

            ->race(
                key: 'capsicumite',
                name: 'Capsicumite'
            )
                ->description(
                    'Fiery pepper folk whose temperaments range from sweet to scorching.'
                )
                ->speed(30)
                ->size('Medium')
                ->creatureType('Humanoid')
                ->resistance('Fire')
                ->language('Common')
                ->language('Produce')
                ->trait('Pepper Heat')
                ->done()

            ->race(
                key: 'fungifolk',
                name: 'Fungifolk'
            )
                ->description(
                    'Mysterious mushroom folk connected through ancient subterranean networks.'
                )
                ->speed(30)
                ->size('Small or Medium')
                ->creatureType('Humanoid')
                ->darkvision(60)
                ->language('Common')
                ->language('Mycelial')
                ->trait('Spore Speech')
                ->done()

            ->race(
                key: 'rootkin',
                name: 'Rootkin'
            )
                ->description(
                    'Earthbound folk descended from potatoes, carrots, turnips and other root crops.'
                )
                ->speed(30)
                ->size('Medium')
                ->creatureType('Humanoid')
                ->language('Common')
                ->language('Produce')
                ->trait('Deep Roots')
                ->done();

        if (function_exists('get_option')) {
            $records = get_option('gmrc_steward_folk', []);
            foreach (is_array($records) ? $records : [] as $key => $record) {
                if (! is_array($record) || ($record['status'] ?? '') !== 'published') {
                    continue;
                }
                $name = trim((string) ($record['name'] ?? ''));
                $description = trim((string) ($record['description'] ?? ''));
                if ($name === '' || $description === '') {
                    continue;
                }
                $builder = $scriptorium->race(key: sanitize_key((string) $key), name: $name)
                    ->description($description)
                    ->speed((int) ($record['speed'] ?? 30))
                    ->size((string) ($record['size'] ?? 'Medium'))
                    ->creatureType((string) ($record['creature_type'] ?? 'Humanoid'));
                if ((int) ($record['darkvision'] ?? 0) > 0) {
                    $builder->darkvision((int) $record['darkvision']);
                }
                foreach ((array) ($record['languages'] ?? []) as $language) {
                    $builder->language((string) $language);
                }
                foreach ((array) ($record['traits'] ?? []) as $trait) {
                    $builder->trait((string) $trait);
                }
                $builder->done();
            }
        }

        $this->registerDefinitions(
            $scriptorium->definitions()
        );
    }
}
