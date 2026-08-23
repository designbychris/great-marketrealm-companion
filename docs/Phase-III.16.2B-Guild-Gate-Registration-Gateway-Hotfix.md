# Guild Gate Registration Gateway Hotfix

Registration now uses a dedicated WordPress admin-post action (`gmrc_guild_gate_register`) for both authenticated and unauthenticated requests. The dedicated handler normalises the route to `guild-gate/register` and hands the command to the existing Companion application request gateway, preserving the existing nonce and router contracts.

When `WP_DEBUG` is enabled, safe gateway breadcrumbs are logged before controller dispatch. No passwords, email addresses, usernames, Turnstile tokens, or secrets are logged.
