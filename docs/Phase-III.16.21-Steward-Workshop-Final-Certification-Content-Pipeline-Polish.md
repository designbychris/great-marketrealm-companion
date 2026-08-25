# Phase III.16.21 — Steward's Workshop Final Certification & Content Pipeline Polish

This phase closes the Steward Workshop build by auditing the complete
Steward-authored content pipeline as one system.

## Certified content families

1. Monsters
2. Spells
3. Backgrounds
4. Equipment & Items
5. Callings & Paths
6. Folk
7. Heritages

The six authoring rooms remain intact. Heritages are certified as their
own content family while inheriting the lifecycle state of their parent
Folk.

## Pipeline health

For each content family the Steward's Office now compares:

- total authored records
- Draft records
- Published records
- Archived records
- records visible through the Published projection
- invalid or unknown lifecycle states

A family is Healthy when its lifecycle data is valid and its Published
projection contains exactly its Published records. This explicitly guards
against Draft or Archived content leaking into live Companion catalogues.

## Lifecycle policy

Draft content remains private to the Steward.
Published content may enter Companion catalogues.
Archived content is retired without destructive loss.
Permanent deletion remains dependency-guarded.

## Steward's Office presentation

The existing Workshop Certification card is promoted into a complete
Content Health & Certification panel with totals, a seven-family health
table, direct links to each workshop, and a final certification seal.
