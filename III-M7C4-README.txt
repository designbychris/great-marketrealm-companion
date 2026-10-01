Phase III.M.7C.4 — The Registrar's Privacy Ledger
==================================================

This release-candidate patch adds two deliberately small native Pocket improvements.

7C.4A — The Ledger Pulls Fresh
- Pull down from the top of the Adventurers' Register to refresh authoritative character data.
- Pull down from the top of an open Character Ledger to refresh that adventurer from the same no-store server source.
- The open Character tab is preserved across refresh.
- Guild Diceworks mode/history are preserved across an open-character refresh.
- Offline pulls fail closed and do not queue or invent character writes.
- The existing visible Register refresh button remains available.

7C.4B — Privacy & Support
- Canonical Privacy Policy: https://greatmarketrealm.co.uk/the-pocket-companion/privacy/
- Canonical Support: https://greatmarketrealm.co.uk/support/
- Both are available before sign-in at the Guild Gate.
- Both are also available from the Character > More tab.
- External pages open through the Capacitor system-browser bridge.
- No analytics, tracking, permissions, or new data stores are introduced by this patch.

Certification
-------------
Run the sacred suite:
  php vendor/bin/phpunit --display-warnings

Then rebuild native Pocket:
  cd native/pocket-companion
  npm run build
  npm run android:prepare

Pixel field check:
1. Register: change a character on desktop, pull down at the top, confirm the card updates.
2. Open character: change authoritative data on desktop, pull down at the top, confirm the Ledger updates.
3. Open Spellbook (or another tab), pull to refresh, confirm the same tab remains selected.
4. Put the device offline and pull; confirm a friendly offline message and no local write/queued mutation.
5. Confirm the visible Register refresh button still works.
6. Confirm Privacy Policy and Support open the correct public pages from Gate and More.
