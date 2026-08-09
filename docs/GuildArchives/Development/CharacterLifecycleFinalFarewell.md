# Character Lifecycle Initiative — Phase I: The Final Farewell

Phase I completes the destructive end of the Character lifecycle.

## Flow

1. The Open Ledger view offers a clearly labelled **Delete Adventurer** action.
2. The action opens a dedicated GET confirmation page.
3. The confirmation page repeats the adventurer's name and identity.
4. Nothing is deleted until the user submits the destructive form.
5. The form uses the existing application POST gateway with `_method=DELETE`.
6. `FrontendServiceProvider` verifies a character-specific WordPress nonce.
7. The controller resolves the Character through the current-user repository
   before deletion.
8. Portrait metadata is cleared before the Character post is permanently
   removed.
9. The player is redirected to the Adventurer Register with a success notice.

## Ownership

`CharacterRepository::find()` and `delete()` resolve posts with
`author => get_current_user_id()`. The controller additionally calls
`findCharacter()` before deletion, so an unknown or another user's Character
cannot enter the destructive action through the normal route.

## Language

The Guild styling is allowed to be warm, but destructive actions remain
explicit. The final button says **Delete <character name>** and the page states
that the action is permanent.
