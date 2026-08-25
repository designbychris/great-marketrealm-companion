<?php
use GreatMarketrealmCompanion\Modules\Administration\Workshop\FolkWorkshop;
defined('ABSPATH') || exit;
$isNew = ! is_array($selectedFolk ?? null);
$folk = $isNew ? [] : $selectedFolk;
$status = $isNew ? FolkWorkshop::STATUS_DRAFT : (string) ($folk['status'] ?? FolkWorkshop::STATUS_DRAFT);
$value = static fn (string $key): string => (string) ($folk[$key] ?? '');
$heritageLines = [];
foreach ((array) ($folk['heritages'] ?? []) as $heritage) {
    if (is_array($heritage)) {
        $heritageLines[] = implode(' | ', [
            (string) ($heritage['name'] ?? ''),
            (string) ($heritage['description'] ?? ''),
            (string) ($heritage['identity'] ?? ''),
            (string) ($heritage['traits'] ?? ''),
        ]);
    }
}
$baseUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'folk-workshop'], admin_url('admin.php'));
?>
<div class="wrap gmrc-admin gmrc-canonical-steward gmrc-folk-workshop">
<header class="gmrc-canonical-steward__hero"><div><p class="gmrc-stewards-office__eyebrow">Steward-authored identity · Custom Folk Registry</p><h1>Folk &amp; Heritage Workshop</h1><p>Create playable Marketrealm peoples and Heritages without altering protected canonical Folk or requiring bespoke portrait artwork.</p></div><a class="button" href="<?php echo esc_url(add_query_arg(['page'=>'gmrc-stewards-office'],admin_url('admin.php')));?>">Back to Steward's Office</a></header>
<?php if (isset($_GET['gmrc_folk_workshop_saved'])) : ?><div class="notice notice-success"><p>Steward Folk saved.</p></div><?php endif; ?>
<?php if (isset($_GET['gmrc_folk_workshop_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_folk_workshop_error'])); ?></p></div><?php endif; ?>
<div class="gmrc-canonical-steward__workspace">
<aside class="gmrc-canonical-steward__register"><div class="gmrc-canonical-steward__register-head"><h2>Steward Folk</h2><a class="button button-primary" href="<?php echo esc_url($baseUrl); ?>">Add Folk</a></div><?php if ($stewardFolk === []) : ?><p>No custom Folk have been recorded yet.</p><?php endif; ?><div class="gmrc-canonical-steward__list"><?php foreach ($stewardFolk as $key => $data) : $url=add_query_arg(['page'=>'gmrc-stewards-office','section'=>'folk-workshop','folk'=>$key],admin_url('admin.php')); ?><a href="<?php echo esc_url($url); ?>"<?php echo ! $isNew && ($folk['key'] ?? '') === $key ? ' aria-current="page"' : ''; ?>><span class="gmrc-canonical-steward__thumb" aria-hidden="true">🍎</span><span><strong><?php echo esc_html((string) ($data['name'] ?? 'Untitled Folk')); ?></strong><small><?php echo esc_html(ucfirst((string) ($data['status'] ?? 'draft'))); ?></small></span></a><?php endforeach; ?></div></aside>
<main class="gmrc-canonical-steward__editor"><form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="gmrc_save_steward_folk"><input type="hidden" name="folk_key" value="<?php echo esc_attr($isNew ? '' : (string) $folk['key']); ?>"><?php wp_nonce_field('gmrc_save_steward_folk_' . ($isNew ? 'new' : (string) $folk['key']), 'gmrc_steward_folk_nonce'); ?><header><div><p class="gmrc-stewards-office__eyebrow"><?php echo $isNew ? 'New playable Folk' : 'Editing Steward Folk'; ?></p><h2><?php echo esc_html($isNew ? 'Untitled Folk' : (string) $folk['name']); ?></h2></div><span class="gmrc-stewards-office__status"><?php echo esc_html(ucfirst($status)); ?></span></header>
<div class="gmrc-canonical-steward__fields"><label><strong>Name</strong><input name="name" required value="<?php echo esc_attr($value('name')); ?>"></label><label><strong>Status</strong><select name="status"><?php foreach (['draft'=>'Draft','published'=>'Published','archived'=>'Archived'] as $key=>$label) : ?><option value="<?php echo esc_attr($key); ?>"<?php selected($status,$key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label><label><strong>Walking speed</strong><input type="number" name="speed" min="0" max="120" step="5" value="<?php echo esc_attr((string) ($folk['speed'] ?? 30)); ?>"></label><label><strong>Size</strong><select name="size"><?php foreach (['Small','Medium','Small or Medium'] as $size) : ?><option value="<?php echo esc_attr($size); ?>"<?php selected((string)($folk['size']??'Medium'),$size); ?>><?php echo esc_html($size); ?></option><?php endforeach; ?></select></label><label><strong>Creature type</strong><input name="creature_type" value="<?php echo esc_attr($value('creature_type') ?: 'Humanoid'); ?>"></label><label><strong>Darkvision</strong><input type="number" name="darkvision" min="0" max="300" step="5" value="<?php echo esc_attr((string) ($folk['darkvision'] ?? 0)); ?>"></label></div>
<label><strong>Folk description</strong><textarea name="description" rows="6"><?php echo esc_textarea($value('description')); ?></textarea></label>
<section class="gmrc-spell-workshop__mechanics">
<h3>Portrait identity</h3>
<label><strong>Default Folk portrait image URL</strong><input type="url" class="large-text" name="portrait_url" value="<?php echo esc_attr($value('portrait_url')); ?>" placeholder="https://…"></label>
<p class="description">Optional. Choose or upload a representative image in the WordPress Media Library and paste its file URL here. Characters of this Steward Folk use it as their safe default portrait instead of an unsuitable procedural anatomy. A Character's own custom portrait still takes priority.</p>
</section>
<?php
$mechanics = is_array($folk['mechanics'] ?? null) ? $folk['mechanics'] : [];
$abilityModifiers = is_array($mechanics['ability_modifiers'] ?? null) ? $mechanics['ability_modifiers'] : [];
?>
<section class="gmrc-spell-workshop__mechanics gmrc-folk-workshop__mechanics">
<h3>Playable Folk mechanics</h3>
<p>These structured grants are applied by the Character Builder. Keep descriptive lore in Traits; put executable Character rules here.</p>
<h4>Ability score improvements</h4>
<div class="gmrc-canonical-steward__fields">
<?php foreach (['strength'=>'Strength','dexterity'=>'Dexterity','constitution'=>'Constitution','intelligence'=>'Intelligence','wisdom'=>'Wisdom','charisma'=>'Charisma'] as $abilityKey=>$abilityLabel) : ?>
<label><strong><?php echo esc_html($abilityLabel); ?></strong><select name="ability_<?php echo esc_attr($abilityKey); ?>"><?php for ($bonus=0;$bonus<=4;$bonus++) : ?><option value="<?php echo esc_attr((string)$bonus); ?>"<?php selected((int)($abilityModifiers[$abilityKey]??0),$bonus); ?>><?php echo esc_html($bonus===0?'No increase':'+' . $bonus); ?></option><?php endfor; ?></select></label>
<?php endforeach; ?>
</div>
<div class="gmrc-canonical-steward__fields">
<label><strong>Granted skill proficiencies</strong><textarea name="skill_proficiencies" rows="5" placeholder="One canonical skill per line, e.g. persuasion"><?php echo esc_textarea(implode("\n",(array)($mechanics['skill_proficiencies']??[]))); ?></textarea></label>
<label><strong>Granted tool proficiencies</strong><textarea name="tool_proficiencies" rows="5" placeholder="One canonical tool identifier per line"><?php echo esc_textarea(implode("\n",(array)($mechanics['tool_proficiencies']??[]))); ?></textarea></label>
<label><strong>Automatic languages</strong><textarea name="automatic_languages" rows="5" placeholder="One canonical language per line, e.g. common&#10;piespeak"><?php echo esc_textarea(implode("\n",(array)($mechanics['automatic_languages']??[]))); ?></textarea></label>
<label><strong>Additional language choices</strong><input type="number" name="chosen_language_count" min="0" max="4" value="<?php echo esc_attr((string)($mechanics['chosen_language_count']??0)); ?>"><span class="description">Recorded now for the choice-aware registration bridge; automatic languages are applied immediately.</span></label>
<label><strong>Damage resistances</strong><textarea name="resistances" rows="5" placeholder="One damage type per line"><?php echo esc_textarea(implode("\n",(array)($mechanics['resistances']??[]))); ?></textarea></label>
</div>
</section>
<div class="gmrc-canonical-steward__fields"><label><strong>Languages</strong><textarea name="languages" rows="5" placeholder="One language per line"><?php echo esc_textarea(implode("\n", (array) ($folk['languages'] ?? []))); ?></textarea></label><label><strong>Traits / lore markers</strong><textarea name="traits" rows="5" placeholder="One trait per line"><?php echo esc_textarea(implode("\n", (array) ($folk['traits'] ?? []))); ?></textarea></label></div>
<section class="gmrc-spell-workshop__mechanics"><h3>Heritages</h3><p>One Heritage per line: <code>Name | description | identity | trait summary</code>. Published Heritages become selectable only beneath their Published parent Folk.</p><textarea name="heritages" rows="10" class="large-text code"><?php echo esc_textarea(implode("\n", $heritageLines)); ?></textarea><p class="description">Save newly written Heritage identities once. Their structured mechanical inheritance cards will then appear below.</p></section>
<?php if (! empty($folk['heritages'])) : ?>
<section class="gmrc-spell-workshop__mechanics gmrc-folk-workshop__heritage-mechanics">
<h3>Heritage mechanics &amp; inheritance</h3>
<p>Each Heritage adds these grants <strong>on top of</strong> its parent Folk. Leaving a field empty means the Heritage inherits the Folk without changing that rule.</p>
<?php foreach ((array) $folk['heritages'] as $heritage) :
    if (! is_array($heritage)) { continue; }
    $heritageKey = (string) ($heritage['key'] ?? '');
    $heritageMechanics = is_array($heritage['mechanics'] ?? null) ? $heritage['mechanics'] : [];
    $heritageAbilities = is_array($heritageMechanics['ability_modifiers'] ?? null) ? $heritageMechanics['ability_modifiers'] : [];
    $fieldBase = 'heritage_mechanics[' . $heritageKey . ']';
?>
<article class="gmrc-canonical-steward__panel gmrc-folk-workshop__heritage-card">
<h4><?php echo esc_html((string) ($heritage['name'] ?? 'Heritage')); ?></h4>
<p class="description">Inherited from <?php echo esc_html((string) ($folk['name'] ?? 'parent Folk')); ?> · <code><?php echo esc_html($heritageKey); ?></code></p>
<div class="gmrc-canonical-steward__fields">
<?php foreach (['strength'=>'Strength','dexterity'=>'Dexterity','constitution'=>'Constitution','intelligence'=>'Intelligence','wisdom'=>'Wisdom','charisma'=>'Charisma'] as $abilityKey=>$abilityLabel) : ?>
<label><strong><?php echo esc_html($abilityLabel); ?> addition</strong><select name="<?php echo esc_attr($fieldBase . '[ability_modifiers][' . $abilityKey . ']'); ?>"><?php for ($bonus=0;$bonus<=4;$bonus++) : ?><option value="<?php echo esc_attr((string)$bonus); ?>"<?php selected((int)($heritageAbilities[$abilityKey]??0),$bonus); ?>><?php echo esc_html($bonus===0?'No addition':'+' . $bonus); ?></option><?php endfor; ?></select></label>
<?php endforeach; ?>
<label><strong>Additional skill proficiencies</strong><textarea name="<?php echo esc_attr($fieldBase . '[skill_proficiencies]'); ?>" rows="4"><?php echo esc_textarea(implode("\n",(array)($heritageMechanics['skill_proficiencies']??[]))); ?></textarea></label>
<label><strong>Additional tool proficiencies</strong><textarea name="<?php echo esc_attr($fieldBase . '[tool_proficiencies]'); ?>" rows="4"><?php echo esc_textarea(implode("\n",(array)($heritageMechanics['tool_proficiencies']??[]))); ?></textarea></label>
<label><strong>Additional automatic languages</strong><textarea name="<?php echo esc_attr($fieldBase . '[automatic_languages]'); ?>" rows="4"><?php echo esc_textarea(implode("\n",(array)($heritageMechanics['automatic_languages']??[]))); ?></textarea></label>
<label><strong>Additional language choices</strong><input type="number" min="0" max="4" name="<?php echo esc_attr($fieldBase . '[chosen_language_count]'); ?>" value="<?php echo esc_attr((string)($heritageMechanics['chosen_language_count']??0)); ?>"></label>
<label><strong>Additional damage resistances</strong><textarea name="<?php echo esc_attr($fieldBase . '[resistances]'); ?>" rows="4"><?php echo esc_textarea(implode("\n",(array)($heritageMechanics['resistances']??[]))); ?></textarea></label>
<label><strong>Size override</strong><select name="<?php echo esc_attr($fieldBase . '[size]'); ?>"><option value="">Inherit parent size</option><?php foreach (['Tiny','Small','Medium','Large','Small or Medium'] as $heritageSize) : ?><option value="<?php echo esc_attr($heritageSize); ?>"<?php selected((string)($heritageMechanics['size']??''),$heritageSize); ?>><?php echo esc_html($heritageSize); ?></option><?php endforeach; ?></select></label>
<label><strong>Walking speed override</strong><input type="number" min="0" max="120" step="5" name="<?php echo esc_attr($fieldBase . '[speed]'); ?>" value="<?php echo esc_attr((string)($heritageMechanics['speed']??'')); ?>" placeholder="Inherit parent"></label>
<label><strong>Named Heritage traits</strong><textarea name="<?php echo esc_attr($fieldBase . '[features]'); ?>" rows="6" placeholder="Trait name | mechanical description"><?php $featureLines=[]; foreach ((array)($heritageMechanics['features']??[]) as $feature) { if (is_array($feature)) { $featureLines[]=(string)($feature['name']??'') . ' | ' . (string)($feature['description']??''); } } echo esc_textarea(implode("\n",$featureLines)); ?></textarea><span class="description">One named rule per line. These are shown verbatim in Heritage Guidance.</span></label>
<label><strong>Proficiency choices</strong><textarea name="<?php echo esc_attr($fieldBase . '[proficiency_choices]'); ?>" rows="5" placeholder="Flexible Logic | 1 | Acrobatics, Sleight of Hand"><?php $choiceLines=[]; foreach ((array)($heritageMechanics['proficiency_choices']??[]) as $choice) { if (is_array($choice)) { $choiceLines[]=(string)($choice['name']??'') . ' | ' . (int)($choice['choose']??1) . ' | ' . implode(', ',(array)($choice['from']??[])); } } echo esc_textarea(implode("\n",$choiceLines)); ?></textarea><span class="description">Name | number to choose | comma-separated options.</span></label>
</div>
<p class="description"><strong>Publication certification:</strong> named traits must have both a name and description; proficiency choices must provide enough options; size and speed overrides must use recognised Character Builder values.</p>
</article>
<?php endforeach; ?>
</section>
<?php endif; ?>
<label><strong>Private Steward notes</strong><textarea name="steward_notes" rows="4"><?php echo esc_textarea($value('steward_notes')); ?></textarea></label><?php submit_button($isNew ? 'Create Folk' : 'Save Folk'); ?></form></main>
</div></div>
<?php $deleteType='folk'; $deleteKey=$isNew?'':(string)($folk['key']??''); $deleteLabel=$isNew?'this Folk':(string)($folk['name']??'this Folk'); require GMRC_PATH . 'app/Modules/Administration/Views/_steward-delete.php'; ?>
