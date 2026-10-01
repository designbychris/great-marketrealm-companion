Phase III.M.7C.5 — Final Release-Readiness Audit

Adds a source-controlled, secret-free native release audit.

After applying the patch:
1. php vendor/bin/phpunit --display-warnings
2. cd native/pocket-companion
3. npm run build
4. npm run android:prepare
5. npm run release:audit

The audit checks release identity, local production bundling, authoritative/offline architecture rules,
secure storage, Android target/compile SDK 36+, native callback, and absence of unexpected sensitive
permissions/cleartext enablement.

It never reads signing passwords and does not build or sign the AAB.
