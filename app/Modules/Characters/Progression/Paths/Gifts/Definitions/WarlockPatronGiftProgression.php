<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Definitions;

use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts\PathGiftProgressionDefinitionInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Marketrealm Warlock Patron Gift progression.
 *
 * Each Patron grants a defining first gift with the Level 1 contract, then
 * matures at Levels 6, 10 and 14.
 */
final class WarlockPatronGiftProgression implements PathGiftProgressionDefinitionInterface
{
    /** @var array<string,array{label:string,gifts:array<int,array<string,mixed>>}> */
    private const PATRONS = [
        'pact-of-the-mascot' => [
            'label' => 'Pact of the Mascot',
            'gifts' => [
                [
                    'key' => 'smiling-sponsorship',
                    'label' => 'Smiling Sponsorship',
                    'level' => 1,
                    'summary' =>
                        'Your impossible Patron marks you as an official representative of something nobody remembers agreeing to advertise.',
                    'detail' =>
                        'The Mascot’s first gift is presence. Your smile, posture and supernatural confidence carry the strange authority of a brand that seems to recognise itself through you, even when nobody can explain what the brand actually sells.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'brand-ambassador',
                    'label' => 'Brand Ambassador',
                    'level' => 6,
                    'summary' =>
                        'Turn attention into leverage as your Patron’s cheerful influence becomes much harder to ignore.',
                    'detail' =>
                        'By now the bargain has taught you how to occupy a room like a living campaign. Allies notice your confidence, enemies notice your certainty, and neutral observers often realise too late that they have somehow become part of the presentation.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'impossible-endorsement',
                    'label' => 'Impossible Endorsement',
                    'level' => 10,
                    'summary' =>
                        'Carry supernatural credibility that can make even an absurd promise feel briefly plausible.',
                    'detail' =>
                        'The Mascot no longer merely lends you charm; it lends you the unsettling weight of institutional recognition. Your declarations can feel as though they were approved by a vast invisible department that nobody is brave enough to contact.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'mascot-unmasked',
                    'label' => 'Mascot Unmasked',
                    'level' => 14,
                    'summary' =>
                        'Let the cheerful disguise slip just enough to reveal the ancient thing smiling underneath.',
                    'detail' =>
                        'At the height of the Patron bond, you understand that the costume was never the source of the power. Your presence can now carry both halves of the bargain at once: impossible friendliness and the terrible certainty that something behind it has been watching for a very long time.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-forgotten-freezer' => [
            'label' => 'The Forgotten Freezer',
            'gifts' => [
                [
                    'key' => 'frostbound-whisper',
                    'label' => 'Frostbound Whisper',
                    'level' => 1,
                    'summary' =>
                        'Hear the distant hum of your Patron in cold rooms, abandoned aisles and moments of unnatural stillness.',
                    'detail' =>
                        'Your contract begins as a sound at the edge of hearing: compressor hum, ice settling and something vast shifting behind a door that should have remained sealed. Cold places begin to feel less empty when you enter them.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'cold-storage',
                    'label' => 'Cold Storage',
                    'level' => 6,
                    'summary' =>
                        'Learn the Freezer’s terrible patience and preserve your resolve when others begin to crack.',
                    'detail' =>
                        'The Forgotten Freezer does not hurry. Its gift teaches you to hold fear, anger and magical pressure in suspension until the correct moment arrives, like something preserved far beyond its expected date.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'door-left-open',
                    'label' => 'Door Left Open',
                    'level' => 10,
                    'summary' =>
                        'Carry a trace of the impossible cold beyond the threshold where it belongs.',
                    'detail' =>
                        'Your connection is now strong enough that the Patron’s atmosphere follows you. Rooms seem quieter, breath seems sharper and enemies may feel the instinctive discomfort of standing too close to a freezer door that has been open for far too long.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'heart-of-the-forgotten-freezer',
                    'label' => 'Heart of the Forgotten Freezer',
                    'level' => 14,
                    'summary' =>
                        'Become a living fragment of the cold, forgotten place at the centre of your bargain.',
                    'detail' =>
                        'The contract reaches a point where distance matters less. Something of the Freezer now exists within your supernatural identity: patient, preserved and frighteningly difficult to thaw once its attention has settled on a problem.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-spoilfather' => [
            'label' => 'The Spoilfather',
            'gifts' => [
                [
                    'key' => 'first-bloom-of-rot',
                    'label' => 'First Bloom of Rot',
                    'level' => 1,
                    'summary' =>
                        'Recognise decay not merely as an ending, but as a process your Patron understands intimately.',
                    'detail' =>
                        'The Spoilfather’s first lesson is uncomfortable: freshness is temporary, but transformation is inevitable. You begin to notice weakness, age and deterioration with the instinct of someone who has been taught where every perfect surface will eventually split.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'patient-decay',
                    'label' => 'Patient Decay',
                    'level' => 6,
                    'summary' =>
                        'Learn to let pressure accumulate until an enemy’s own weaknesses begin doing your work for you.',
                    'detail' =>
                        'The Spoilfather rarely demands haste. Your pact matures into a talent for attrition, patience and trusting that small failures become serious failures when given enough time and the correct encouragement.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'feast-of-spoilage',
                    'label' => 'Feast of Spoilage',
                    'level' => 10,
                    'summary' =>
                        'Draw confidence from collapse, corruption and plans that are beginning to come apart.',
                    'detail' =>
                        'Where others see only ruin, your Patron sees abundance. The deterioration of hostile plans, protections and certainty becomes something you can read and exploit with increasingly disturbing enthusiasm.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'spoilfathers-heir',
                    'label' => 'Spoilfather’s Heir',
                    'level' => 14,
                    'summary' =>
                        'Stand as a favoured agent of inevitable decline, carrying the Patron’s patience into the living Marketrealm.',
                    'detail' =>
                        'At full Patron maturity, you no longer merely witness decay. You understand its rhythm well enough to embody the Spoilfather’s philosophy: everything changes, everything weakens, and anything pretending otherwise is merely waiting to be corrected.',
                    'mode' => 'automatic',
                ],
            ],
        ],
        'the-sugar-fiend' => [
            'label' => 'The Sugar Fiend',
            'gifts' => [
                [
                    'key' => 'first-taste',
                    'label' => 'First Taste',
                    'level' => 1,
                    'summary' =>
                        'Receive the first exhilarating sample of power that made the Sugar Fiend’s bargain impossible to refuse.',
                    'detail' =>
                        'The contract begins with reward rather than threat. Magic feels bright, immediate and deliciously easy for one dangerous moment, which is precisely why the small print becomes so difficult to remember.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sugar-rush-bargain',
                    'label' => 'Sugar Rush Bargain',
                    'level' => 6,
                    'summary' =>
                        'Turn temptation into sudden momentum when the pact promises that one more indulgence will solve everything.',
                    'detail' =>
                        'The Sugar Fiend teaches you how quickly desire can become action. Your magic increasingly carries the emotional rhythm of a sugar rush: confidence first, consequences later, and another glittering promise waiting just beyond the crash.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'glazed-temptation',
                    'label' => 'Glazed Temptation',
                    'level' => 10,
                    'summary' =>
                        'Make dangerous choices look beautiful enough that hesitation briefly feels unreasonable.',
                    'detail' =>
                        'Your Patron’s influence becomes sophisticated rather than merely excessive. Power arrives polished, sparkling and perfectly presented, hiding its sharper edges beneath a finish designed to make refusal feel almost impolite.',
                    'mode' => 'automatic',
                ],
                [
                    'key' => 'sweetest-ruin',
                    'label' => 'Sweetest Ruin',
                    'level' => 14,
                    'summary' =>
                        'Embody the final lesson of the Sugar Fiend: the most dangerous bargains are often the ones people desperately want to accept.',
                    'detail' =>
                        'At the height of the pact, sweetness and danger become inseparable in your supernatural identity. You carry the intoxicating certainty of a reward so appealing that everyone in the room instinctively wonders what they would be willing to trade for another taste.',
                    'mode' => 'automatic',
                ],
            ],
        ],
    ];

    private function __construct(
        private string $path
    ) {
        if (! isset(self::PATRONS[$path])) {
            throw new InvalidArgumentException(
                'Unknown Warlock Patron gift progression.'
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
            array_keys(self::PATRONS)
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
        return self::PATRONS[
            $this->path
        ]['label'];
    }

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array
    {
        return self::PATRONS[
            $this->path
        ]['gifts'];
    }
}
