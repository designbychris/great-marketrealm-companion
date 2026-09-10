<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Backgrounds\Models;

defined('ABSPATH') || exit;

final class BackgroundRecord
{
    public function __construct(
        private string $key,
        private string $name,
        private string $featureName,
        private string $featureDetail,
        private array $skills,
        private array $tools,
        private string $toolLabel,
        private array $sourceIssues = [],
        private int $languageChoices = 0,
        private array $fixedLanguages = [],
        private ?string $canonicalId = null,
        private ?string $expansion = null
    ) {
    }

    public function key(): string { return $this->key; }
    public function name(): string { return $this->name; }
    public function featureName(): string { return $this->featureName; }
    public function featureDetail(): string { return $this->featureDetail; }
    public function skills(): array { return $this->skills; }
    public function tools(): array { return $this->tools; }
    public function toolLabel(): string { return $this->toolLabel; }
    public function sourceIssues(): array { return $this->sourceIssues; }
    public function languageChoices(): int { return $this->languageChoices; }
    public function fixedLanguages(): array { return $this->fixedLanguages; }
    public function canonicalId(): ?string { return $this->canonicalId; }
    public function expansion(): ?string { return $this->expansion; }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'feature_name' => $this->featureName,
            'feature_detail' => $this->featureDetail,
            'skills' => $this->skills,
            'tools' => $this->tools,
            'tool_label' => $this->toolLabel,
            'source_issues' => $this->sourceIssues,
            'language_choices' => $this->languageChoices,
            'fixed_languages' => $this->fixedLanguages,
            'canonical_id' => $this->canonicalId,
            'expansion' => $this->expansion,
        ];
    }
}
