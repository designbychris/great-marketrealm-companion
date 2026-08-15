# Character Lifecycle Initiative — Phase III.9.1
## The Living Register

Phase III.9 begins by separating **advancement paperwork** from the
adventurer's **current certified Guild record**.

The Rising Register remains responsible for experience thresholds, pending
advancement, Choice Folios and the next Guild Certification. The Living
Register answers a different question:

> What does the Guild recognise as permanently true about this adventurer
> right now?

## Living Register presenter

`LivingRegisterPresenter` is a read-only presentation boundary over the
existing Character aggregate and completed advancement archive.

It currently records:

- certified Level;
- Calling;
- certified Path of Calling;
- current proficiency bonus;
- current and maximum vitality;
- permanent Spellbook and Cantrip totals;
- permanent Gifts of the Path;
- total completed Guild Certifications;
- the most recent certification archive entry.

It creates no new persistence fields and never reads pending advancement
choices as though they were permanent Character state.

## Open Ledger relationship

The Progression spread now has two explicit responsibilities:

**Rising Register — page XI**

- experience earned;
- progress toward the next threshold;
- whether another advancement may begin;
- entry into the Advancement Ledger.

**Living Register — page XII**

- the adventurer's current certified progression state;
- permanent Calling and Path information;
- certified Path Gift and learned Arcana counts;
- certification history;
- a clearly separated preview of the next possible Guild Certification.

This makes the visual language match the domain boundary: the left page rises
toward change; the right page records what has actually survived certification.

## Safety boundary

The Living Register cannot mutate:

- Level;
- XP;
- HP;
- Calling Path;
- Spellbook;
- Path Gifts;
- pending advancement paperwork;
- certification history.

Guild Certification remains the sole normal advancement mutation boundary.

## Foundation for Phase III.9

III.9.1 establishes the stable current-state projection that later Living
Register passes can enrich without reaching into controllers or duplicating
progression rules. It also gives the future Guild Diceworks a trustworthy
certified-character surface to consume when live play systems need current
bonuses and unlocked abilities.
