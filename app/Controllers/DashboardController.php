<?php

namespace GreatMarketrealmCompanion\Controllers;

use GreatMarketrealmCompanion\Repositories\CharacterRepository;

defined('ABSPATH') || exit;

/**
 * Dashboard Controller
 *
 * Handles the player dashboard.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.1
 */
class DashboardController extends Controller
{
    /**
     * Character Repository.
     *
     * @var CharacterRepository
     */
    protected CharacterRepository $characters;

    /**
     * Constructor.
     */

    /**
     * TODO:
     * Replace with Dependency Injection Container.
     */
    public function __construct()
    {
        $this->characters = new CharacterRepository();
    }

    /**
     * Dashboard.
     *
     * @return string
     */
    public function index(): string
    {
        $this->requireLogin();

        $user = $this->user();

        $characters = $this->characters->getByUser($user->ID);

        return $this->view(
            'dashboard.index',
            [
                'user'             => $user,
                'characters'       => $characters,
                'character_count' => $this->characters->countByUser($userId),
                'page_title'       => __('Dashboard', 'great-marketrealm-companion'),
            ]
        );
    }
}
