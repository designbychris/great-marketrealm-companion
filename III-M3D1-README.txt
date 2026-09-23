Phase III.M.3D.1 — The Dicekeeper's Touch
Apply this patch over the repository exported 2026-09-23 09:31 (III.M.3D deployed).
Changed: app/Mobile/PocketPage.php
Added: tests/Unit/Mobile/PocketDicekeeperTouchRegressionTest.php
Quick ability checks use the same local secure roller, result panel and six-roll history as manual rolls. Dice-count and modifier steppers enforce existing bounds. Natural-roll feedback respects reduced motion.
No API, database, portrait or vitality changes. Run php vendor/bin/phpunit --display-warnings and test on mobile after clearing cache.
