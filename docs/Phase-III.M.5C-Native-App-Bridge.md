# Phase III.M.5C — The Native App Bridge

Phase III.M.5C formalises the boundary between the existing Pocket Companion and a future Android/iOS shell. It does **not** introduce a second character model, native-only gameplay state, or offline character synchronisation.

## Contract

Authenticated clients can inspect `GET /wp-json/gmrc-pocket/v1/bridge`. The response advertises contract version `1.0`, the current Pocket REST endpoints, lifecycle expectations, and privacy/cache rules. The response is `private, no-store`.

The existing Pocket REST API remains authoritative. Character ownership continues to be enforced by the owner-scoped character repository. The browser/PWA continues to authenticate using the existing WordPress login session and REST nonce.

## Native-shell assumptions

The first native shell should host or communicate with the Pocket experience on the same Great MarketRealm origin so the established WordPress cookie + REST nonce authentication model can be retained during the initial native bridge. A future authentication phase may replace that transport contract, but must version the bridge rather than silently changing it.

On application resume, a shell should revalidate `/session`. When connectivity returns, it should refresh the live ledger. A cold offline launch should use the public connection guard. Character pages, REST payloads and credentials are not approved for offline caching.

## Deep links and navigation

The PWA manifest remains the canonical installed-app launch point for this phase. Native deep-link schemes/universal links are intentionally deferred until the Android/iOS package identifiers and store distribution strategy are chosen.

## Non-goals

- No offline HP/spell-slot writes.
- No background character synchronisation.
- No duplicated character persistence.
- No native credentials stored by GMRC.
- No change to desktop Companion behaviour.
