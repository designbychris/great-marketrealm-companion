<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Administration\Workshop;

use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\CanonicalSpellRegister;
use RuntimeException;

defined('ABSPATH') || exit;

/** Persistent Steward-authored spells kept separate from protected Handbook canon. */
final class SpellWorkshop
{
    public const OPTION = 'gmrc_steward_spells';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';
    public const ACCESS = ['Artificer','Bard','Cleric','Druid','Paladin','Ranger','Sorcerer','Warlock','Wizard'];

    public function __construct(private ?CanonicalSpellRegister $canonical = null)
    {
        $this->canonical ??= new CanonicalSpellRegister();
    }

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        $records = \function_exists('get_option') ? \get_option(self::OPTION, []) : [];
        return is_array($records) ? $records : [];
    }

    /** @return array<string,array<string,mixed>> */
    public function published(): array
    {
        return array_filter($this->all(), static fn(array $r): bool => ($r['status'] ?? '') === self::STATUS_PUBLISHED);
    }

    /** @return array<string,mixed>|null */
    public function find(string $key): ?array
    {
        $records=$this->all(); $key=sanitize_key($key);
        return isset($records[$key]) && is_array($records[$key]) ? $records[$key] : null;
    }

    /** @param array<string,mixed> $input */
    public function save(string $key, array $input): string
    {
        $records=$this->all();
        $existing=$key!=='' ? ($records[sanitize_key($key)]??null) : null;
        $name=sanitize_text_field((string)($input['name']??''));
        if ($name==='') throw new RuntimeException('A Steward spell requires a name.');

        $key=is_array($existing) ? sanitize_key($key) : $this->uniqueKey($name,$records);
        $status=sanitize_key((string)($input['status']??self::STATUS_DRAFT));
        if (!in_array($status,[self::STATUS_DRAFT,self::STATUS_PUBLISHED,self::STATUS_ARCHIVED],true)) $status=self::STATUS_DRAFT;

        $level=$this->nullableLevel($input['level']??null);
        $access=$this->accessLabels($input['access_labels']??[]);
        $rules=sanitize_textarea_field((string)($input['rules_text']??''));
        $casting=sanitize_text_field((string)($input['casting_time']??''));
        $range=sanitize_text_field((string)($input['range']??''));
        $duration=sanitize_text_field((string)($input['duration']??''));
        $school=sanitize_key((string)($input['school']??''));
        if ($status===self::STATUS_PUBLISHED) {
            if ($level===null || $school==='' || $access===[] || $rules==='' || $casting==='' || $range==='' || $duration==='') {
                throw new RuntimeException('Published Steward spells require level, school, class access, casting time, range, duration and rules text.');
            }
        }

        $rollKind=sanitize_key((string)($input['roll_kind']??''));
        if (!in_array($rollKind,['damage','healing'],true)) $rollKind='';
        $save=sanitize_key((string)($input['save_ability']??''));
        if (!in_array($save,['strength','dexterity','constitution','intelligence','wisdom','charisma'],true)) $save='';

        $records[$key]=[
            'key'=>$key,'origin'=>'steward','status'=>$status,'name'=>$name,'kind'=>'marketrealm-original',
            'original_spell'=>null,'level'=>$level,'school'=>$school!==''?$school:null,'access_labels'=>$access,'source_issues'=>[],
            'casting_time'=>$casting,'range'=>$range,'components'=>sanitize_text_field((string)($input['components']??'')),
            'duration'=>$duration,'ritual'=>!empty($input['ritual']),'concentration'=>!empty($input['concentration']),
            'rules_text'=>$rules,'higher_levels'=>sanitize_textarea_field((string)($input['higher_levels']??'')),
            'roll_kind'=>$rollKind!==''?$rollKind:null,'formula'=>sanitize_text_field((string)($input['formula']??'')) ?: null,
            'damage_type'=>sanitize_key((string)($input['damage_type']??'')) ?: null,'save_ability'=>$save!==''?$save:null,
            'add_casting_modifier'=>!empty($input['add_casting_modifier']),'spell_attack'=>!empty($input['spell_attack']),
            'steward_notes'=>sanitize_textarea_field((string)($input['steward_notes']??'')),
            'variants'=>[['source_variant'=>'steward-workshop','level'=>$level,'school'=>$school!==''?$school:null,'access_labels'=>$access,'source_text'=>$rules]],
            'updated_at'=>gmdate('c'),
        ];
        update_option(self::OPTION,$records,false);
        return $key;
    }

    /** @return array<int,string> */
    private function accessLabels(mixed $input): array
    {
        $values=is_array($input)?$input:[]; $allowed=array_combine(array_map('sanitize_key',self::ACCESS),self::ACCESS); $out=[];
        foreach($values as $value){$key=sanitize_key((string)$value);if(isset($allowed[$key]))$out[$key]=$allowed[$key];}
        return array_values($out);
    }

    private function uniqueKey(string $name,array $records): string
    {
        $base=sanitize_key($name)?:'spell'; $key='steward-spell-'.$base; $i=2;
        while(isset($records[$key]) || $this->canonical->find($key)!==null) $key='steward-spell-'.$base.'-'.$i++;
        return $key;
    }

    private function nullableLevel(mixed $value): ?int
    {
        if($value===''||$value===null)return null; $level=(int)$value; return $level>=0&&$level<=9?$level:null;
    }
}
