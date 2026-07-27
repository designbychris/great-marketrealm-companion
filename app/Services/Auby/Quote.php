<?php

declare(strict_types=1);

namespace GMRC\Services\Auby;

final class Quote
{
    private string $text;
    private string $author;
    private string $category;
    private bool $allowCoffeeStain;
    private bool $allowInkBlot;
    private bool $allowCorrection;
    private ?string $correctionText;

    public function __construct(
        string $text,
        string $author = 'Auby',
        string $category = QuoteCategories::GENERAL,
        bool $allowCoffeeStain = true,
        bool $allowInkBlot = true,
        bool $allowCorrection = false,
        ?string $correctionText = null
    ) {
        $text = trim($text);
        $author = trim($author);
        $category = sanitize_key($category);

        if ($text === '') {
            throw new \InvalidArgumentException(
                'An Auby quote cannot be empty.'
            );
        }

        if ($author === '') {
            $author = 'Auby';
        }

        if (!QuoteCategories::isValid($category)) {
            $category = QuoteCategories::GENERAL;
        }

        $this->text = $text;
        $this->author = $author;
        $this->category = $category;
        $this->allowCoffeeStain = $allowCoffeeStain;
        $this->allowInkBlot = $allowInkBlot;
        $this->allowCorrection = $allowCorrection;
        $this->correctionText = $correctionText !== null
            ? trim($correctionText)
            : null;
    }

    public function text(): string
    {
        return $this->text;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function allowsCoffeeStain(): bool
    {
        return $this->allowCoffeeStain;
    }

    public function allowsInkBlot(): bool
    {
        return $this->allowInkBlot;
    }

    public function allowsCorrection(): bool
    {
        return $this->allowCorrection
            && $this->correctionText !== null
            && $this->correctionText !== '';
    }

    public function correctionText(): ?string
    {
        return $this->correctionText;
    }

    /**
     * Stable identifier generated from the quote's contents.
     */
    public function id(): string
    {
        return md5(
            implode('|', [
                $this->category,
                $this->author,
                $this->text,
                $this->correctionText ?? '',
            ])
        );
    }

    /**
     * Returns a deterministic number between 0 and 99.
     *
     * This means the same quote always receives the same imperfections.
     */
    private function imperfectionValue(string $type): int
    {
        $hash = hash('crc32b', $this->id() . '|' . $type);

        return (int) hexdec(substr($hash, 0, 2)) % 100;
    }

    public function hasCoffeeStain(): bool
    {
        if (!$this->allowCoffeeStain) {
            return false;
        }

        return $this->imperfectionValue('coffee') < 8;
    }

    public function hasInkBlot(): bool
    {
        if (!$this->allowInkBlot) {
            return false;
        }

        return $this->imperfectionValue('ink') < 16;
    }

    public function rotation(): int
    {
        $value = $this->imperfectionValue('rotation');

        if ($value < 33) {
            return -1;
        }

        if ($value > 66) {
            return 1;
        }

        return 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'                => $this->id(),
            'text'              => $this->text(),
            'author'            => $this->author(),
            'category'          => $this->category(),
            'coffee_stain'      => $this->hasCoffeeStain(),
            'ink_blot'          => $this->hasInkBlot(),
            'rotation'          => $this->rotation(),
            'has_correction'    => $this->allowsCorrection(),
            'correction_text'   => $this->correctionText(),
        ];
    }
}
