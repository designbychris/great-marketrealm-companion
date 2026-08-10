# Character Lifecycle Initiative — Phase III.7.2.2: The Dice of Destiny

Character registration supports two explicit ability-score methods.

- Standard Guild Array keeps the existing 15, 14, 13, 12, 10 and 8 contract.
- Dice of Destiny rolls 3d6 independently for all six ability scores.

Rolled results populate the same form fields used by Character registration.
Server-side validation accepts only 3–18 in rolled mode, while Standard mode
continues to require every Guild Array value exactly once.

The browser uses Web Crypto rejection sampling when available, provides
individual Roll 3d6 controls and Roll All Six, displays each die plus its total
and modifier, and announces results through an ARIA live region.
