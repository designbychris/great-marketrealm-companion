<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories;

use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Steward-controlled illustrations used by Character Creation choice cards.
 */
final class CharacterCardArtworkRegister
{
    public const OPTION = 'gmrc_character_card_artwork';

    /** @return array<string,array<string,int>> */
    public function all(): array
    {
        $stored = function_exists('get_option')
            ? get_option(self::OPTION, [])
            : [];

        $normalised = [
            'race' => [],
            'class' => [],
            'background' => [],
        ];

        if (! is_array($stored)) {
            return $normalised;
        }

        foreach ($normalised as $kind => $_) {
            $records = is_array($stored[$kind] ?? null)
                ? $stored[$kind]
                : [];

            foreach ($records as $key => $attachmentId) {
                $key = sanitize_key((string) $key);
                $attachmentId = absint($attachmentId);
                if ($key !== '' && $attachmentId > 0) {
                    $normalised[$kind][$key] = $attachmentId;
                }
            }
        }

        return $normalised;
    }

    public function attachmentId(string $kind, string $key): int
    {
        $kind = $this->kind($kind);
        $key = sanitize_key($key);
        return (int) ($this->all()[$kind][$key] ?? 0);
    }

    /** @return array<string,array<string,array{attachment_id:int,url:string}>> */
    public function presentationMap(): array
    {
        $map = ['race' => [], 'class' => [], 'background' => []];

        foreach ($this->all() as $kind => $records) {
            foreach ($records as $key => $attachmentId) {
                $url = function_exists('wp_get_attachment_image_url')
                    ? wp_get_attachment_image_url($attachmentId, 'large')
                    : false;

                if (is_string($url) && $url !== '') {
                    $map[$kind][$key] = [
                        'attachment_id' => $attachmentId,
                        'url' => $url,
                    ];
                }
            }
        }

        return $map;
    }

    public function save(string $kind, string $key, int $attachmentId): void
    {
        $kind = $this->kind($kind);
        $key = sanitize_key($key);
        if ($key === '') {
            throw new RuntimeException('Choose a Character Creation record before assigning artwork.');
        }

        if ($attachmentId > 0
            && function_exists('wp_attachment_is_image')
            && ! wp_attachment_is_image($attachmentId)) {
            throw new RuntimeException('Character card artwork must be a WordPress image attachment.');
        }

        $records = $this->all();
        if ($attachmentId > 0) {
            $records[$kind][$key] = $attachmentId;
        } else {
            unset($records[$kind][$key]);
        }

        if (function_exists('update_option')) {
            update_option(self::OPTION, $records, false);
        }
    }

    private function kind(string $kind): string
    {
        $kind = sanitize_key($kind);
        if (! in_array($kind, ['race', 'class', 'background'], true)) {
            throw new RuntimeException('That Character Creation artwork type is not recognised.');
        }

        return $kind;
    }
}
