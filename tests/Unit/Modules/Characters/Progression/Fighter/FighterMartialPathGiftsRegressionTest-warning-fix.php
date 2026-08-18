<?php
/*
 * Phase III.12.2B warning cleanup
 *
 * Apply this one-line change in:
 * tests/Unit/Modules/Characters/Progression/Fighter/FighterMartialPathGiftsRegressionTest.php
 *
 * Replace:
 *     "as $gift",
 *
 * With:
 *     'as $gift',
 *
 * This prevents PHP from interpolating the undefined $gift variable in the
 * PHPUnit assertion. No production code or Fighter behaviour changes.
 */
