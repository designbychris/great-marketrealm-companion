Phase III.M.6H.1 — The Combat Test Reads the Weapon Correctly

Regression-only correction for III.M.6H.

- Corrects the critical-damage JavaScript assertion to match optional chaining used by production code.
- Prevents PHP from interpolating the JavaScript `${attack.label}` template expression inside the regression assertion.
- No production PHP, JavaScript, CSS, API, authentication, storage, combat, equipment, or Diceworks behaviour changes.

Apply over III.M.6H and run:
php vendor/bin/phpunit --display-warnings
