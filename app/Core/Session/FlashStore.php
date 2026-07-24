<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

/**
 * Flash Store.
 *
 * Stores temporary data for the following request.
 *
 * @package MarketrealmCompanion
 * @since 0.7.0
 */
class FlashStore
{
    /**
     * Session storage key.
     */
    protected const KEY = 'gmrc_flash';

    /**
     * Create the flash store.
     */
    public function __construct(
        protected SessionStore $session
    ) {
    }

    /**
     * Age the flash data at the beginning of a request.
     *
     * Previously available data is removed and newly flashed
     * data becomes available for the current request.
     */
    public function age(): void
    {
        $flash = $this->store();

        $flash['old'] = $flash['new'];
        $flash['new'] = [];

        $this->save($flash);
    }

    /**
     * Store data for the following request.
     */
    public function put(
        string $key,
        mixed $value
    ): void {
        $flash = $this->store();

        $flash['new'][$key] = $value;

        $this->save($flash);
    }

    /**
     * Determine whether flash data is available.
     */
    public function has(string $key): bool
    {
        $flash = $this->store();

        return array_key_exists(
            $key,
            $flash['old']
        );
    }

    /**
     * Retrieve flash data for the current request.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        $flash = $this->store();

        return $flash['old'][$key] ?? $default;
    }

    /**
     * Retrieve and remove flash data.
     */
    public function pull(
        string $key,
        mixed $default = null
    ): mixed {
        $value = $this->get(
            $key,
            $default
        );

        $this->forget($key);

        return $value;
    }

    /**
     * Remove a flash value.
     */
    public function forget(string $key): void
    {
        $flash = $this->store();

        unset(
            $flash['old'][$key],
            $flash['new'][$key]
        );

        $this->save($flash);
    }

    /**
     * Remove all flash data.
     */
    public function clear(): void
    {
        $this->session->forget(
            self::KEY
        );
    }

    /**
     * Retrieve the flash storage structure.
     *
     * @return array{
     *     old: array<string, mixed>,
     *     new: array<string, mixed>
     * }
     */
    protected function store(): array
    {
        $flash = $this->session->get(
            self::KEY,
            []
        );

        return [
            'old' => is_array($flash['old'] ?? null)
                ? $flash['old']
                : [],
            'new' => is_array($flash['new'] ?? null)
                ? $flash['new']
                : [],
        ];
    }

    /**
     * Persist the flash storage structure.
     *
     * @param array{
     *     old: array<string, mixed>,
     *     new: array<string, mixed>
     * } $flash
     */
    protected function save(array $flash): void
    {
        $this->session->put(
            self::KEY,
            $flash
        );
    }
}
