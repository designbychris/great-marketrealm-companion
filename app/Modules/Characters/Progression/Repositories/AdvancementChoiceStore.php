<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Repositories;

use GreatMarketrealmCompanion\Core\Session\SessionStore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;

defined('ABSPATH') || exit;

final class AdvancementChoiceStore
{
    private const PREFIX = 'gmrc_advancement_choices_';

    public function __construct(
        private ?SessionStore $session = null
    ) {
        $this->session ??= new SessionStore();
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function all(
        CharacterId $characterId,
        int $targetLevel
    ): array {
        $stored = $this->session->get(
            $this->sessionKey(
                $characterId,
                $targetLevel
            ),
            []
        );

        return is_array($stored)
            ? $stored
            : [];
    }

    /**
     * @param array<int,string> $selections
     */
    public function put(
        CharacterId $characterId,
        int $targetLevel,
        string $choiceKey,
        array $selections
    ): void {
        $choices = $this->all(
            $characterId,
            $targetLevel
        );

        $choices[sanitize_key($choiceKey)] =
            array_values($selections);

        $this->session->put(
            $this->sessionKey(
                $characterId,
                $targetLevel
            ),
            $choices
        );
    }

    public function clear(
        CharacterId $characterId,
        int $targetLevel
    ): void {
        $this->session->forget(
            $this->sessionKey(
                $characterId,
                $targetLevel
            )
        );
    }

    private function sessionKey(
        CharacterId $characterId,
        int $targetLevel
    ): string {
        return self::PREFIX
            . sanitize_key($characterId->value())
            . '_'
            . $targetLevel;
    }
}
