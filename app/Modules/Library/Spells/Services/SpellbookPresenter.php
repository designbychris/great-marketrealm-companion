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
        $kind = $this->normaliseKind(
            $filters['kind'] ?? ''
        );
        $level = $this->normaliseLevel(
            $filters['level'] ?? ''
        );
        $school = $this->normaliseSchool(
            $filters['school'] ?? ''
        );
        $access = $this->normaliseAccess(
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
                        $level !== ''
                        && (
                            $level === 'unknown'
                                ? $spell->level() !== null
                                : $spell->level() !== (int) $level
                        )
                    ) {
                        return false;
                    }

                    if (
                        $school !== ''
                        && (
                            $school === 'unknown'
                                ? $spell->school() !== null
                                : sanitize_key(
                                    (string) $spell->school()
                                ) !== $school
                        )
                    ) {
                        return false;
                    }

                    if (
                        $access !== ''
                        && (
                            $access === 'unknown'
                                ? $spell->accessLabels() !== []
                                : ! $this->matchesAccess(
                                    $spell,
                                    $access
                                )
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
                'q' => $query,
                'kind' => $kind,
                'level' => $level,
                'school' => $school,
                'access' => $access,
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

        $data['source_issue_labels'] =
            array_map(
                fn (string $issue): string =>
                    $this->sourceIssueLabel(
                        $issue
                    ),
                $spell->sourceIssues()
            );

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

    private function normaliseKind(
        string $kind
    ): string {
        $kind = sanitize_key($kind);

        return in_array(
            $kind,
            [
                'renamed',
                'marketrealm-original',
            ],
            true
        )
            ? $kind
            : '';
    }

    private function normaliseLevel(
        string $level
    ): string {
        $level = $this->normalise($level);

        if ($level === 'unknown') {
            return $level;
        }

        if (
            ! preg_match(
                '/^\d+$/',
                $level
            )
        ) {
            return '';
        }

        $numeric = (int) $level;

        return in_array(
            $numeric,
            $this->levelOptions(),
            true
        )
            ? (string) $numeric
            : '';
    }

    private function normaliseSchool(
        string $school
    ): string {
        $school = sanitize_key($school);

        if ($school === 'unknown') {
            return $school;
        }

        return in_array(
            $school,
            $this->schoolOptions(),
            true
        )
            ? $school
            : '';
    }

    private function normaliseAccess(
        string $access
    ): string {
        $normalised =
            $this->normalise($access);

        if ($normalised === 'unknown') {
            return $normalised;
        }

        foreach (
            $this->accessOptions()
            as $label
        ) {
            if (
                $this->normalise($label)
                === $normalised
            ) {
                return $normalised;
            }
        }

        return '';
    }

    private function sourceIssueLabel(
        string $issue
    ): string {
        return match ($issue) {
            'level-not-stated-in-handbook' =>
                'Level not stated in handbook',
            'school-not-stated-in-handbook' =>
                'School not stated in handbook',
            'access-not-stated-in-handbook' =>
                'Calling access not stated in handbook',
            default => ucwords(
                str_replace(
                    '-',
                    ' ',
                    $issue
                )
            ),
        };
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
