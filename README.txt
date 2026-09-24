Phase III.M.4B — Adventurer's Dashboard (initial app dashboard)
Apply the app/ and tests/ directories over the CURRENT GMRC plugin, preserving paths.
This patch changes only PocketPage.php and adds one PHPUnit regression test.
The existing live character controls are moved into accessible tabs, not cloned.
Test on staging first; clear caches, run php vendor/bin/phpunit --display-warnings,
then check mobile/desktop navigation, HP, quick dice, combat, equipment, and spell slots.
PHPUnit was unavailable in the supplied repository environment; full suite not run here.
