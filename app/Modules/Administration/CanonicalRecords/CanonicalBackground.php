<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\CanonicalRecords;

defined('ABSPATH') || exit;

final class CanonicalBackground
{
    /**
     * @param array<int,string> $skills
     * @param array<int,string> $tools
     * @param array<int,string> $sourceIssues
     */
    public function __construct(
        private string $key,
        private string $name,
        private string $featureName,
        private string $featureDetail,
        private array $skills,
        private array $tools,
        private string $toolLabel,
        private array $sourceIssues,
        private string $source,
        private string $stewardNotes = ''
    ) {
    }

    public function key(): string { return $this->key; }
    public function name(): string { return $this->name; }
    public function featureName(): string { return $this->featureName; }
    public function featureDetail(): string { return $this->featureDetail; }
    /** @return array<int,string> */
    public function skills(): array { return $this->skills; }
    /** @return array<int,string> */
    public function tools(): array { return $this->tools; }
    public function toolLabel(): string { return $this->toolLabel; }
    /** @return array<int,string> */
    public function sourceIssues(): array { return $this->sourceIssues; }
    public function source(): string { return $this->source; }
    public function stewardNotes(): string { return $this->stewardNotes; }
}
