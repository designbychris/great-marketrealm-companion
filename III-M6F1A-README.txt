Phase III.M.6F.1A — The Test Reads the Map Correctly

TEST-ONLY corrective patch.

Deploy this patch over the current Great MarketRealm Companion plugin, then run:

php vendor/bin/phpunit --display-warnings

Expected correction:
- no PocketNativeAdventurersRegisterRegressionTest endpoint mismatch
- no portrait optional-chaining mismatch
- no Undefined variable $ORIGIN warning

No production behaviour is changed by this patch.
