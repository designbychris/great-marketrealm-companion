# Phase III.M.6G.1 — The Adventurer Rolls Their Training

Native Pocket training is now actionable through one shared Guild Diceworks.

## Contract

- Ability checks, saving throws and skills roll from the Character tab.
- The API supplies canonical ability, save and skill modifiers. Native never derives character modifiers.
- Dice use `crypto.getRandomValues()` with rejection sampling.
- Normal, Advantage and Disadvantage are supported.
- One Diceworks tray remains mounted while changing dashboard tabs and is collapsed by default.
- Recent rolls are session-memory only (six entries); no character roll data is persisted to native storage.
- Natural 20 keeps the celebratory Diceworks reaction; Natural 1 keeps the established single lonely confetti reaction/Auby line.
- The Overview measures receive the Pixel field-test spacing correction.
- No native character database and no offline character writes are introduced.
