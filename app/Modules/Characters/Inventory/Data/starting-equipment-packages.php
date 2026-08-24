<?php

declare(strict_types=1);
defined('ABSPATH') || exit;

/*
 * Certified Companion starting kits. Every entry resolves to an existing
 * Armoury item ID. The source Handbook does not currently provide a complete
 * structured equipment table for every Calling, so these packages are kept
 * explicitly separate from immutable Handbook identity and can be Stewarded.
 */
return [
    ['id'=>'artificer-field','class'=>'artificer','label'=>'Field Inventor Kit','items'=>['light-crossbow'=>1,'dagger'=>2,'leather-armour'=>1,'explorers-pack'=>1]],
    ['id'=>'artificer-workshop','class'=>'artificer','label'=>'Workshop Expedition Kit','items'=>['handaxe'=>1,'dagger'=>1,'studded-leather'=>1,'dungeoneers-pack'=>1]],
    ['id'=>'barbarian-greataxe','class'=>'barbarian','label'=>'Greataxe Raider Kit','items'=>['greataxe'=>1,'handaxe'=>2,'explorers-pack'=>1]],
    ['id'=>'barbarian-maul','class'=>'barbarian','label'=>'Maul Breaker Kit','items'=>['maul'=>1,'javelin'=>4,'explorers-pack'=>1]],
    ['id'=>'bard-duellist','class'=>'bard','label'=>'Duellist Performer Kit','items'=>['rapier'=>1,'dagger'=>1,'leather-armour'=>1,'entertainers-pack'=>1]],
    ['id'=>'bard-traveller','class'=>'bard','label'=>'Travelling Minstrel Kit','items'=>['longsword'=>1,'dagger'=>1,'leather-armour'=>1,'diplomats-pack'=>1]],
    ['id'=>'cleric-mace','class'=>'cleric','label'=>'Temple Guardian Kit','items'=>['mace'=>1,'scale-mail'=>1,'shield'=>1,'explorers-pack'=>1]],
    ['id'=>'cleric-hammer','class'=>'cleric','label'=>'Pilgrim Warhammer Kit','items'=>['warhammer'=>1,'chain-mail'=>1,'shield'=>1,'explorers-pack'=>1]],
    ['id'=>'druid-scimitar','class'=>'druid','label'=>'Scimitar Warden Kit','items'=>['scimitar'=>1,'leather-armour'=>1,'shield'=>1,'explorers-pack'=>1]],
    ['id'=>'druid-spear','class'=>'druid','label'=>'Spear Wanderer Kit','items'=>['spear'=>1,'leather-armour'=>1,'explorers-pack'=>1]],
    ['id'=>'fighter-sword','class'=>'fighter','label'=>'Sword & Shield Kit','items'=>['longsword'=>1,'chain-mail'=>1,'shield'=>1,'light-crossbow'=>1,'dungeoneers-pack'=>1]],
    ['id'=>'fighter-archer','class'=>'fighter','label'=>'Archer Kit','items'=>['longbow'=>1,'arrows-20'=>1,'quiver'=>1,'leather-armour'=>1,'shortsword'=>1,'explorers-pack'=>1]],
    ['id'=>'monk-staff','class'=>'monk','label'=>'Quarterstaff Pilgrim Kit','items'=>['quarterstaff'=>1,'dart'=>10,'explorers-pack'=>1]],
    ['id'=>'monk-sword','class'=>'monk','label'=>'Shortsword Pilgrim Kit','items'=>['shortsword'=>1,'dart'=>10,'explorers-pack'=>1]],
    ['id'=>'paladin-sword','class'=>'paladin','label'=>'Sword & Shield Oath Kit','items'=>['longsword'=>1,'chain-mail'=>1,'shield'=>1,'javelin'=>5,'explorers-pack'=>1]],
    ['id'=>'paladin-greatsword','class'=>'paladin','label'=>'Great Weapon Oath Kit','items'=>['greatsword'=>1,'chain-mail'=>1,'javelin'=>5,'explorers-pack'=>1]],
    ['id'=>'ranger-archer','class'=>'ranger','label'=>'Archer Scout Kit','items'=>['longbow'=>1,'arrows-20'=>1,'quiver'=>1,'shortsword'=>2,'leather-armour'=>1,'explorers-pack'=>1]],
    ['id'=>'ranger-skirmisher','class'=>'ranger','label'=>'Skirmisher Kit','items'=>['scimitar'=>2,'shortbow'=>1,'arrows-20'=>1,'quiver'=>1,'leather-armour'=>1,'explorers-pack'=>1]],
    ['id'=>'rogue-burglar','class'=>'rogue','label'=>'Burglar Kit','items'=>['rapier'=>1,'shortbow'=>1,'arrows-20'=>1,'quiver'=>1,'leather-armour'=>1,'burglars-pack'=>1,'dagger'=>2]],
    ['id'=>'rogue-duellist','class'=>'rogue','label'=>'Duellist Kit','items'=>['shortsword'=>1,'hand-crossbow'=>1,'leather-armour'=>1,'dungeoneers-pack'=>1,'dagger'=>2]],
    ['id'=>'sorcerer-crossbow','class'=>'sorcerer','label'=>'Crossbow Wanderer Kit','items'=>['light-crossbow'=>1,'dagger'=>2,'dungeoneers-pack'=>1]],
    ['id'=>'sorcerer-spear','class'=>'sorcerer','label'=>'Spear Wanderer Kit','items'=>['spear'=>1,'dagger'=>2,'explorers-pack'=>1]],
    ['id'=>'warlock-crossbow','class'=>'warlock','label'=>'Pact Crossbow Kit','items'=>['light-crossbow'=>1,'dagger'=>2,'leather-armour'=>1,'dungeoneers-pack'=>1]],
    ['id'=>'warlock-blade','class'=>'warlock','label'=>'Pact Blade Kit','items'=>['shortsword'=>1,'dagger'=>2,'leather-armour'=>1,'explorers-pack'=>1]],
    ['id'=>'wizard-scholar','class'=>'wizard','label'=>'Scholar Expedition Kit','items'=>['quarterstaff'=>1,'dagger'=>1,'scholars-pack'=>1]],
    ['id'=>'wizard-explorer','class'=>'wizard','label'=>'Explorer Mage Kit','items'=>['dagger'=>1,'explorers-pack'=>1]],
];
