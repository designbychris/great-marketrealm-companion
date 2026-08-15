# Phase III.10.6.1 — The Ledger Index Moves Upfront

This polish phase moves the main Character Ledger index from beneath the book to the top of the Ledger, before the first folio and Illuminator Toolkit.

## Upfront Ledger Index

The existing seven Ledger tabs are unchanged semantically:

- Overview
- Skills & Training
- Equipment
- Attacks
- Spells & Abilities
- Progression
- Archive Notes

The same ARIA tab roles, `aria-selected`, `aria-controls`, roving tabindex, query-string restoration and Left/Right/Home/End keyboard navigation remain in place.

Only presentation and document position change. The index is now left-aligned and immediately discoverable before the Ledger content.

## Vital Measures request-gateway repair

Browser testing revealed that the manual Adventuring Measures form was using the correct nonce but the global `gmrc_app_request` gateway had no nonce-action mapping for:

`POST /characters/{id}/vital-measures`

The gateway now maps that route to:

`gmrc_character_vitals_{id}`

which matches the nonce already emitted by the Ledger form.

This allows manual Current HP / Temporary HP changes to reach `CharacterController::updateVitalMeasures()` instead of being rejected at `admin-post.php`.

## Diceworks targeting correction

The temporary untargeted Diceworks HP action introduced during III.10.6 is removed from the live UI.

Damage and healing rolls still calculate and display normally, but they no longer offer to mutate the current adventurer's HP. A future target-aware combat system must identify self, ally, NPC or hostile target before Diceworks can apply a result.

This avoids accidentally applying a player's weapon damage to their own character and leaves manual Vital Measures as the correct player-HP interface for now.
