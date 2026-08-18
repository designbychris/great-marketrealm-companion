# Phase III.11.3A.1 — Fellowship Bonds Test Alignment

The first III.11.3A PHPUnit run exposed two test-environment issues.

## Character Controller isolation

The Character Controller's Fellowship presenter is deliberately optional so
existing direct-construction unit tests remain backward-compatible.

The original III.11.3A implementation resolved `get_current_user_id()` before
checking whether the optional Fellowship presenter existed. The isolated
controller tests do not boot WordPress, so that global function is unavailable
there.

The controller now resolves the current user only when the Fellowship presenter
is present. It also guards the WordPress function with `function_exists()`.

Production behaviour is unchanged.

## Fellowship heading regression

The Fellowship section heading is formatted across several lines in the PHP
view. The regression incorrectly required the literal contiguous source string
`>Fellowships<`.

The test now checks the semantic heading text `Fellowships` without depending
on template whitespace.

No Character/Fellowship domain, persistence, routing, membership, or UI
behaviour changes are introduced by this correction.
