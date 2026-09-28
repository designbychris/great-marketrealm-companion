Phase III.M.5C — The Native App Bridge

Apply this patch over the current GMRC baseline (III.M.5B.1 complete).

Adds:
- app/Mobile/PocketNativeBridge.php
- authenticated GET /wp-json/gmrc-pocket/v1/bridge contract endpoint
- bridge URL in Pocket browser configuration
- native bridge architecture notes
- regression coverage

No character persistence is duplicated. No private data is approved for offline caching.
The existing WordPress cookie + REST nonce authentication remains authoritative for this phase.
