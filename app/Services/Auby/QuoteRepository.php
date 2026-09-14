<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Auby;

final class QuoteRepository
{
    private QuoteCollection $quotes;

    /**
     * Create the quote repository.
     *
     * An empty injected collection is treated the same as no
     * collection so the built-in Auby quotes remain available
     * when the dependency container auto-resolves this class.
     */
    public function __construct(
        ?QuoteCollection $quotes = null
    ) {
        if (
            $quotes instanceof QuoteCollection
            && ! $quotes->isEmpty()
        ) {
            $this->quotes = $quotes;
    
            return;
        }
    
        $this->quotes = new QuoteCollection(
            $this->defaultQuotes()
        );
    }

    public function random(
        string $category = QuoteCategories::GENERAL
    ): Quote {
        $category = sanitize_key($category);

        if (!QuoteCategories::isValid($category)) {
            $category = QuoteCategories::GENERAL;
        }

        $quotes = $this->quotes->forCategory($category);

        if ($quotes->isEmpty()) {
            $quotes = $this->quotes->forCategory(
                QuoteCategories::GENERAL
            );
        }

        $quote = $quotes->random();

        if ($quote instanceof Quote) {
            return $quote;
        }

        return new Quote(
            'A fresh page awaits.',
            'Auby',
            QuoteCategories::GENERAL
        );
    }

    /**
     * Retrieve several different quotes from a category.
     *
     * @return array<int,Quote>
     */
    public function many(
        string $category,
        int $quantity = 3
    ): array {
        $quantity = max(
            1,
            min(10, $quantity)
        );
    
        $selected = [];
        $attempts = 0;
        $maximumAttempts = $quantity * 10;
    
        while (
            count($selected) < $quantity
            && $attempts < $maximumAttempts
        ) {
            $quote = $this->random(
                $category
            );
    
            $selected[
                $quote->id()
            ] = $quote;
    
            $attempts++;
        }
    
        return array_values(
            $selected
        );
    }

    public function all(): QuoteCollection
    {
        return $this->quotes;
    }

    /**
     * @return array<Quote>
     */
    private function defaultQuotes(): array
    {
        $quotes = [
            new Quote(
                __('Every page begins empty. That is what makes it full of possibility.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                __('The Guild Ledger remembers what hurried minds often forget.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                __('There is always room in the margin for one more story.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                __('A well-kept record is a kindness to those who come after us.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                __('Every hero begins with a name, and every legend begins with a page.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('A fresh name in the Register. Let us hope they remember to pack rope.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('The Guild remembers its heroes, even when the heroes misplace their maps.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('A character is measured by their choices, not merely by their level.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('The finest adventures often begin with someone writing down a very bad idea.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('Names have power. Titles mostly have paperwork.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                __('Half of cooking is confidence. The other half is butter.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                __('A recipe is simply a spell with clearer instructions.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                __('Never underestimate rosemary, patience, or a sufficiently large spoon.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                __('Measure carefully. Improvise confidently. Blame the oven sparingly.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                __('Some recipes nourish the body. The best ones become stories.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                __('You will swear you packed rope. The Pantry suggests otherwise.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                __('An organised Pantry is the first defence against unexpected turnips.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                __('If you cannot find it, look behind the cabbage.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                __('A full Pantry encourages bravery. An empty one encourages creativity.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                __('Never stand behind an angry Broccolop.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                __('A monster properly recorded is slightly less alarming the second time.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                __('Never judge a tomato by its skin, particularly when it has teeth.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                __('The Broccolop is perfectly harmless.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::BESTIARY,
                true,
                true,
                true,
                'Do not believe the sentence above.'
            ),

            new Quote(
                __('Most creatures prefer not to be catalogued while they are eating.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                __('Every campaign begins with a destination and immediately wanders elsewhere.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                __('Plans are useful. Adventurers are inventive.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                __('A good map shows where you meant to go. A good story records where you ended up.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                __('The Guild notices courage. It also notices kindness.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                __('Some victories deserve trumpets. Others deserve a quiet line of ink.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                __('I have been waiting a very long time to write this entry.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                __('Even enchanted Ledgers require the occasional adjustment.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::SETTINGS
            ),

            new Quote(
                __('Arrange the Ledger however you please. I shall try not to move anything.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::SETTINGS
            ),

            new Quote(
                __('A little organisation now prevents considerable muttering later.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::SETTINGS
            ),
            
            new Quote(
                __('Ah! A fresh page. I was wondering who we would be writing about today.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CREATOR,
                false,
                true
            ),
            
            new Quote(
                __('Every adventurer begins as an empty page. Fortunately, I have plenty of ink.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CREATOR
            ),
            
            new Quote(
                __('Choose carefully. The Ledger remembers everything. Except where I left my spectacles.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CREATOR,
                true,
                true,
                true,
                'They were on my head.'
            ),
            
            new Quote(
                __('Now that is a proper adventurer’s name.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_NAME
            ),
            
            new Quote(
                __('Names have power. They also make filing considerably easier.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_NAME
            ),
            
            new Quote(
                __('Excellent. I shall write that in my neatest handwriting.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_NAME
            ),
            
            new Quote(
                __('A fine heritage. The Archive has many stories about their people.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_RACE
            ),
            
            new Quote(
                __('An excellent choice. Every corner of the Marketrealm brings something special.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_RACE
            ),
            
            new Quote(
                __('Heritage recorded! I knew there was a reason I sharpened this quill.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_RACE
            ),
            
            new Quote(
                __('A noble calling. Or at least a very interesting one.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CLASS
            ),
            
            new Quote(
                __('That path should produce plenty of stories for the Ledger.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CLASS
            ),
            
            new Quote(
                __('Class recorded. I shall leave room for heroic deeds and minor administrative mishaps.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_CLASS
            ),
            
            new Quote(
                __('Wonderful! The Guild Ledger is ready to receive its newest adventurer.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_READY,
                false,
                true
            ),
            
            new Quote(
                __('Everything appears to be in order. That is unusually encouraging.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_READY
            ),
            
            new Quote(
                __('Name, heritage and calling—all recorded. The first page of the adventure awaits.', 'great-marketrealm-companion'),
                'Auby',
                QuoteCategories::CHARACTER_READY
            ),
        ];

        /**
         * Allows other parts of GMRC to register additional quotes.
         *
         * @param array<Quote> $quotes
         */
        $filteredQuotes = apply_filters(
            'gmrc_auby_quotes',
            $quotes
        );

        if (!is_array($filteredQuotes)) {
            return $quotes;
        }

        return array_values(
            array_filter(
                $filteredQuotes,
                static fn ($quote): bool =>
                    $quote instanceof Quote
            )
        );
    }
}
