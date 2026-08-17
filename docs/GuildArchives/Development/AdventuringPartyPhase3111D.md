# Phase III.11.1D — Party Routes & Controller

The Fellowship now has a complete HTTP boundary inside the Companion.

## Route family

The Party Kingdom contributes:

- `GET /parties`
- `GET /parties/create`
- `POST /parties`
- `GET /parties/{id}`
- `GET /parties/{id}/edit`
- `PUT /parties/{id}`
- `DELETE /parties/{id}`
- `POST /parties/{id}/members`
- `PUT /parties/{id}/members/{character}/role`
- `DELETE /parties/{id}/members/{character}`

## Thin controller

`PartyController` translates the HTTP boundary into III.11.1C application actions.

It does not write WordPress posts/meta directly and does not reimplement membership rules.

The signed-in WordPress user becomes the `PartyOwnerId` for every owner-scoped operation.

## Validated writes

Dedicated Form Requests protect:

- Fellowship creation;
- Fellowship rename;
- adventurer membership;
- membership role changes.

Every write request requires an authenticated Guild account.

Character membership input remains a 26-character domain identifier and role input is restricted to `leader` or `member`.

## WordPress form bridge

The existing `gmrc_app_request` admin-post bridge now recognises Party mutation nonce contracts:

- `gmrc_create_party`;
- `gmrc_party_{partyId}`;
- `gmrc_party_members_{partyId}`.

The scaffold forms use those exact nonce actions, preventing the form-verification problem previously encountered when an HTTP route was added without extending the bridge.

## Scaffold views

III.11.1D contains intentionally plain `index`, `create`, `show` and `edit` views so every route is browser-testable.

They are not the finished Fellowship experience.

Phase III.11.1E replaces this scaffold presentation with the illuminated Fellowship Register, Guild portraits, member summaries and Auby presentation.

## Navigation boundary

The Parties Kingdom still does not contribute a sidebar navigation item. That visual/navigation launch belongs with III.11.1E so users are not directed to the scaffold as though it were finished.
