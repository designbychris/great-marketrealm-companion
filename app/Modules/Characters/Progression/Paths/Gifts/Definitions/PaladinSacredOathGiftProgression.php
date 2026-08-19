<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Great Marketrealm Sacred Oath gift progression.
 *
 * III.12.6B establishes automatic Oath Gifts at 3 / 7 / 15 / 20.
 * Active resource expenditure and scene-dependent mechanics remain the
 * responsibility of later Paladin slices.
 */
final class PaladinSacredOathGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const OATHS = [
        'oath-of-inventory' => [
            'label' => 'Oath of Inventory',
            'gifts' => [
                [
                    'key' => 'sacred-stocktake',
                    'label' => 'Sacred Stocktake',
                    'level' => 3,
                    'summary' =>
                        'Bring order to confusion by identifying what the Fellowship still has, still needs and must protect.',
                    'detail' =>
                        'Your oath begins with stewardship. You learn to treat supplies, allies and promises as entries in one sacred ledger: each deserves attention, accountability and protection from careless loss.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'keeper-of-the-ledger',
                    'label' => 'Keeper of the Ledger',
                    'level' => 7,
                    'summary' =>
                        'Your steady presence helps the Fellowship hold together when resources and plans begin to fray.',
                    'detail' =>
                        'You become the adventurer people trust when a situation needs structure. The Oath of Inventory turns organisation into reassurance and makes preparation feel like a form of protection.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'nothing-unaccounted',
                    'label' => 'Nothing Unaccounted',
                    'level' => 15,
                    'summary' =>
                        'Notice losses, substitutions and dangerous omissions before they become disasters.',
                    'detail' =>
                        'Mastery of stewardship sharpens your awareness. Missing details stand out like a blank line in a completed manifest, giving the oath its characteristic vigilance.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-inventory',
                    'label' => 'Grand Inventory',
                    'level' => 20,
                    'summary' =>
                        'Stand as the ultimate keeper of everything entrusted to your Fellowship.',
                    'detail' =>
                        'At full mastery, your oath becomes a living promise that people, provisions and responsibilities will not simply vanish into chaos while you still stand to account for them.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-the-colonel' => [
            'label' => 'Oath of the Colonel',
            'gifts' => [
                [
                    'key' => 'eleven-herbs-command',
                    'label' => 'Eleven-Herbs Command',
                    'level' => 3,
                    'summary' =>
                        'Lead with seasoned confidence and turn decisive orders into immediate battlefield momentum.',
                    'detail' =>
                        'The Colonel’s oath begins with unmistakable command presence. Your Fellowship learns that when you call the next move, you intend to be standing at the front when it happens.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'bucket-line',
                    'label' => 'Bucket Line',
                    'level' => 7,
                    'summary' =>
                        'Hold allies together in a disciplined frontline that refuses to scatter under pressure.',
                    'detail' =>
                        'Your leadership becomes contagious. Nearby companions find it easier to keep formation, trust the plan and meet danger as one organised serving rather than eight separate problems.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'original-recipe-resolve',
                    'label' => 'Original Recipe Resolve',
                    'level' => 15,
                    'summary' =>
                        'Return to the fundamentals of courage when clever plans and fashionable tactics fail.',
                    'detail' =>
                        'Experience teaches you that dependable principles survive changing circumstances. You become exceptionally difficult to shake when duty, loyalty and a clear objective remain.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-colonels-service',
                    'label' => 'Grand Colonel’s Service',
                    'level' => 20,
                    'summary' =>
                        'Become a legendary field commander whose arrival makes the whole Fellowship stand taller.',
                    'detail' =>
                        'At full mastery, martial confidence and sacred leadership become inseparable. Your presence turns a desperate battle into something that suddenly looks organised enough to win.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-the-creamfather' => [
            'label' => 'Oath of the Creamfather',
            'gifts' => [
                [
                    'key' => 'velvet-benediction',
                    'label' => 'Velvet Benediction',
                    'level' => 3,
                    'summary' =>
                        'Wrap allies in a calm, reassuring presence that makes fear and pain feel less absolute.',
                    'detail' =>
                        'The Creamfather teaches that hospitality can be sacred armour. Your first gift is the ability to make safety feel present even before danger has actually passed.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'table-for-all',
                    'label' => 'Table for All',
                    'level' => 7,
                    'summary' =>
                        'Turn protection into fellowship by making nearby companions feel included in your sacred care.',
                    'detail' =>
                        'Your oath widens from personal kindness into communal responsibility. Nobody under your protection should feel like an unwanted guest at the table.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'richness-of-spirit',
                    'label' => 'Richness of Spirit',
                    'level' => 15,
                    'summary' =>
                        'Meet cruelty with composure so deep that intimidation struggles to find a foothold.',
                    'detail' =>
                        'Your gentleness becomes formidable rather than fragile. The more an enemy tries to sour the moment, the clearer your own sacred confidence becomes.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'creamfather-ascendant',
                    'label' => 'Creamfather Ascendant',
                    'level' => 20,
                    'summary' =>
                        'Become a radiant host and protector whose presence transforms the emotional centre of a battlefield.',
                    'detail' =>
                        'At full mastery, hospitality becomes sovereign power: allies feel sheltered, enemies feel judged and the space around you seems to remember what true welcome should mean.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-aroma' => [
            'label' => 'Oath of Aroma',
            'gifts' => [
                [
                    'key' => 'scent-of-intent',
                    'label' => 'Scent of Intent',
                    'level' => 3,
                    'summary' =>
                        'Read the atmosphere of an encounter before obvious words or weapons reveal what is coming.',
                    'detail' =>
                        'Your oath teaches that intent leaves traces. Mood, fear, confidence and corruption all alter the invisible character of a place, and you learn to notice those changes quickly.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfumed-presence',
                    'label' => 'Perfumed Presence',
                    'level' => 7,
                    'summary' =>
                        'Carry a memorable sacred presence that steadies allies and makes enemies increasingly aware of you.',
                    'detail' =>
                        'Your aura becomes difficult to ignore. Even when you say nothing, the battlefield seems to register that you have arrived and chosen where you stand.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'memory-in-the-air',
                    'label' => 'Memory in the Air',
                    'level' => 15,
                    'summary' =>
                        'Leave an impression strong enough that places and people seem to remember your passage.',
                    'detail' =>
                        'The Oath of Aroma matures into a strange mastery of association. Presence, warning and reassurance linger after the immediate moment has passed.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'incense-of-judgement',
                    'label' => 'Incense of Judgement',
                    'level' => 20,
                    'summary' =>
                        'Transform the space around you into a sacred atmosphere of unmistakable purpose.',
                    'detail' =>
                        'At full mastery, your oath becomes almost environmental. Allies breathe easier, enemies feel exposed and the battlefield itself seems scented with the certainty of your judgement.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-clearance' => [
            'label' => 'Oath of Clearance',
            'gifts' => [
                [
                    'key' => 'final-markdown',
                    'label' => 'Final Markdown',
                    'level' => 3,
                    'summary' =>
                        'Recognise when delay has stopped being caution and the time has come to finish what was started.',
                    'detail' =>
                        'The Oath of Clearance is not reckless; it is decisive. You learn to identify stale battles, exhausted plans and dangers that become worse the longer everyone refuses to close them.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'make-room',
                    'label' => 'Make Room',
                    'level' => 7,
                    'summary' =>
                        'Create space for allies by confronting whatever is blocking the Fellowship’s next move.',
                    'detail' =>
                        'Your oath becomes a force for transition. Where others see an immovable obstacle, you see something occupying space that the future urgently requires.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'everything-must-go',
                    'label' => 'Everything Must Go',
                    'level' => 15,
                    'summary' =>
                        'Commit fully when an encounter reaches the point where half-measures only prolong the danger.',
                    'detail' =>
                        'Years of difficult endings make your judgement frighteningly clear. When the moment truly demands resolution, hesitation falls away.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'closing-bell',
                    'label' => 'Closing Bell',
                    'level' => 20,
                    'summary' =>
                        'Become the sacred herald of endings that make space for something better to begin.',
                    'detail' =>
                        'At full mastery, your presence carries the certainty of closing time. What must end becomes difficult to postpone, and what survives gains room to become something new.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-seasoning' => [
            'label' => 'Oath of Seasoning',
            'gifts' => [
                [
                    'key' => 'measured-pinch',
                    'label' => 'Measured Pinch',
                    'level' => 3,
                    'summary' =>
                        'Learn that the right amount of force, mercy or courage matters more than simply applying more.',
                    'detail' =>
                        'Your oath begins with judgement. A seasoned Paladin studies what a moment lacks and avoids overwhelming a problem with the wrong answer.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'balanced-palate',
                    'label' => 'Balanced Palate',
                    'level' => 7,
                    'summary' =>
                        'Adapt your support to the Fellowship instead of demanding that every ally fight exactly as you do.',
                    'detail' =>
                        'You become sensitive to imbalance in both plans and people. The Oath of Seasoning rewards adjustment, complementing strengths and correcting excess.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'perfect-blend',
                    'label' => 'Perfect Blend',
                    'level' => 15,
                    'summary' =>
                        'Combine martial discipline, sacred power and Fellowship support into one carefully judged response.',
                    'detail' =>
                        'By now, balance is instinctive. You no longer think in isolated ingredients; you see how every decision changes the whole encounter.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'master-seasoner',
                    'label' => 'Master Seasoner',
                    'level' => 20,
                    'summary' =>
                        'Become a living expression of sacred balance, enhancing everything around you without losing your own identity.',
                    'detail' =>
                        'At full mastery, the Fellowship feels more complete in your presence. Your judgement brings out strengths, tempers excess and makes disparate talents work together.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-carbonation' => [
            'label' => 'Oath of Carbonation',
            'gifts' => [
                [
                    'key' => 'sacred-fizz',
                    'label' => 'Sacred Fizz',
                    'level' => 3,
                    'summary' =>
                        'Release a burst of uplifting sacred energy that turns hesitation into immediate momentum.',
                    'detail' =>
                        'The Oath of Carbonation refuses to let courage go flat. Your first gift is energetic, bright and designed to get a stalled Fellowship moving again.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'pressure-builds',
                    'label' => 'Pressure Builds',
                    'level' => 7,
                    'summary' =>
                        'Hold mounting sacred energy until the right moment lets it burst into decisive action.',
                    'detail' =>
                        'You learn that contained pressure can be useful rather than dangerous. Patience and enthusiasm stop being opposites and become parts of the same rhythm.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'never-go-flat',
                    'label' => 'Never Go Flat',
                    'level' => 15,
                    'summary' =>
                        'Recover your fighting spirit with astonishing speed when circumstances try to drain the Fellowship’s energy.',
                    'detail' =>
                        'By this point, discouragement has trouble sticking. You have learned how to restore momentum before a bad moment becomes the mood of the entire encounter.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'bottled-thunder',
                    'label' => 'Bottled Thunder',
                    'level' => 20,
                    'summary' =>
                        'Become an explosive font of sacred momentum whose energy transforms the pace of battle.',
                    'detail' =>
                        'At full mastery, the pressure behind your oath becomes legendary. When released, it feels as though the whole Fellowship has been shaken awake at once.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'oath-of-the-cleaver-saint' => [
            'label' => 'Oath of the Cleaver Saint',
            'gifts' => [
                [
                    'key' => 'consecrated-edge',
                    'label' => 'Consecrated Edge',
                    'level' => 3,
                    'summary' =>
                        'Treat disciplined weapon work as an act of protection rather than mere violence.',
                    'detail' =>
                        'The Cleaver Saint’s oath begins with precise force. Your weapon is not sacred because it cuts; it is sacred because you have sworn to place that edge between danger and those under your protection.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'butchers-ward',
                    'label' => 'Butcher’s Ward',
                    'level' => 7,
                    'summary' =>
                        'Hold dangerous ground with a severe protective presence that discourages enemies from passing you.',
                    'detail' =>
                        'You learn to make your position matter. The safest route to your allies increasingly appears to be the one that does not pass through you.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'saints-resolve',
                    'label' => 'Saint’s Resolve',
                    'level' => 15,
                    'summary' =>
                        'Remain precise and purposeful even when battle becomes ugly enough to invite cruelty.',
                    'detail' =>
                        'The oath’s true test is restraint. Your resolve lets you wield brutal tools without surrendering the sacred reason you took them up.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'grand-cleaver-saint',
                    'label' => 'Grand Cleaver Saint',
                    'level' => 20,
                    'summary' =>
                        'Become a legendary guardian of sacred steel whose presence defines the frontline.',
                    'detail' =>
                        'At full mastery, protection and martial force become one vocation. Allies rally behind you while enemies understand exactly where the Fellowship has chosen to make its stand.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::OATHS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Paladin Sacred Oath gift progression.'
            );
        }
    }

    public static function for(
        string $path
    ): self {
        return new self(
            sanitize_key($path)
        );
    }

    /** @return array<int,self> */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn (string $path): self =>
                self::for($path),
            array_keys(self::OATHS)
        );
    }

    public function supports(
        string $pathKey
    ): bool {
        return sanitize_key($pathKey)
            === $this->path;
    }

    public function pathKey(): string
    {
        return $this->path;
    }

    public function pathLabel(): string
    {
        return self::OATHS[
            $this->path
        ]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::OATHS[
            $this->path
        ]['gifts'];
    }
}
