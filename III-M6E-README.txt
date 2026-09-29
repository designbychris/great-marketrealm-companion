Phase III.M.6E — The Adventurers Enter the Pocket

Overlay at the GMRC repository root.

Server:
  php vendor/bin/phpunit --display-warnings

Native workstation:
  cd native\pocket-companion
  npm run build
  npm run android:prepare

No new npm dependency is introduced in 6E.

Pixel field test:
1. Launch with the remembered Guild key; Chrome should not open.
2. Tap Open Adventurers' Register.
3. Confirm only the signed-in user's real Companion characters appear.
4. Confirm portrait, name, level/race/class and current/max HP.
5. Select a character and verify portrait, HP, AC, initiative, speed, proficiency and six ability scores against desktop GMRC.
6. Back returns to the Register; refresh re-fetches live data.
7. Return to Auby and Leave the Guild; the Register must no longer be accessible.
8. Re-authenticate and confirm the Register returns.
