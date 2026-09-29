Phase III.M.6F.1 — The Field Fits the Pocket
Incoming baseline: OK (3979 tests, 14932 assertions)

Server:
  php vendor/bin/phpunit --display-warnings

After green:
  cd native\pocket-companion
  npm run build
  npm run android:prepare
Then Play in Android Studio.

Field checks: Fellowship notes use the animated Seal parchment; native character dashboard uses more width; bottom dock remains clear; HP Save/Damage/Healing still sync.
