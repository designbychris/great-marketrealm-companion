# Phase III.16.12 — Fellowship Seals

## Purpose

Fellowship Seals establish the account-to-account invitation path anticipated by the Phase III.16 administration programme. Market Pass remains the Campaign invitation mechanism; a Fellowship Seal is deliberately scoped to one existing Fellowship.

## Certified design

- A Fellowship custodian may issue one current, short-lived Fellowship Seal from the Fellowship Hall.
- Seals reuse the Core `InviteCodeGenerator`, including its cryptographically secure randomness and ambiguity-resistant alphabet.
- Codes are case-insensitive, expire after seven days, and may be rotated or revoked without removing existing Fellowship members.
- A registered Guild Player redeems a Seal and explicitly chooses one Character owned by their own account.
- Redemption verifies Character ownership server-side before the Character is added to the Fellowship.
- A Fellowship custodian cannot redeem their own Seal.
- Existing membership redemption is idempotent and does not create duplicate membership.
- Redeemed Characters enter as normal Fellowship members. Ownership, Company administration, offices, role management, Treasury administration, and permanent Fellowship editing remain with the Fellowship custodian unless changed through the existing custodian-only workflows.
- No username or email address needs to be exchanged between Fellowship accounts.
- Seal commands use dedicated application-gateway nonces and preserve the Phase III.16 Guild Gate / `admin-post.php` routing hotfixes.

## Relationship to Campaign Fellowships

Campaign membership and Fellowship membership remain distinct concerns. Market Pass invites a Guild account into a Campaign; Fellowship Seal invites one of a Player's adventurers into a Fellowship. Campaign Fellowship synchronisation introduced in III.16.11C continues to govern Campaign-managed membership provenance independently.
