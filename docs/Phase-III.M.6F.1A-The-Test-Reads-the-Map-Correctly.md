# Phase III.M.6F.1A — The Test Reads the Map Correctly

Corrective regression-test-only patch for the native Adventurers' Register.

## Correction

- The native characters endpoint assertion now matches the intentional absolute `${ORIGIN}` URL used by the Capacitor client.
- The portrait assertion now matches the client's null-safe `character?.portrait` access.
- The `${ORIGIN}` JavaScript fragment is asserted from a PHP single-quoted string so PHP does not attempt to interpolate `$ORIGIN`.

## Production behaviour

No production PHP, JavaScript, CSS, API, authentication, storage, or native behaviour changes are included in this phase.
