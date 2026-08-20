<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Canon Druid Circle gifts from III.12.10B.
 *
 * This slice certifies the supplied 2 / 6 / 10 / 14 Circle progression.
 * Resource expenditure, Wild Shape interactions and active Circle actions
 * remain for later Druid active-play slices.
 */
final class DruidCircleGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const CIRCLES = [
        'circle-of-eating-fresh' => [
            'label' => 'Circle of Eating Fresh',
            'gifts' => [
                ['key'=>'crisp-aura','label'=>'Crisp Aura','level'=>2,'summary'=>'Allies within 10 feet regain 1 HP per round in natural terrain; once per long rest expand the aura to 30 feet for 1 minute.','detail'=>'Allies within 10 feet of you regain 1 HP per round when standing in natural terrain. You can expand this aura to 30 feet for 1 minute once per long rest.','mode'=>'automatic'],
                ['key'=>'natures-label','label'=>'Nature’s Label','level'=>6,'summary'=>'Magically inspect an item or creature for organic purity, disease, spoilage or magical taint; Detect Poison and Disease is always prepared.','detail'=>'You can magically inspect any item or creature to know if it is organic, diseased, spoiled, or magically tainted, as Detect Poison and Disease. Detect Poison and Disease is always prepared.','mode'=>'automatic'],
                ['key'=>'hydroponic-revival','label'=>'Hydroponic Revival','level'=>10,'summary'=>'Cure Wounds also grants advantage on the target’s next attack or saving throw.','detail'=>'When you cast Cure Wounds, the target gains advantage on their next attack or saving throw.','mode'=>'automatic'],
                ['key'=>'preservative-purge','label'=>'Preservative Purge','level'=>14,'summary'=>'Once per long rest, unleash a 30-foot cleansing storm that purges toxins, conditions and corruption.','detail'=>'Once per long rest, you unleash a cleansing storm that purges toxins, conditions, and corruption in a 30-foot radius.','mode'=>'automatic'],
            ],
        ],
        'circle-of-the-groveflame' => [
            'label'=>'Circle of the Groveflame',
            'gifts'=>[
                ['key'=>'spiceburst','label'=>'Spiceburst','level'=>2,'summary'=>'Add your Wisdom modifier to one damage roll of a fire-damage spell.','detail'=>'When you cast a spell that deals fire damage, you can add your Wisdom modifier to one of the damage rolls.','mode'=>'automatic'],
                ['key'=>'spiceberry','label'=>'Spiceberry','level'=>6,'summary'=>'Goodberry becomes Spiceberry: each berry also grants 1 temporary HP and frightened immunity for 1 hour.','detail'=>'Your Goodberry becomes Spiceberry. When consumed, each berry grants 1 temporary HP in addition to healing and immunity to the frightened condition for 1 hour.','mode'=>'automatic'],
                ['key'=>'flame-frond-form','label'=>'Flame Frond Form','level'=>10,'summary'=>'Wild Shape may become a Spice Basilisk with spicy breath once per short rest using the DM’s stat block.','detail'=>'When using Wild Shape, you may choose to transform into a Spice Basilisk, a fire-based beast that can emit spicy breath once per short rest (DM’s stat block).','mode'=>'automatic'],
                ['key'=>'scorching-bloom','label'=>'Scorching Bloom','level'=>14,'summary'=>'Once per long rest erupt spice flowers for 4d8 fire in 20 feet with a possible stun; pungent flame spells can also briefly blind.','detail'=>'Once per long rest, cause an eruptive wave of spice flowers. All enemies within 20 feet take 4d8 fire damage and must make a Constitution saving throw or be stunned until the end of their next turn. Additionally, once per short or long rest, you can cause your flame spells to carry a scent so pungent that creatures hit must succeed on a Constitution save or become blinded until the end of your next turn.','mode'=>'automatic'],
            ],
        ],
        'circle-of-the-deep-soil' => [
            'label'=>'Circle of the Deep Soil',
            'gifts'=>[
                ['key'=>'buried-memory','label'=>'Buried Memory','level'=>2,'summary'=>'Speak with dead plants and recall the last 24 hours remembered by a tree or root system.','detail'=>'Speak with dead plants and recall the last 24 hours of memory from any tree or root system.','mode'=>'automatic'],
                ['key'=>'earthen-hold','label'=>'Earthen Hold','level'=>6,'summary'=>'React to erupt vines and reduce a creature’s speed to 0 on a failed Constitution save.','detail'=>'Gain a reaction to cause vines to erupt, reducing a creature’s speed to 0. A Constitution save resists the effect.','mode'=>'automatic'],
                ['key'=>'soil-communion','label'=>'Soil Communion','level'=>10,'summary'=>'No longer require food or water and become immune to petrified and restrained.','detail'=>'You no longer require food or water and are immune to being petrified or restrained.','mode'=>'automatic'],
                ['key'=>'living-earthquake','label'=>'Living Earthquake','level'=>14,'summary'=>'Once per long rest, create a 20-foot tremor for 1 minute; creatures in the area face a DC 16 Dexterity save or fall prone each round.','detail'=>'Once per long rest, cause a 20-foot tremor for 1 minute. Creatures in the area make a DC 16 Dexterity save or fall prone each round.','mode'=>'automatic'],
            ],
        ],
        'circle-of-the-compost' => [
            'label'=>'Circle of the Compost',
            'gifts'=>[
                ['key'=>'rotbound-affinity-and-compost-surge','label'=>'Rotbound Affinity & Compost Surge','level'=>2,'summary'=>'Resist poison and necrotic damage, commune with vermin and fungi, gain Druidcraft and Infestation, and react to a nearby creature falling to 0 HP.','detail'=>'Gain resistance to poison and necrotic damage. Speak with and understand vermin and fungi as if under Speak with Animals and Plants, and learn Druidcraft and Infestation if needed. When a creature within 30 feet drops to 0 HP, use your reaction either to regain 1d6 + Wisdom modifier HP or cause another creature within 10 feet of the fallen target to take necrotic damage equal to your Druid level. Compost Surge has proficiency bonus uses per long rest.','mode'=>'automatic'],
                ['key'=>'mulchborn','label'=>'Mulchborn','level'=>6,'summary'=>'Bonus-action rooting grants half cover and forced-movement immunity while adjacent creatures risk 2d8 poison damage.','detail'=>'As a bonus action, partially root yourself for up to 1 minute or until you move. Gain half cover and cannot be moved against your will. A creature ending its turn adjacent to you must make a Constitution save (DC = 8 + Wisdom modifier + proficiency bonus) or take 2d8 poison damage. Recharge on short rest.','mode'=>'automatic'],
                ['key'=>'bloom-of-decay','label'=>'Bloom of Decay','level'=>10,'summary'=>'Once per long rest create a 20-foot compost bloom with difficult terrain, 4d6 poison and 1d6 ally healing; also gain slot-free Blight and Insect Plague uses.','detail'=>'Once per long rest, create Bloom of Decay in a 20-foot radius around you for 1 minute. Enemies treat it as difficult terrain and make a Constitution save at the start of their turn or take 4d6 poison damage. Allies in the bloom regain 1d6 HP at the start of their turn. You can also cast Blight and Insect Plague once per long rest without using a spell slot.','mode'=>'automatic'],
                ['key'=>'avatar-of-the-rotten-grove','label'=>'Avatar of the Rotten Grove','level'=>14,'summary'=>'Become poison-immune and spend Wild Shape to become a Large Compost Elemental for 1 minute.','detail'=>'Gain immunity to poison damage and the poisoned condition. As an action, spend a Wild Shape use to become a Compost Elemental for 1 minute: Large size; temporary HP equal to twice your Druid level; +2 AC; resistance to all physical damage; two Mulch Slam attacks for 2d10 bludgeoning + 2d6 poison; enemies within 10 feet at the start of their turn make a Constitution save or are slowed until end of turn. A 10-foot aura of buzzing flies and mold lightly obscures vision.','mode'=>'automatic'],
            ],
        ],
        'circle-of-curdle' => [
            'label'=>'Circle of Curdle',
            'gifts'=>[
                ['key'=>'spoilage-touch-and-aura-of-curdling','label'=>'Spoilage Touch & Aura of Curdling','level'=>2,'summary'=>'Learn Infestation and Chill Touch, spoil or preserve food and water at will, and impose Constitution-save disadvantage near your Wild Shape.','detail'=>'Learn the Infestation and Chill Touch cantrips and may spoil or preserve food or water at will. While in Wild Shape, creatures within 5 feet of you have disadvantage on Constitution saving throws due to your noxious microbial presence.','mode'=>'automatic'],
                ['key'=>'rot-within','label'=>'Rot Within','level'=>6,'summary'=>'A creature failing a save against your Druid spell becomes Curdled: -1 AC and disadvantage on one saving throw chosen each round.','detail'=>'When a creature fails a saving throw against your Druid spell, it becomes Curdled for 1 minute. Curdled creatures take -1 to AC and have disadvantage on one saving throw of your choice, chosen each round. The effect ends on a successful Constitution save.','mode'=>'automatic'],
                ['key'=>'animate-spoil','label'=>'Animate Spoil','level'=>10,'summary'=>'Once per long rest animate a 10-foot-radius patch of moldy or rotting food into an allied CR 4 Curd Golem.','detail'=>'Once per long rest, animate a 10-foot-radius patch of moldy or rotting food into a Curd Golem. It acts as an ally with the stats of a CR 4 creature, using Elemental Myrmidon as the base template with spoiled flavouring.','mode'=>'automatic'],
                ['key'=>'bacteria-bloom','label'=>'Bacteria Bloom','level'=>14,'summary'=>'Release beneficial spores: allies within 20 feet gain Druid level + Wisdom modifier temporary HP while enemies save or become poisoned for 1 minute.','detail'=>'As an action, release a cloud of beneficial spores. All allies within 20 feet gain temporary hit points equal to your Druid level + Wisdom modifier, and enemies must save or become poisoned for 1 minute.','mode'=>'automatic'],
            ],
        ],
        'circle-of-the-churn' => [
            'label'=>'Circle of the Churn',
            'gifts'=>[
                ['key'=>'frozen-curd','label'=>'Frozen Curd','level'=>2,'summary'=>'Use Wild Shape to create a 10-minute frost-covered Curd Form with fire resistance, ice/snow mobility and cold unarmed attacks.','detail'=>'When you use Wild Shape, you can instead create a Curd Form, transforming into a semi-solid, frost-covered being. Gain resistance to fire damage, immunity to difficult terrain made of ice or snow, and unarmed attacks deal cold instead of bludgeoning damage. The form lasts 10 minutes and can be used once per long rest or by expending a Wild Shape use.','mode'=>'automatic'],
                ['key'=>'blessing-of-the-creammother','label'=>'Blessing of the Creammother','level'=>6,'summary'=>'Your healing spells also grant 1d6 temporary HP and resistance to the next source of fire or necrotic damage.','detail'=>'When a creature within 30 feet regains hit points due to a spell you cast, it also gains 1d6 temporary hit points and resistance to the next source of fire or necrotic damage.','mode'=>'automatic'],
                ['key'=>'glacial-growth','label'=>'Glacial Growth','level'=>10,'summary'=>'Create a 10-foot icy difficult-terrain zone within 60 feet for 1 minute; enemies initially save against being knocked prone.','detail'=>'As an action, designate a 10-foot radius within 60 feet. It becomes icy difficult terrain for 1 minute. Enemies in the area when it appears must make a Strength saving throw or be knocked prone. Uses equal your proficiency bonus per long rest.','mode'=>'automatic'],
                ['key'=>'true-churnform','label'=>'True Churnform','level'=>14,'summary'=>'Once per long rest become primordial dairy elemental power for 1 minute, gaining recurring temporary HP and maximized healing/cold spells.','detail'=>'As an action, become a being of primordial dairy elemental power for 1 minute. Gain temporary HP equal to your Druid level at the start of each of your turns. Spells you cast that restore HP or deal cold damage are maximized. Recharge on long rest.','mode'=>'automatic'],
            ],
        ],
    ];

    public static function forCircle(string $circleKey): self
    {
        $key=sanitize_key($circleKey);
        if (!isset(self::CIRCLES[$key])) {
            throw new InvalidArgumentException('Unknown Druid Circle.');
        }
        return new self($key);
    }

    /** @return array<int,self> */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn(string $key): self => new self($key),
            array_keys(self::CIRCLES)
        );
    }

    private function __construct(private string $circleKey) {}

    public function supports(string $pathKey): bool
    {
        return sanitize_key($pathKey) === $this->circleKey;
    }

    public function pathLabel(): string
    {
        return self::CIRCLES[$this->circleKey]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::CIRCLES[$this->circleKey]['gifts'];
    }
}
