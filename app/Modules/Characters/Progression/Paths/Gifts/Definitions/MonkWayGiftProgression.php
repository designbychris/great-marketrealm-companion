<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Great Marketrealm Monastic Way gift progression.
 *
 * III.12.5B establishes descriptive, automatic Way gifts at 3/6/11/17.
 * Mechanical spend actions are intentionally deferred to later Monk slices.
 */
final class MonkWayGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const WAYS = [
        'way-of-the-spun-cloud' => [
            'label' => 'Way of the Spun Cloud',
            'gifts' => [
                ['key'=>'sugarwind-step','label'=>'Sugarwind Step','level'=>3,'summary'=>'Drift through openings with spun-sugar lightness.','detail'=>'Your Way teaches footwork that values graceful repositioning, elusive angles and never remaining where pressure expects you to be.','mode'=>'automatic'],
                ['key'=>'cloudthread-evasion','label'=>'Cloudthread Evasion','level'=>6,'summary'=>'Turn defensive movement into an almost weightless escape.','detail'=>'Your movement becomes harder to predict, helping the Way feel increasingly untouchable when the battlefield begins to close around you.','mode'=>'automatic'],
                ['key'=>'gossamer-current','label'=>'Gossamer Current','level'=>11,'summary'=>'Carry momentum through the battlefield like a strand caught on the breeze.','detail'=>'The Way develops continuous motion, linking repositioning and martial pressure into one flowing rhythm.','mode'=>'automatic'],
                ['key'=>'living-spun-storm','label'=>'Living Spun Storm','level'=>17,'summary'=>'Become the centre of an elegant, impossible storm of motion.','detail'=>'At full mastery, the Spun Cloud turns mobility into identity: enemies struggle to decide where you end and the moving battlefield begins.','mode'=>'automatic'],
            ],
        ],
        'way-of-the-neon-crunch' => [
            'label' => 'Way of the Neon Crunch',
            'gifts' => [
                ['key'=>'crackling-opening','label'=>'Crackling Opening','level'=>3,'summary'=>'Begin exchanges with vivid, explosive tempo.','detail'=>'Your discipline arrives with bright confidence and sudden pressure, favouring decisive openings and energetic combinations.','mode'=>'automatic'],
                ['key'=>'afterimage-crunch','label'=>'Afterimage Crunch','level'=>6,'summary'=>'Leave attention a fraction behind your actual movement.','detail'=>'Fast changes of angle and dazzling motion make your next position harder to read than your last.','mode'=>'automatic'],
                ['key'=>'electric-rhythm','label'=>'Electric Rhythm','level'=>11,'summary'=>'Sustain a relentless cadence of movement and striking pressure.','detail'=>'The Way teaches you to keep combat moving at a pace that feels natural to you and increasingly uncomfortable to everyone else.','mode'=>'automatic'],
                ['key'=>'neon-overdrive','label'=>'Neon Overdrive','level'=>17,'summary'=>'Reach a spectacular peak of speed, colour and martial confidence.','detail'=>'At full mastery, the Neon Crunch becomes controlled spectacle: every movement announces danger a moment too late for opponents to answer it.','mode'=>'automatic'],
            ],
        ],
        'way-of-the-vacuum-seal' => [
            'label' => 'Way of the Vacuum Seal',
            'gifts' => [
                ['key'=>'sealed-stance','label'=>'Sealed Stance','level'=>3,'summary'=>'Remove wasted movement and close obvious openings.','detail'=>'Your discipline favours compact defence, careful positioning and the calm certainty of leaving enemies less room to exploit.','mode'=>'automatic'],
                ['key'=>'pressure-lock','label'=>'Pressure Lock','level'=>6,'summary'=>'Make nearby space feel increasingly difficult for enemies to use.','detail'=>'The Way develops controlled pressure, rewarding patient positioning and denying easy routes through your defence.','mode'=>'automatic'],
                ['key'=>'preserved-focus','label'=>'Preserved Focus','level'=>11,'summary'=>'Hold disciplined concentration through prolonged danger.','detail'=>'Your training preserves composure under strain, keeping your martial decisions precise when a fight becomes chaotic.','mode'=>'automatic'],
                ['key'=>'perfect-seal','label'=>'Perfect Seal','level'=>17,'summary'=>'Become a near-flawless expression of contained martial pressure.','detail'=>'At full mastery, wasted motion disappears and opponents feel every available opening close before they can use it.','mode'=>'automatic'],
            ],
        ],
        'way-of-the-simmering-soul' => [
            'label' => 'Way of the Simmering Soul',
            'gifts' => [
                ['key'=>'banked-heat','label'=>'Banked Heat','level'=>3,'summary'=>'Keep inner power controlled until the moment it matters.','detail'=>'Your Way teaches patience: discipline is not hurried, but held at a steady simmer until a deliberate release becomes possible.','mode'=>'automatic'],
                ['key'=>'steady-boil','label'=>'Steady Boil','level'=>6,'summary'=>'Remain composed as pressure builds around you.','detail'=>'Sustained danger feeds rather than disrupts your focus, giving the Way its characteristic calm under mounting strain.','mode'=>'automatic'],
                ['key'=>'rolling-soul','label'=>'Rolling Soul','level'=>11,'summary'=>'Turn accumulated composure into powerful martial momentum.','detail'=>'The simmer becomes a rolling rhythm, allowing patient control to transition naturally into decisive action.','mode'=>'automatic'],
                ['key'=>'cauldron-heart','label'=>'Cauldron Heart','level'=>17,'summary'=>'Carry an immense reserve of controlled inner heat.','detail'=>'At full mastery, your calm no longer suggests passivity; it suggests a tremendous force being held exactly where you intend it.','mode'=>'automatic'],
            ],
        ],
        'way-of-the-whirling-utensil' => [
            'label' => 'Way of the Whirling Utensil',
            'gifts' => [
                ['key'=>'cutlery-cadence','label'=>'Cutlery Cadence','level'=>3,'summary'=>'Turn kitchen-tool rhythm into flowing martial combinations.','detail'=>'The Way treats utensil-like movement as disciplined choreography, filling nearby space with precise flourishes and sudden changes of direction.','mode'=>'automatic'],
                ['key'=>'silverware-circle','label'=>'Silverware Circle','level'=>6,'summary'=>'Control the space around you with continuous sweeping motion.','detail'=>'Your combinations widen into a defensive and offensive rhythm that makes careless approaches increasingly uncomfortable.','mode'=>'automatic'],
                ['key'=>'banquet-tempest','label'=>'Banquet Tempest','level'=>11,'summary'=>'Link rapid flourishes into a sustained storm of martial pressure.','detail'=>'The Way becomes less like a sequence of techniques and more like one uninterrupted performance of controlled motion.','mode'=>'automatic'],
                ['key'=>'grand-service','label'=>'Grand Service','level'=>17,'summary'=>'Conduct the battlefield like the final service of a legendary feast.','detail'=>'At full mastery, every flourish has purpose and every nearby opening seems to belong to your rhythm.','mode'=>'automatic'],
            ],
        ],
        'way-of-the-spongecake-soul' => [
            'label' => 'Way of the Spongecake Soul',
            'gifts' => [
                ['key'=>'yielding-crumb','label'=>'Yielding Crumb','level'=>3,'summary'=>'Give under pressure without surrendering your structure.','detail'=>'Your Way teaches flexible defence: absorb the shape of a difficult moment, then recover your stance instead of meeting everything rigidly.','mode'=>'automatic'],
                ['key'=>'spring-back','label'=>'Spring Back','level'=>6,'summary'=>'Recover quickly after being forced out of your preferred rhythm.','detail'=>'Like a well-made sponge returning to form, your discipline favours resilience and the ability to re-enter the fight after disruption.','mode'=>'automatic'],
                ['key'=>'layered-resilience','label'=>'Layered Resilience','level'=>11,'summary'=>'Let repeated pressure reveal deeper reserves rather than weakness.','detail'=>'The Way develops a deceptively durable core, making flexibility and persistence increasingly difficult to separate.','mode'=>'automatic'],
                ['key'=>'uncrushable-soul','label'=>'Uncrushable Soul','level'=>17,'summary'=>'Become impossibly difficult to keep compressed or defeated.','detail'=>'At full mastery, softness becomes strength: pressure may change your shape for a moment, but it cannot persuade you to stay down.','mode'=>'automatic'],
            ],
        ],
    ];

    private function __construct(private string $path)
    {
        if (! isset(self::WAYS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Monk Monastic Way gift progression.'
            );
        }
    }

    public static function for(string $path): self
    {
        return new self(sanitize_key($path));
    }

    /** @return array<int,self> */
    public static function allDefinitions(): array
    {
        return array_map(
            static fn (string $path): self => self::for($path),
            array_keys(self::WAYS)
        );
    }

    public function supports(string $pathKey): bool
    {
        return sanitize_key($pathKey) === $this->path;
    }

    public function pathKey(): string
    {
        return $this->path;
    }

    public function pathLabel(): string
    {
        return self::WAYS[$this->path]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::WAYS[$this->path]['gifts'];
    }
}
