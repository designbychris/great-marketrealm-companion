<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Spells\Services;

use GreatMarketrealmCompanion\Modules\Library\Spells\Models\SpellRecord;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\HandbookSpellRegister;

defined('ABSPATH') || exit;

/**
 * Query and presentation service for Sage's read-only Spellbook.
 */
final class SpellbookPresenter
{
    public function __construct(
        private ?HandbookSpellRegister $register = null
    ) {
        $this->register ??= new HandbookSpellRegister();
    }

    /**
     * @param array<string,string> $filters
     * @return array<string,mixed>
     */
    public function present(array $filters = []): array
    {
        $query = $this->normalise(
            $filters['q'] ?? ''
        );
        $kind = sanitize_key(
            $filters['kind'] ?? ''
        );
        $level = $this->normaliseLevel(
            $filters['level'] ?? ''
        );
        $school = sanitize_key(
            $filters['school'] ?? ''
        );
        $access = $this->normalise(
            $filters['access'] ?? ''
        );

        $records = array_values(
            array_filter(
                $this->register->all(),
                function (
                    SpellRecord $spell
                ) use (
                    $query,
                    $kind,
                    $level,
                    $school,
                    $access
                ): bool {
                    if (
                        $kind !== ''
                        && $spell->kind() !== $kind
                    ) {
                        return false;
                    }

                    if (
                        $level !== null
                        && $spell->level() !== $level
                    ) {
                        return false;
                    }

                    if (
                        $school !== ''
                        && sanitize_key(
                            (string) $spell->school()
                        ) !== $school
                    ) {
                        return false;
                    }

                    if (
                        $access !== ''
                        && ! $this->matchesAccess(
                            $spell,
                            $access
                        )
                    ) {
                        return false;
                    }

                    return $query === ''
                        || $this->matchesQuery(
                            $spell,
                            $query
                        );
                }
            )
        );

        usort(
            $records,
            static fn (
                SpellRecord $a,
                SpellRecord $b
            ): int => strcasecmp(
                $a->name(),
                $b->name()
            )
        );

        return [
            'filters' => [
                'q' => $filters['q'] ?? '',
                'kind' => $kind,
                'level' => $filters['level'] ?? '',
                'school' => $school,
                'access' => $filters['access'] ?? '',
            ],
            'results' => array_map(
                fn (SpellRecord $spell): array =>
                    $this->spellView($spell),
                $records
            ),
            'result_count' => count($records),
            'total_count' =>
                count($this->register->all()),
            'levels' =>
                $this->levelOptions(),
            'schools' =>
                $this->schoolOptions(),
            'access_labels' =>
                $this->accessOptions(),
            'renamed_count' =>
                count(
                    $this->register->byKind(
                        'renamed'
                    )
                ),
            'original_count' =>
                count(
                    $this->register->byKind(
                        'marketrealm-original'
                    )
                ),
            'source_issue_count' =>
                count(
                    $this->register
                        ->withSourceIssues()
                ),
        ];
    }

    /** @return array<string,mixed> */
    private function spellView(
        SpellRecord $spell
    ): array {
        $data = $spell->toArray();

        $data['level_label'] =
            $spell->level() === null
                ? 'Level not stated'
                : (
                    $spell->level() === 0
                        ? 'Cantrip'
                        : 'Level '
                            . $spell->level()
                );

        $data['school_label'] =
            $spell->school() === null
                ? 'School not stated'
                : ucwords(
                    str_replace(
                        '-',
                        ' ',
                        $spell->school()
                    )
                );

        $data['kind_label'] =
            $spell->kind() === 'renamed'
                ? 'Marketrealm Rename'
                : 'Marketrealm Original';

        return $data;
    }

    private function matchesQuery(
        SpellRecord $spell,
        string $query
    ): bool {
        $haystack = [
            $spell->name(),
            $spell->originalSpell() ?? '',
            $spell->school() ?? '',
            implode(
                ' ',
                $spell->accessLabels()
            ),
        ];

        foreach ($spell->variants() as $variant) {
            $haystack[] =
                (string) (
                    $variant['source_text']
                    ?? ''
                );
        }

        return str_contains(
            $this->normalise(
                implode(
                    ' ',
                    $haystack
                )
            ),
            $query
        );
    }

    private function matchesAccess(
        SpellRecord $spell,
        string $access
    ): bool {
        foreach (
            $spell->accessLabels()
            as $label
        ) {
            if (
                $this->normalise($label)
                === $access
            ) {
                return true;
            }
        }

        return false;
    }

    /** @return array<int,int|string> */
    private function levelOptions(): array
    {
        $levels = [];

        foreach (
            $this->register->all()
            as $spell
        ) {
            if ($spell->level() !== null) {
                $levels[] = $spell->level();
            }
        }

        $levels = array_values(
            array_unique($levels)
        );

        sort($levels);

        return $levels;
    }

    /** @return array<int,string> */
    private function schoolOptions(): array
    {
        $schools = [];

        foreach (
            $this->register->all()
            as $spell
        ) {
            if ($spell->school() !== null) {
                $schools[] =
                    $spell->school();
            }
        }

        $schools = array_values(
            array_unique($schools)
        );

        sort(
            $schools,
            SORT_NATURAL
                | SORT_FLAG_CASE
        );

        return $schools;
    }

    /** @return array<int,string> */
    private function accessOptions(): array
    {
        $labels = [];

        foreach (
            $this->register->all()
            as $spell
        ) {
            foreach (
                $spell->accessLabels()
                as $label
            ) {
                $labels[] = $label;
            }
        }

        $labels = array_values(
            array_unique($labels)
        );

        sort(
            $labels,
            SORT_NATURAL
                | SORT_FLAG_CASE
        );

        return $labels;
    }

    private function normaliseLevel(
        string $level
    ): ?int {
        if ($level === '') {
            return null;
        }

        if (
            ! preg_match(
                '/^\d+$/',
                $level
            )
        ) {
            return null;
        }

        return (int) $level;
    }

    private function normalise(
        string $value
    ): string {
        return strtolower(
            trim(
                sanitize_text_field(
                    $value
                )
            )
        );
    }
}
