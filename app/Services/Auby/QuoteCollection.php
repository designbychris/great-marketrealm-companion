<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Services\Auby;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

final class QuoteCollection implements Countable, IteratorAggregate
{
    /**
     * @var array<Quote>
     */
    private array $quotes = [];

    /**
     * @param array<Quote> $quotes
     */
    public function __construct(array $quotes = [])
    {
        foreach ($quotes as $quote) {
            $this->add($quote);
        }
    }

    public function add(Quote $quote): self
    {
        $this->quotes[] = $quote;

        return $this;
    }

    /**
     * @return array<Quote>
     */
    public function all(): array
    {
        return $this->quotes;
    }

    public function count(): int
    {
        return count($this->quotes);
    }

    public function isEmpty(): bool
    {
        return $this->quotes === [];
    }

    public function random(): ?Quote
    {
        if ($this->isEmpty()) {
            return null;
        }

        $index = array_rand($this->quotes);

        return $this->quotes[$index];
    }

    public function forCategory(string $category): self
    {
        $category = sanitize_key($category);

        return new self(
            array_values(
                array_filter(
                    $this->quotes,
                    static fn (Quote $quote): bool =>
                        $quote->category() === $category
                )
            )
        );
    }

    /**
     * @return Traversable<int, Quote>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->quotes);
    }
}
