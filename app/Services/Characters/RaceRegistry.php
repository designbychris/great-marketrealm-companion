<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Characters;

use GreatMarketrealmCompanion\Services\Registry\Registry;

final class RaceRegistry extends Registry
{
    protected function register(): void
    {
        $this->registerItem(
            key: 'fructan',
            name: 'Fructan',
            attributes: [
                'description' => 'Fruit-born folk whose forms reflect the produce from which they descend.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'vegfolk',
            name: 'Vegfolk',
            attributes: [
                'description' => 'Hardy vegetable folk known for resilience, practicality and deep roots.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'capsicumite',
            name: 'Capsicumite',
            attributes: [
                'description' => 'Fiery pepper folk whose temperaments range from sweet to scorching.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'stalker',
            name: 'Stalker',
            attributes: [
                'description' => 'Tall, fibrous folk descended from celery, asparagus and other stalked produce.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'melonian',
            name: 'Melonian',
            attributes: [
                'description' => 'Broad and cheerful melon folk protected by naturally sturdy rinds.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'fungifolk',
            name: 'Fungifolk',
            attributes: [
                'description' => 'Mysterious mushroom folk connected through ancient subterranean networks.',
                'speed'       => 30,
                'size'        => 'Small or Medium',
            ],
        );

        $this->registerItem(
            key: 'dairyfolk',
            name: 'Dairyfolk',
            attributes: [
                'description' => 'Cultured folk whose many varieties include milk, cheese, cream and yoghurt lineages.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'boxfolk',
            name: 'Boxfolk',
            attributes: [
                'description' => 'Packaged people shaped by the brands, labels and shelves of the Marketrealm.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'rootkin',
            name: 'Rootkin',
            attributes: [
                'description' => 'Earthbound folk descended from potatoes, carrots, turnips and other root crops.',
                'speed'       => 30,
                'size'        => 'Medium',
            ],
        );

        $this->registerItem(
            key: 'marshmallow-folk',
            name: 'Marshmallow Folk',
            attributes: [
                'description' => 'Soft and buoyant confectionery folk with surprising resilience.',
                'speed'       => 30,
                'size'        => 'Small or Medium',
            ],
        );

        $this->registerItem(
            key: 'fluffling',
            name: 'Fluffling',
            attributes: [
                'description' => 'Light candyfloss folk whose bodies drift and curl like spun sugar.',
                'speed'       => 30,
                'size'        => 'Small',
            ],
        );
    }
}
