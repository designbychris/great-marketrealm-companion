# Phase III.M.6C.1 — Windows Native Workshop Correction

The first Windows native expedition proved that Capacitor itself worked, but Node.js 24 on Windows rejected `spawnSync npx.cmd` with `EINVAL` when `prepare-android.mjs` invoked Capacitor through `npx.cmd`.

This correction invokes the project-local Capacitor CLI with the current Node executable (`process.execPath`) instead. It remains cross-platform, avoids shell-specific `.cmd` launch behaviour, and keeps the existing Android callback intent-filter installation and Capacitor sync in one deterministic preparation command.

The Guild Gate callback remains:

`uk.co.greatmarketrealm.pocket://auth/callback`

No authentication, token-storage, API, PWA, or character-data behaviour is changed by this correction.
