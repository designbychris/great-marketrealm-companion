Phase III.M.6F — The Adventurer Takes the Field

Overlay this patch at the GMRC repository root.

Server baseline entering the phase:
  3974 tests / 14904 assertions / ALL GREEN

Run:
  php vendor/bin/phpunit --display-warnings

Native workstation (no new npm dependency):
  cd native\pocket-companion
  npm run build
  npm run android:prepare

Pixel field test:
1. Launch with remembered Guild key; browser should not open.
2. Open Register and select an adventurer.
3. Confirm native bottom dock: Overview / Character / Combat / Spellbook / More.
4. Overview: edit Current HP and Temporary HP, Save HP, then verify desktop GMRC matches.
5. Apply damage larger than Temp HP; verify Temp HP is consumed first and remainder reaches Current HP.
6. Apply healing; verify Current HP never exceeds Maximum HP.
7. Verify Maximum HP has no editable control.
8. Change HP on desktop, refresh/reopen native character, and confirm latest values arrive.
9. Character tab shows the six ability scores. Combat/Spellbook/More remain explicit future-phase placeholders.
10. Desktop Fellowship Register: Auby quote badge uses Auby's face, not the aubergine emoji.
