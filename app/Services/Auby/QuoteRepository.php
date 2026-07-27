<?php

declare(strict_types=1);

namespace GMRC\Services\Auby;

final class QuoteRepository
{
    private QuoteCollection $quotes;

    public function __construct(?QuoteCollection $quotes = null)
    {
        $this->quotes = $quotes ?? new QuoteCollection(
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
                'Every page begins empty. That is what makes it full of possibility.',
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                'The Guild Ledger remembers what hurried minds often forget.',
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                'There is always room in the margin for one more story.',
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                'A well-kept record is a kindness to those who come after us.',
                'Auby',
                QuoteCategories::GENERAL
            ),

            new Quote(
                'Every hero begins with a name, and every legend begins with a page.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'A fresh name in the Register. Let us hope they remember to pack rope.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'The Guild remembers its heroes, even when the heroes misplace their maps.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'A character is measured by their choices, not merely by their level.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'The finest adventures often begin with someone writing down a very bad idea.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'Names have power. Titles mostly have paperwork.',
                'Auby',
                QuoteCategories::REGISTER
            ),

            new Quote(
                'Half of cooking is confidence. The other half is butter.',
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                'A recipe is simply a spell with clearer instructions.',
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                'Never underestimate rosemary, patience, or a sufficiently large spoon.',
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                'Measure carefully. Improvise confidently. Blame the oven sparingly.',
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                'Some recipes nourish the body. The best ones become stories.',
                'Auby',
                QuoteCategories::RECIPES
            ),

            new Quote(
                'You will swear you packed rope. The Pantry suggests otherwise.',
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                'An organised Pantry is the first defence against unexpected turnips.',
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                'If you cannot find it, look behind the cabbage.',
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                'A full Pantry encourages bravery. An empty one encourages creativity.',
                'Auby',
                QuoteCategories::PANTRY
            ),

            new Quote(
                'Never stand behind an angry Broccolop.',
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                'A monster properly recorded is slightly less alarming the second time.',
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                'Never judge a tomato by its skin, particularly when it has teeth.',
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                'The Broccolop is perfectly harmless.',
                'Auby',
                QuoteCategories::BESTIARY,
                true,
                true,
                true,
                'Do not believe the sentence above.'
            ),

            new Quote(
                'Most creatures prefer not to be catalogued while they are eating.',
                'Auby',
                QuoteCategories::BESTIARY
            ),

            new Quote(
                'Every campaign begins with a destination and immediately wanders elsewhere.',
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                'Plans are useful. Adventurers are inventive.',
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                'A good map shows where you meant to go. A good story records where you ended up.',
                'Auby',
                QuoteCategories::CAMPAIGNS
            ),

            new Quote(
                'The Guild notices courage. It also notices kindness.',
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                'Some victories deserve trumpets. Others deserve a quiet line of ink.',
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                'I have been waiting a very long time to write this entry.',
                'Auby',
                QuoteCategories::ACHIEVEMENTS
            ),

            new Quote(
                'Even enchanted Ledgers require the occasional adjustment.',
                'Auby',
                QuoteCategories::SETTINGS
            ),

            new Quote(
                'Arrange the Ledger however you please. I shall try not to move anything.',
                'Auby',
                QuoteCategories::SETTINGS
            ),

            new Quote(
                'A little organisation now prevents considerable muttering later.',
                'Auby',
                QuoteCategories::SETTINGS
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
