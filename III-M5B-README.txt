Great MarketRealm Companion — Phase III.M.5B: The Pocket Connection Guard

Apply this patch on top of the completed Phase III.M.5A installation.

Files:
- app/Mobile/PocketPage.php
- app/Mobile/PocketAppFoundation.php
- tests/Unit/Mobile/PocketConnectionGuardRegressionTest.php

Behaviour:
- Adds an aria-live connection banner to the authenticated Pocket Companion.
- Detects browser online/offline transitions.
- Pauses network-backed HP and spell-slot writes while offline; local Guild Diceworks remains usable.
- Automatically refreshes the live ledger after connectivity returns.
- Converts fetch-level connection failures into a clear guarded state.
- Adds a generated, non-cached offline launch screen for installed-PWA navigations.
- Does NOT cache character pages, REST responses, credentials, HP, spell slots, or other private gameplay data.

Validation performed before packaging:
- PHP syntax checks passed for both modified PHP files and the new regression test.
- Existing III.M.5A regression source assertions were retained.
- ZIP integrity checked after packaging.
- Full project PHPUnit suite is not bundled in the uploaded repository and must be run on the deployment host.
