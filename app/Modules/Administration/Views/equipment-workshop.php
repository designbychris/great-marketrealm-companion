<?php

use GreatMarketrealmCompanion\Modules\Administration\Workshop\EquipmentWorkshop;

defined('ABSPATH') || exit;
$records = is_array($stewardEquipment ?? null) ? $stewardEquipment : [];
$isNew = ! is_array($selectedEquipment ?? null);
$item = $isNew ? [] : $selectedEquipment;
$status = $isNew ? EquipmentWorkshop::STATUS_DRAFT : (string) ($item['status'] ?? EquipmentWorkshop::STATUS_DRAFT);
$baseUrl = add_query_arg(['page' => 'gmrc-stewards-office', 'section' => 'equipment-workshop'], admin_url('admin.php'));
$properties = implode(', ', is_array($item['properties'] ?? null) ? $item['properties'] : []);
?>
<div class="wrap gmrc-admin gmrc-canonical-steward gmrc-equipment-workshop">
<header class="gmrc-canonical-steward__hero"><div><p class="gmrc-stewards-office__eyebrow">Steward-authored content · Shared Armoury</p><h1>Equipment &amp; Item Workshop</h1><p>Create mundane Marketrealm equipment without changing protected Quartermaster records. Published items enter the shared Armoury and Character satchels; archived items remain resolvable for adventurers already carrying them.</p></div><a class="button" href="<?php echo esc_url($baseUrl); ?>">New item</a></header>
<?php if (isset($_GET['gmrc_equipment_workshop_saved'])) : ?><div class="notice notice-success is-dismissible"><p>The Steward item has been sealed.</p></div><?php endif; ?>
<?php if (isset($_GET['gmrc_equipment_workshop_error'])) : ?><div class="notice notice-error"><p><?php echo esc_html(rawurldecode((string) $_GET['gmrc_equipment_workshop_error'])); ?></p></div><?php endif; ?>
<div class="gmrc-canonical-steward__layout">
<aside class="gmrc-canonical-steward__register" aria-label="Steward equipment register"><h2>Steward creations</h2><p><?php echo esc_html((string) count($records)); ?> custom items recorded.</p><ul><?php foreach ($records as $key => $record) : ?><li><a href="<?php echo esc_url(add_query_arg('item', (string) $key, $baseUrl)); ?>"><strong><?php echo esc_html((string) ($record['name'] ?? $key)); ?></strong><span><?php echo esc_html(ucfirst((string) ($record['status'] ?? 'draft')) . ' · ' . ucfirst((string) ($record['category'] ?? 'item'))); ?></span></a></li><?php endforeach; ?></ul></aside>
<main class="gmrc-canonical-steward__editor">
<form class="gmrc-canonical-steward__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
<input type="hidden" name="action" value="gmrc_save_steward_equipment"><input type="hidden" name="item_key" value="<?php echo esc_attr($isNew ? '' : (string) $item['key']); ?>"><?php wp_nonce_field('gmrc_save_steward_equipment_' . ($isNew ? 'new' : (string) $item['key']), 'gmrc_steward_equipment_nonce'); ?>
<header><div><p class="gmrc-stewards-office__eyebrow"><?php echo $isNew ? 'New shared item' : 'Editing Steward item'; ?></p><h2><?php echo esc_html($isNew ? 'Untitled Item' : (string) $item['name']); ?></h2></div><span class="gmrc-stewards-office__status"><?php echo esc_html(ucfirst($status)); ?></span></header>
<div class="gmrc-canonical-steward__fields">
<label><span>Name</span><input name="name" value="<?php echo esc_attr((string) ($item['name'] ?? '')); ?>" required></label>
<label><span>Publication</span><select name="status"><?php foreach ([EquipmentWorkshop::STATUS_DRAFT, EquipmentWorkshop::STATUS_PUBLISHED, EquipmentWorkshop::STATUS_ARCHIVED] as $value) : ?><option value="<?php echo esc_attr($value); ?>"<?php selected($status, $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label>
<label><span>Category</span><select name="category"><option value="">Choose…</option><?php foreach (EquipmentWorkshop::CATEGORIES as $value) : ?><option value="<?php echo esc_attr($value); ?>"<?php selected((string) ($item['category'] ?? ''), $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label>
<label><span>Weight (lb)</span><input name="weight" type="number" min="0" max="1000" step="0.01" value="<?php echo esc_attr((string) ($item['weight'] ?? '')); ?>"></label>
<label class="gmrc-canonical-steward__field--wide"><span>Description</span><textarea name="description" rows="4"><?php echo esc_textarea((string) ($item['description'] ?? '')); ?></textarea></label>
<label><span>Equipment slot</span><select name="equip_slot"><option value="">Not equippable</option><?php foreach (EquipmentWorkshop::EQUIP_SLOTS as $value) : ?><option value="<?php echo esc_attr($value); ?>"<?php selected((string) ($item['equip_slot'] ?? ''), $value); ?>><?php echo esc_html(ucwords(str_replace('-', ' ', $value))); ?></option><?php endforeach; ?></select></label>
<label><span>Damage die</span><input name="damage_die" placeholder="1d8" value="<?php echo esc_attr((string) ($item['damage_die'] ?? '')); ?>"></label>
<label><span>Damage type</span><select name="damage_type"><option value="">None</option><?php foreach (EquipmentWorkshop::DAMAGE_TYPES as $value) : ?><option value="<?php echo esc_attr($value); ?>"<?php selected((string) ($item['damage_type'] ?? ''), $value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label>
<label><span>Reach / range</span><input name="range" placeholder="Melee · 5 ft" value="<?php echo esc_attr((string) ($item['range'] ?? '')); ?>"></label>
<label><span>Armour base</span><input name="armour_base" type="number" min="1" max="30" value="<?php echo esc_attr(isset($item['armour_base']) ? (string) $item['armour_base'] : ''); ?>"></label>
<label><span>Dexterity cap</span><input name="dexterity_cap" type="number" min="0" max="10" value="<?php echo esc_attr(isset($item['dexterity_cap']) ? (string) $item['dexterity_cap'] : ''); ?>"></label>
<label><span>Armour bonus</span><input name="armour_bonus" type="number" min="-10" max="10" value="<?php echo esc_attr((string) ($item['armour_bonus'] ?? 0)); ?>"></label>
<label class="gmrc-canonical-steward__field--wide"><span>Properties</span><input name="properties" placeholder="finesse, light, thrown" value="<?php echo esc_attr($properties); ?>"><small>Comma-separated mechanical property keys.</small></label>
<label class="gmrc-canonical-steward__field--wide"><span>Private Steward notes</span><textarea name="steward_notes" rows="4"><?php echo esc_textarea((string) ($item['steward_notes'] ?? '')); ?></textarea></label>
</div>
<div class="gmrc-canonical-steward__actions"><?php submit_button($isNew ? 'Create item' : 'Save item', 'primary', 'submit', false); ?><p class="description">Drafts stay private. Published items become available to the Guild Armoury and Character inventory. Archived items are hidden from new selection but preserved for existing satchels.</p></div>
</form></main></div></div>

<?php $deleteType = 'equipment'; $deleteKey = $isNew ? '' : (string) ($item['key'] ?? ''); $deleteLabel = $isNew ? 'this item' : (string) ($item['name'] ?? 'this item'); require GMRC_PATH . 'app/Modules/Administration/Views/_steward-delete.php'; ?>
