<?php

declare(strict_types=1);

namespace GMRC\Services\Auby;

final class Auby
{
    private QuoteRepository $quotes;

    public function __construct(
        ?QuoteRepository $quotes = null
    ) {
        $this->quotes = $quotes ?? new QuoteRepository();
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
}
