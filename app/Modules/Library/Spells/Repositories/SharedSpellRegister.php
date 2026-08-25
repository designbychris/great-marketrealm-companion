<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\Library\Spells\Repositories;

use GreatMarketrealmCompanion\Modules\Administration\Workshop\SpellWorkshop;
use GreatMarketrealmCompanion\Modules\Library\Spells\Models\SpellRecord;

defined('ABSPATH') || exit;

/** Effective Spell Register: protected canon plus published Steward creations. */
final class SharedSpellRegister
{
    public function __construct(private ?CanonicalSpellRegister $canonical=null, private ?SpellWorkshop $workshop=null)
    {
        $this->canonical??=new CanonicalSpellRegister(); $this->workshop??=new SpellWorkshop($this->canonical);
    }
    /** @return array<int,SpellRecord> */
    public function all(): array { return array_values($this->records()); }
    public function find(string $key): ?SpellRecord { return $this->records()[sanitize_key($key)]??null; }
    /** @return array<int,SpellRecord> */
    public function byKind(string $kind): array{return array_values(array_filter($this->all(),fn(SpellRecord $s)=>$s->kind()===$kind));}
    /** @return array<int,SpellRecord> */
    public function byLevel(?int $level): array{return array_values(array_filter($this->all(),fn(SpellRecord $s)=>$s->level()===$level));}
    /** @return array<int,SpellRecord> */
    public function withSourceIssues(): array{return array_values(array_filter($this->all(),fn(SpellRecord $s)=>$s->sourceIssues()!==[]));}
    public function sourceVariantCount(): int{return array_sum(array_map(fn(SpellRecord $s)=>count($s->variants()),$this->all()));}
    /** @return array<int,SpellRecord> */
    public function stewardPublished(): array{return array_values(array_filter($this->all(),fn(SpellRecord $s)=>$s->isStewardAuthored()));}
    /** @return array<string,SpellRecord> */
    private function records(): array
    {
        $records=[]; foreach($this->canonical->all() as $spell)$records[$spell->key()]=$spell;
        foreach($this->workshop->published() as $key=>$data){$spell=SpellRecord::fromArray($data);if(!isset($records[$spell->key()]))$records[$spell->key()]=$spell;}
        return $records;
    }
}
