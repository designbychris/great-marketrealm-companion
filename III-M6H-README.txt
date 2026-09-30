Phase III.M.6H — The Adventurer Enters the Fray

Apply over the III.M.6G.1 green baseline.

Adds:
- Native Combat tab with authoritative equipped weapon attacks.
- Shared Diceworks attack rolls with Normal / Advantage / Disadvantage.
- Damage and server-certified critical-damage formula rolls.
- Read-only native Equipment in More.
- Empty states for no equipped weapons / empty pack.
- Regression coverage for native combat/equipment boundaries.

Server check:
  php vendor/bin/phpunit --display-warnings

Windows native check:
  cd native\pocket-companion
  npm run build
  npm run android:prepare
