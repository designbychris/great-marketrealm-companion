<?php

namespace GreatMarketrealmCompanion\Core\Session;

defined('ABSPATH') || exit;

/**
 * Flash Store.
 *
 * Stores temporary data for the following request.
 *
 * Flash data is separated into:
 *
 * - new: available on the following request
 * - old: available during the current request
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
     * Old form input key.
     */
    protected const OLD_INPUT_KEY = 'old_input';

    /**
     * Validation errors key.
     */
    protected const ERRORS_KEY = 'errors';

    /**
     * Success message key.
     */
    protected const SUCCESS_KEY = 'success';

    /**
     * Error message key.
     */
    protected const ERROR_KEY = 'error';

    /**
     * Create the flash store.
     */
    public function __construct(
        protected SessionStore $session
    ) {
    }

    /**
     * Age flash data at the beginning of a request.
     *
     * Existing current-request data is discarded, while data
     * flashed during the previous request becomes available.
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
     * Determine whether flash data is available
     * during the current request.
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

        return $flash['old'][$key]
            ?? $default;
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
     *
     * The value is removed from both current and
     * following-request flash storage.
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
     * Flash form input for the following request.
     *
     * @param array<string, mixed> $input
     */
    public function flashOldInput(array $input): void
    {
        $this->put(
            self::OLD_INPUT_KEY,
            $input
        );
    }

    /**
     * Retrieve old form input.
     *
     * When no key is provided, all old input is returned.
     */
    public function old(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        $input = $this->get(
            self::OLD_INPUT_KEY,
            []
        );

        if (! is_array($input)) {
            return $key === null
                ? []
                : $default;
        }

        if ($key === null) {
            return $input;
        }

        return $input[$key]
            ?? $default;
    }

    /**
     * Determine whether old form input exists.
     */
    public function hasOldInput(
        ?string $key = null
    ): bool {
        $input = $this->old();

        if (! is_array($input)) {
            return false;
        }

        if ($key === null) {
            return $input !== [];
        }

        return array_key_exists(
            $key,
            $input
        );
    }

    /**
     * Flash validation errors for the following request.
     *
     * @param array<string, mixed>|object $errors
     */
    public function flashErrors(
        array|object $errors
    ): void {
        $this->put(
            self::ERRORS_KEY,
            $errors
        );
    }

    /**
     * Retrieve validation errors.
     *
     * This may return an error array or a future
     * ValidationErrors object.
     *
     * @return array<string, mixed>|object|null
     */
    public function errors(): array|object|null
    {
        $errors = $this->get(
            self::ERRORS_KEY
        );

        return is_array($errors) || is_object($errors)
            ? $errors
            : null;
    }

    /**
     * Store or retrieve a success message.
     *
     * Supplying a message flashes it for the following request.
     * Calling the method without a message retrieves the current
     * request's success message.
     */
    public function success(
        ?string $message = null
    ): ?string {
        if ($message !== null) {
            $this->put(
                self::SUCCESS_KEY,
                $message
            );

            return $message;
        }

        $stored = $this->get(
            self::SUCCESS_KEY
        );

        return is_string($stored)
            ? $stored
            : null;
    }

    /**
     * Store or retrieve an error message.
     *
     * Supplying a message flashes it for the following request.
     * Calling the method without a message retrieves the current
     * request's error message.
     */
    public function error(
        ?string $message = null
    ): ?string {
        if ($message !== null) {
            $this->put(
                self::ERROR_KEY,
                $message
            );

            return $message;
        }

        $stored = $this->get(
            self::ERROR_KEY
        );

        return is_string($stored)
            ? $stored
            : null;
    }

    /**
     * Retrieve the normalised flash storage structure.
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

        if (! is_array($flash)) {
            $flash = [];
        }

        return [
            'old' => is_array(
                $flash['old'] ?? null
            )
                ? $flash['old']
                : [],

            'new' => is_array(
                $flash['new'] ?? null
            )
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
