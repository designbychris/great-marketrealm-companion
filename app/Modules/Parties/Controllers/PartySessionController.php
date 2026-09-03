<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Controllers;

use GreatMarketrealmCompanion\Core\Http\RedirectResponse;
use GreatMarketrealmCompanion\Core\Http\ResponseFactory;
use GreatMarketrealmCompanion\Core\Session\FlashStore;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Modules\Parties\Models\PartyChronicleEntry;
use GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects\PartyId;
use GreatMarketrealmCompanion\Modules\Parties\Repositories\PartyRepository;
use GreatMarketrealmCompanion\Modules\Parties\Services\SharedFellowshipAccess;
use RuntimeException;

final class PartySessionController
{
    public function __construct(private SharedFellowshipAccess $access, private PartyRepository $parties, private ViewFactory $views, private ResponseFactory $responses, private FlashStore $flash) {}

    public function show(string $id, string $session): string
    {
        $party = $this->access->findForAccount(PartyId::fromString($id), get_current_user_id());
        [$record, $notes] = $this->records($party->chronicle()->entries(), $session);
        if (! $record instanceof PartyChronicleEntry) { throw new RuntimeException('That Fellowship Session could not be found.'); }
        return $this->views->render(View::make('parties.sessions.show', ['party' => $party, 'record' => $record, 'notes' => $notes]));
    }

    public function addNote(string $id, string $session): RedirectResponse
    {
        $party = $this->access->findForAccount(PartyId::fromString($id), get_current_user_id());
        check_admin_referer('gmrc_party_session_note_' . $id . '_' . $session, 'gmrc_nonce');
        $content = sanitize_textarea_field(wp_unslash((string) ($_POST['content'] ?? '')));
        if ($content === '') { throw new RuntimeException('A Session note cannot be empty.'); }
        $party->chronicle()->addSessionNote('Session note', $content, get_current_user_id(), $session);
        $this->parties->save($party);
        $this->flash->success('Your memory has been added to this Fellowship Session.');
        return $this->responses->redirect(add_query_arg('gmrc_route', 'parties/' . $id . '/sessions/' . $session, home_url('/companion/')));
    }

    /** @param PartyChronicleEntry[] $entries @return array{0:?PartyChronicleEntry,1:array<int,PartyChronicleEntry>} */
    private function records(array $entries, string $session): array
    {
        $record = null; $notes = [];
        foreach ($entries as $entry) {
            if ($entry->sourceValue('tabletop_session_id') !== $session) { continue; }
            if ($entry->sourceValue('kind') === 'tabletop-session') { $record = $entry; } else { $notes[] = $entry; }
        }
        return [$record, $notes];
    }
}
