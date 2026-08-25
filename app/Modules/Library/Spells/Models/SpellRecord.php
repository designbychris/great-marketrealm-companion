<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Spells\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/** One canonical or Steward-authored Spell Register identity. */
final class SpellRecord
{
    /** @param array<string,mixed> $record */
    private function __construct(private array $record) {}

    /** @param array<string,mixed> $record */
    public static function fromArray(array $record): self
    {
        foreach (['key', 'name', 'kind', 'variants'] as $required) {
            if (! array_key_exists($required, $record)) {
                throw new InvalidArgumentException(sprintf('Spell record is missing "%s".', $required));
            }
        }
        if (! in_array($record['kind'], ['renamed', 'marketrealm-original'], true)) {
            throw new InvalidArgumentException('Spell record kind is not recognised.');
        }
        if (! is_array($record['variants']) || $record['variants'] === []) {
            throw new InvalidArgumentException('Spell record requires a source variant.');
        }
        return new self($record);
    }

    public function key(): string { return (string) $this->record['key']; }
    public function name(): string { return (string) $this->record['name']; }
    public function kind(): string { return (string) $this->record['kind']; }
    public function originalSpell(): ?string { return $this->nullableString('original_spell'); }
    public function level(): ?int { $v=$this->record['level']??null; return is_int($v)?$v:null; }
    public function school(): ?string { return $this->nullableString('school'); }
    public function origin(): string { return (string) ($this->record['origin'] ?? 'canonical'); }
    public function publicationStatus(): string { return (string) ($this->record['status'] ?? 'published'); }
    public function isStewardAuthored(): bool { return $this->origin() === 'steward'; }
    public function castingTime(): string { return (string) ($this->record['casting_time'] ?? ''); }
    public function range(): string { return (string) ($this->record['range'] ?? ''); }
    public function components(): string { return (string) ($this->record['components'] ?? ''); }
    public function duration(): string { return (string) ($this->record['duration'] ?? ''); }
    public function rulesText(): string { return (string) ($this->record['rules_text'] ?? $this->variantText()); }
    public function higherLevels(): string { return (string) ($this->record['higher_levels'] ?? ''); }
    public function ritual(): bool { return ! empty($this->record['ritual']); }
    public function concentration(): bool { return ! empty($this->record['concentration']); }
    public function rollKind(): ?string { return $this->nullableString('roll_kind'); }
    public function formula(): ?string { return $this->nullableString('formula'); }
    public function damageType(): ?string { return $this->nullableString('damage_type'); }
    public function saveAbility(): ?string { return $this->nullableString('save_ability'); }
    public function addCastingModifier(): bool { return ! empty($this->record['add_casting_modifier']); }
    public function spellAttack(): bool { return ! empty($this->record['spell_attack']); }

    /** @return array<int,string> */
    public function accessLabels(): array
    {
        return array_values(array_filter($this->record['access_labels'] ?? [], 'is_string'));
    }

    /** @return array<int,string> */
    public function sourceIssues(): array
    {
        return array_values(array_filter($this->record['source_issues'] ?? [], 'is_string'));
    }

    /** @return array<int,array<string,mixed>> */
    public function variants(): array { return $this->record['variants']; }

    public function mechanicsReady(): bool
    {
        return $this->publicationStatus() === 'published'
            && $this->level() !== null
            && $this->school() !== null
            && $this->accessLabels() !== []
            && trim($this->rulesText()) !== ''
            && trim($this->castingTime()) !== ''
            && trim($this->range()) !== ''
            && trim($this->duration()) !== '';
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'key'=>$this->key(),'name'=>$this->name(),'kind'=>$this->kind(),
            'original_spell'=>$this->originalSpell(),'level'=>$this->level(),'school'=>$this->school(),
            'access_labels'=>$this->accessLabels(),'source_issues'=>$this->sourceIssues(),
            'variant_count'=>count($this->variants()),'variants'=>$this->variants(),
            'origin'=>$this->origin(),'status'=>$this->publicationStatus(),
            'casting_time'=>$this->castingTime(),'range'=>$this->range(),'components'=>$this->components(),
            'duration'=>$this->duration(),'rules_text'=>$this->rulesText(),'higher_levels'=>$this->higherLevels(),
            'ritual'=>$this->ritual(),'concentration'=>$this->concentration(),
            'roll_kind'=>$this->rollKind(),'formula'=>$this->formula(),'damage_type'=>$this->damageType(),
            'save_ability'=>$this->saveAbility(),'add_casting_modifier'=>$this->addCastingModifier(),
            'spell_attack'=>$this->spellAttack(),'mechanics_ready'=>$this->mechanicsReady(),
        ];
    }

    private function nullableString(string $key): ?string
    {
        $value=$this->record[$key]??null;
        return is_string($value) && $value!=='' ? $value : null;
    }

    private function variantText(): string
    {
        return trim((string) ($this->record['variants'][0]['source_text'] ?? ''));
    }
}
