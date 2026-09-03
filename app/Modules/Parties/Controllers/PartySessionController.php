<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\SessionRepository;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyChronicleEntry;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use GreatMarketrealmCompanion\Modules\Parties\Services\SharedFellowshipAccess;
use RuntimeException;

final class PartySessionController
{
    public function __construct(
        private SharedFellowshipAccess $access,
        private PartyRepository $parties,
        private ViewFactory $views,
        private ResponseFactory $responses,
        private FlashStore $flash,
        private SessionRepository $sessions
    ) {}

    public function show(string $id, string $session): string
    {
        $party = $this->access->findForAccount(
            PartyId::fromString($id),
            get_current_user_id()
        );
        [$record, $notes] = $this->records($party->chronicle()->entries(), $session);
        if (! $record instanceof PartyChronicleEntry) {
            throw new RuntimeException('That Fellowship Session could not be found.');
        }

        return $this->views->render(View::make('parties.sessions.show', [
            'party' => $party,
            'record' => $record,
            'notes' => $notes,
            'authorCharacters' => $this->ownedCharacters(get_current_user_id()),
        ]));
    }

    public function addNote(string $id, string $session): RedirectResponse
    {
        $userId = get_current_user_id();
        $party = $this->access->findForAccount(PartyId::fromString($id), $userId);
        check_admin_referer(
            'gmrc_party_session_note_' . $id . '_' . $session,
            'gmrc_nonce'
        );

        $content = sanitize_textarea_field(
            wp_unslash((string) ($_POST['content'] ?? ''))
        );
        if ($content === '') {
            throw new RuntimeException('A Session note cannot be empty.');
        }

        [$record] = $this->records($party->chronicle()->entries(), $session);
        if (! $record instanceof PartyChronicleEntry) {
            throw new RuntimeException('That Fellowship Session could not be found.');
        }

        $source = $this->authorSnapshot($userId);
        $entry = $party->chronicle()->addSessionNote(
            'Session note',
            $content,
            $userId,
            $session,
            $source
        );
        $this->parties->save($party);

        $companionSessionId = $record->sourceValue('companion_session_id');
        if ($companionSessionId !== '') {
            $this->sessions->appendPlayerNoteProjection(
                $companionSessionId,
                $this->noteProjection($entry)
            );
        }

        $this->flash->success(
            'Your memory has been added to this Fellowship Session.'
        );

        return $this->responses->redirect(add_query_arg(
            'gmrc_route',
            'parties/' . $id . '/sessions/' . $session,
            home_url('/companion/')
        ));
    }

    /** @return array<string,mixed> */
    private function authorSnapshot(int $userId): array
    {
        $user = get_userdata($userId);
        $displayName = $user !== false && trim((string) $user->display_name) !== ''
            ? trim((string) $user->display_name)
            : 'Fellowship member';
        $avatar = get_avatar_url($userId, ['size' => 96]);

        $characters = $this->ownedCharacters($userId);
        $requestedId = sanitize_text_field(
            wp_unslash((string) ($_POST['character_id'] ?? ''))
        );
        $character = null;

        foreach ($characters as $candidate) {
            if ($requestedId !== '' && (string) ($candidate['id'] ?? '') === $requestedId) {
                $character = $candidate;
                break;
            }
        }
        if ($character === null && count($characters) === 1) {
            $character = $characters[0];
        }

        $token = is_array($character['token'] ?? null) ? $character['token'] : [];

        return [
            'author_display_name' => $displayName,
            'author_avatar_url' => is_string($avatar) ? $avatar : '',
            'character_id' => is_array($character) ? (string) ($character['id'] ?? '') : '',
            'character_name' => is_array($character) ? (string) ($character['name'] ?? '') : '',
            'character_portrait_url' => is_string($token['image_url'] ?? null)
                ? (string) $token['image_url']
                : '',
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function ownedCharacters(int $userId): array
    {
        $characters = apply_filters('gmrc_tabletop_owned_characters', [], $userId);
        if (! is_array($characters)) {
            return [];
        }
        return array_values(array_filter(
            $characters,
            static fn (mixed $character): bool => is_array($character)
                && trim((string) ($character['id'] ?? '')) !== ''
        ));
    }

    /** @return array<string,mixed> */
    private function noteProjection(PartyChronicleEntry $entry): array
    {
        return [
            'id' => $entry->id()->value(),
            'content' => $entry->content(),
            'recorded_at' => $entry->recordedAt()->format(DATE_ATOM),
            'author_user_id' => $entry->authorUserId(),
            'author_display_name' => $entry->sourceValue('author_display_name'),
            'author_avatar_url' => $entry->sourceValue('author_avatar_url'),
            'character_id' => $entry->sourceValue('character_id'),
            'character_name' => $entry->sourceValue('character_name'),
            'character_portrait_url' => $entry->sourceValue('character_portrait_url'),
        ];
    }

    /**
     * @param PartyChronicleEntry[] $entries
     * @return array{0:?PartyChronicleEntry,1:array<int,PartyChronicleEntry>}
     */
    private function records(array $entries, string $session): array
    {
        $record = null;
        $notes = [];
        foreach ($entries as $entry) {
            if ($entry->sourceValue('tabletop_session_id') !== $session) {
                continue;
            }
            if ($entry->sourceValue('kind') === 'tabletop-session') {
                $record = $entry;
            } elseif ($entry->sourceValue('kind') === 'tabletop-session-note') {
                $notes[] = $entry;
            }
        }
        return [$record, $notes];
    }
}
