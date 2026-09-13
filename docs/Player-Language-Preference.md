# Player Language Preference

The Companion stores a personal interface locale in WordPress user meta under `gmrc_interface_locale`.

Supported values in the first multilingual milestone are:

- empty string — System / Site Default
- `en_GB` — English (UK)
- `nl_NL` — Nederlands

The preference is applied through WordPress's `determine_locale` filter before the Companion text domain is loaded. Great Marketrealm Tabletop reads the same meta key, so a shared Table may have participants using different interface languages without changing campaign state.

This preference affects interface chrome only. Character names, campaign names, notes, canonical MarketRealm names, rules text and authored lore are not rewritten by the setting.


- `de_DE` — German (Germany), tester-review pack.
