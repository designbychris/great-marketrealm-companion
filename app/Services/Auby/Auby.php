<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Auby;

defined('ABSPATH') || exit;

final class Auby
{
    public function __construct(
        private QuoteRepository $quotes
    ) {
    }

    public function note(
        string $category = QuoteCategories::GENERAL
    ): Quote {
        return $this->quotes->random($category);
    }

    public function for(
        string $category = QuoteCategories::GENERAL
    ): Quote {
        return $this->note($category);
    }

    /**
     * Retrieve several different notes for a category.
     *
     * @return array<int,Quote>
     */
    public function many(
        string $category,
        int $quantity = 3
    ): array {
        return $this->quotes->many(
            $category,
            $quantity
        );
    }
}
