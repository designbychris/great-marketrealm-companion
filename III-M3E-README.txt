Phase III.M.3E — Adventurer’s Training

Copy app/Mobile/PocketApi.php, app/Mobile/PocketPage.php and tests/Unit/Mobile/PocketTrainingRegressionTest.php into the matching paths of the current GMRC plugin. Back up first; stage before production.

The owner-scoped characters endpoint now includes canonical saving throw and skill modifiers and proficiency/expertise flags. Saving throws and skills are collapsible; Roll uses the existing Pocket Diceworks result and six-roll history. No database changes.

Run php vendor/bin/phpunit --display-warnings. Test on phone: saves, skills, modifiers, expertise, six-entry shared history, HP, portrait, and existing quick ability rolls. Source-level tests do not replace browser or live API testing.
