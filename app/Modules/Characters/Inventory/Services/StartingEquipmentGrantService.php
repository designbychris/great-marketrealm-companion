<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Inventory\Services;

use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\CharacterInventoryRepository;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Repositories\StartingEquipmentPackageRegister;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

final class StartingEquipmentGrantService
{
    public function __construct(
        private readonly ?StartingEquipmentPackageRegister $packages = null,
        private readonly ?CharacterInventoryRepository $inventories = null
    ) {}

    public function grant(CharacterId $characterId, string $classKey, string $packageId): ?string
    {
        $register = $this->packages ?? new StartingEquipmentPackageRegister();
        $package = $register->find($packageId);
        if ($package === null || $package->classKey() !== sanitize_key($classKey)) {
            $package = $register->defaultForClass($classKey);
        }
        if ($package === null) { return null; }

        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty();
        foreach ($package->items() as $itemId => $quantity) {
            if ($catalogue->find($itemId) !== null) {
                $inventory = $inventory->add($itemId, max(1, (int) $quantity));
            }
        }
        ($this->inventories ?? new CharacterInventoryRepository())->save($characterId, $inventory);

        if (function_exists('update_post_meta') && function_exists('get_posts')) {
            $posts = \get_posts(['post_type'=>'gmrc_character','post_status'=>'publish','posts_per_page'=>1,'author'=>\get_current_user_id(),'meta_key'=>'_gmrc_character_id','meta_value'=>$characterId->value()]);
            $post = $posts[0] ?? null;
            if ($post instanceof \WP_Post) { \update_post_meta($post->ID, '_gmrc_starting_equipment_package', $package->id()); }
        }
        return $package->id();
    }
}
