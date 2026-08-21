<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Canonical Player's Handbook "Backgrounds (optional)" transcription.
 * The source does not state languages, equipment or starting wealth.
 */
return [
    [
        'key' => 'crateborn-noble',
        'name' => 'Crateborn Noble',
        'feature_name' => 'Produce of Privilege',
        'feature_detail' => 'Gain favor with Fruitopian courts and advantage on social checks there.',
        'skills' => ['persuasion', 'history'],
        'tools' => ['cartographers-tools'],
        'tool_label' => 'Cartographer’s tools',
        'source_issues' => ['languages-not-stated-in-handbook', 'equipment-not-stated-in-handbook'],
    ],
    [
        'key' => 'backshelf-forager',
        'name' => 'Backshelf Forager',
        'feature_name' => "Scavenger's Eye",
        'feature_detail' => 'You always find edible resources or makeshift tools.',
        'skills' => ['survival', 'investigation'],
        'tools' => ['herbalism-kit'],
        'tool_label' => 'Herbalism Kit',
        'source_issues' => ['languages-not-stated-in-handbook', 'equipment-not-stated-in-handbook'],
    ],
    [
        'key' => 'discount-bin-survivor',
        'name' => 'Discount Bin Survivor',
        'feature_name' => 'Stickered But Strong',
        'feature_detail' => 'You’ve been marked for disposal once. Immune to being frightened once/day.',
        'skills' => ['intimidation', 'stealth'],
        'tools' => ['tinkers-tools'],
        'tool_label' => 'Tinker’s Tools',
        'source_issues' => ['languages-not-stated-in-handbook', 'equipment-not-stated-in-handbook'],
    ],
    [
        'key' => 'cleaners-acolyte',
        'name' => 'Cleaner’s Acolyte',
        'feature_name' => 'Sanitized Mind',
        'feature_detail' => 'You are immune to charm or fear from Rot creatures.',
        'skills' => ['arcana', 'religion'],
        'tools' => ['alchemists-supplies'],
        'tool_label' => 'Alchemist’s Supplies',
        'source_issues' => ['languages-not-stated-in-handbook', 'equipment-not-stated-in-handbook'],
    ],
    [
        'key' => 'cart-ranger',
        'name' => 'Cart Ranger',
        'feature_name' => 'Aisle Scout',
        'feature_detail' => 'You have advantage on checks to navigate or avoid hazards in store environments.',
        'skills' => ['athletics', 'nature'],
        'tools' => ['navigators-tools'],
        'tool_label' => 'Navigator’s Tools',
        'source_issues' => ['languages-not-stated-in-handbook', 'equipment-not-stated-in-handbook'],
    ],
];
