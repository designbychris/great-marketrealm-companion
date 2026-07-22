<?php

namespace GreatMarketrealmCompanion\Modules\Characters\Resources;

use GreatMarketrealmCompanion\Modules\Characters\Controllers\CharacterController;
use GreatMarketrealmCompanion\Resources\Resource;

class CharacterResource extends Resource

defined('ABSPATH') || exit;

/**
 * Character Resource.
 *
 * Defines the application behaviour for managing characters.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
class CharacterResource extends Resource
{
    /**
     * Unique Resource key.
     */
    public function key(): string
    {
        return 'characters';
    }

    /**
     * Singular display name.
     */
    public function singularName(): string
    {
        return 'Character';
    }

    /**
     * Plural display name.
     */
    public function pluralName(): string
    {
        return 'Characters';
    }

    /**
     * Base route prefix.
     */
    public function routePrefix(): string
    {
        return '/characters';
    }

    /**
     * Resource controller.
     */
    public function controller(): string
    {
        return CharacterController::class;
    }
}
