<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Definitions\Characters\RaceDefinition;
use GreatMarketrealmCompanion\Services\Registry\Registry;

final class RaceRegistry extends Registry
{
    protected function register(): void
    {
        $this->registerDefinitions([
            $this->race(
                key: 'fructan',
                name: 'Fructan',
                description: 'Fruit-born folk whose forms reflect the produce from which they descend.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'vegfolk',
                name: 'Vegfolk',
                description: 'Hardy vegetable folk known for resilience, practicality and deep roots.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'capsicumite',
                name: 'Capsicumite',
                description: 'Fiery pepper folk whose temperaments range from sweet to scorching.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'stalker',
                name: 'Stalker',
                description: 'Tall, fibrous folk descended from celery, asparagus and other stalked produce.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'melonian',
                name: 'Melonian',
                description: 'Broad and cheerful melon folk protected by naturally sturdy rinds.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'fungifolk',
                name: 'Fungifolk',
                description: 'Mysterious mushroom folk connected through ancient subterranean networks.',
                speed: 30,
                size: 'Small or Medium',
            ),

            $this->race(
                key: 'dairyfolk',
                name: 'Dairyfolk',
                description: 'Cultured folk whose many varieties include milk, cheese, cream and yoghurt lineages.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'boxfolk',
                name: 'Boxfolk',
                description: 'Packaged people shaped by the brands, labels and shelves of the Marketrealm.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'rootkin',
                name: 'Rootkin',
                description: 'Earthbound folk descended from potatoes, carrots, turnips and other root crops.',
                speed: 30,
                size: 'Medium',
            ),

            $this->race(
                key: 'marshmallow-folk',
                name: 'Marshmallow Folk',
                description: 'Soft and buoyant confectionery folk with surprising resilience.',
                speed: 30,
                size: 'Small or Medium',
            ),

            $this->race(
                key: 'fluffling',
                name: 'Fluffling',
                description: 'Light candyfloss folk whose bodies drift and curl like spun sugar.',
                speed: 30,
                size: 'Small',
            ),
        ]);
    }

    private function race(
        string $key,
        string $name,
        string $description,
        int $speed,
        string $size
    ): RaceDefinition {
        return (new RaceDefinition(
            key: $key,
            name: $name,
        ))
            ->description($description)
            ->speed($speed)
            ->size($size);
    }
}
