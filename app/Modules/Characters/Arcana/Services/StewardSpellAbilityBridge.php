<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\SharedSpellRegister;

defined('ABSPATH') || exit;

/** Projects mechanically complete published Steward spells into Character spell catalogues. */
final class StewardSpellAbilityBridge
{
    public function __construct(private ?SharedSpellRegister $spells=null){$this->spells??=new SharedSpellRegister();}
    /** @return array<int,ArcaneAbilityDefinition> */
    public function abilities(): array
    {
        $out=[];
        foreach($this->spells->stewardPublished() as $spell){
            if(!$spell->mechanicsReady())continue;
            $classes=array_values(array_unique(array_filter(array_map(static fn(string $label):string=>sanitize_key($label),$spell->accessLabels()))));
            if($classes===[])continue;
            $level=$spell->level()??0;
            $out[]=new ArcaneAbilityDefinition(
                $spell->key(),$spell->name(),$level===0?'cantrip':'spell',$classes,$spell->rulesText(),
                $spell->castingTime(),$spell->range(),$spell->duration(),$level===0?'At will':self::slotLabel($level),
                $spell->rollKind(),$spell->formula(),$spell->damageType(),$spell->saveAbility(),$spell->addCastingModifier(),$spell->spellAttack(),
                1,$level
            );
        }
        return $out;
    }
    private static function slotLabel(int $level): string
    {
        $suffix=match($level){1=>'st',2=>'nd',3=>'rd',default=>'th'}; return $level.$suffix.'-level slot';
    }
}
