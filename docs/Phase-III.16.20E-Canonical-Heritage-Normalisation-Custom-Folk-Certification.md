# Phase III.16.20E — Canonical Heritage Normalisation & Custom Folk Certification

Phase III.16.20E closes the presentation gap between canonical Player's Handbook Heritages and Steward-authored Heritages.

## Canonical Heritage normalisation

`HeritageGuidance` is the shared presentation contract. Canonical catalogue records and published Steward Heritages now pass through the same normaliser before they reach the Character Builder. The contract understands ability modifiers, skills, tools, languages, resistances, size, speed, named mechanical features and proficiency choices. Legacy trait arrays remain arrays and are never coerced to the literal text `Array`.

The Character Builder's Heritage Guidance panel now renders list-shaped Heritage traits safely, displays size and speed, presents named core traits and choice rules, and exposes the parent Folk's shared traits and profile inside the inherited section.

## Bananari canonical regression fixture

The bundled Player's Handbook catalogue now records the canonical Bananari profile used to prove the bridge: +2 Dexterity, +1 Intelligence, Medium size, 35 ft speed, Slippery Skin, Quick Peel, and Flexible Logic with its Acrobatics-or-Sleight-of-Hand proficiency choice.

The catalogue version advances to **3.7.5**, ensuring existing installations refresh the bundled snapshot instead of continuing to use a valid but older 3.7.4 option.

## Custom Folk certification

Published Steward Folk now pass a dedicated `CustomFolkCertification` gate. Heritage mechanics may additionally record a size override, speed override, named features, and explicit proficiency choices. The Workshop exposes those fields and validates their publication shape while leaving drafts flexible.

Certification does not rebalance or reinterpret authored rules. It verifies that structured records are safe and complete enough for the shared Heritage presentation contract.
