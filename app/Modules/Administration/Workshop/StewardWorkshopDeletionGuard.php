<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use RuntimeException;

\defined('ABSPATH') || exit;

/** Protects persistent Character and Encounter references from destructive Workshop deletion. */
final class StewardWorkshopDeletionGuard
{
    public const TYPES = ['monster', 'spell', 'background', 'equipment', 'calling'];

    public function assertDeletable(string $type, string $key): void
    {
        $type = sanitize_key($type);
        $key = sanitize_key($key);
        if (! in_array($type, self::TYPES, true) || $key === '' || ! str_starts_with($key, 'steward-')) {
            throw new RuntimeException('Only Steward-authored Workshop records may be permanently deleted.');
        }

        $count = $type === 'monster'
            ? $this->encounterReferences($key)
            : $this->characterReferences($type, $key);

        if ($count > 0) {
            $noun = $type === 'monster' ? 'Encounter' : 'Character';
            throw new RuntimeException(sprintf(
                'This Steward %s cannot be permanently deleted because %d %s record%s still reference%s it. Archive it instead.',
                $type,
                $count,
                $noun,
                $count === 1 ? '' : 's',
                $count === 1 ? 's' : ''
            ));
        }
    }

    private function characterReferences(string $type, string $key): int
    {
        $posts = get_posts([
            'post_type' => 'gmrc_character',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        $count = 0;
        foreach ($posts as $postId) {
            if ($this->characterUses((int) $postId, $type, $key)) {
                $count++;
            }
        }
        return $count;
    }

    private function characterUses(int $postId, string $type, string $key): bool
    {
        if ($type === 'calling') {
            if (sanitize_key((string) get_post_meta($postId, '_gmrc_class', true)) === $key) return true;
            $path = sanitize_key((string) get_post_meta($postId, '_gmrc_subclass', true));
            $record = get_option(CallingWorkshop::OPTION, []);
            foreach ((array) (($record[$key]['paths'] ?? [])) as $candidate) {
                if (is_array($candidate) && sanitize_key((string) ($candidate['key'] ?? '')) === $path) return true;
            }
            return false;
        }
        if ($type === 'background') {
            return sanitize_key((string) get_post_meta($postId, '_gmrc_background', true)) === $key;
        }
        if ($type === 'spell') {
            $book = get_post_meta($postId, '_gmrc_spellbook', true);
            $known = is_array($book) ? array_merge((array) ($book['spells'] ?? []), (array) ($book['cantrips'] ?? [])) : [];
            return in_array($key, array_map('sanitize_key', $known), true);
        }
        if ($type === 'equipment') {
            $inventory = get_post_meta($postId, '_gmrc_inventory', true);
            foreach (is_array($inventory) ? $inventory : [] as $entry) {
                if (is_array($entry) && sanitize_key((string) ($entry['item_id'] ?? '')) === $key) return true;
            }
        }
        return false;
    }

    private function encounterReferences(string $key): int
    {
        $posts = get_posts([
            'post_type' => 'gmrc_encounter',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);
        $count = 0;
        foreach ($posts as $postId) {
            $groups = get_post_meta((int) $postId, '_gmrc_encounter_monster_groups', true);
            foreach (is_array($groups) ? $groups : [] as $group) {
                if (is_array($group) && sanitize_key((string) ($group['monster_id'] ?? '')) === $key) {
                    $count++;
                    break;
                }
            }
        }
        return $count;
    }
}
