<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Session\SessionStore;

final class SessionStoreStub extends SessionStore
{
    /**
     * In-memory session storage.
     *
     * @var array<string, mixed>
     */
    private array $storage = [];

    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->storage[$key]
            ?? $default;
    }

    public function put(
        string $key,
        mixed $value
    ): void {
        $this->storage[$key] = $value;
    }

    public function forget(
        string $key
    ): void {
        unset(
            $this->storage[$key]
        );
    }

    public function flush(): void
    {
        $this->storage = [];
    }
}
