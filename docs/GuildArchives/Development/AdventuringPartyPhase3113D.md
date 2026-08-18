# Phase III.11.3D — The Adventurer & Fellowship Bridge Seal

Phase III.11.3 closes by hardening the complete bridge between individual
adventurers and their Fellowships.

## Sealed bridge surfaces

The completed bridge now includes:

- Character Ledger Fellowship memberships;
- direct links back to Fellowship Halls;
- the Adventurer's personal Purse;
- the Fellowship's shared Treasury;
- Character ↔ Fellowship coin transfers;
- Treasury transfer provenance;
- owner and membership boundaries;
- compensating persistence rollback;
- accessible transfer controls;
- stable Character Ledger rendering.

## Important baseline correction

The live 2,074-test Git export contained the Ledger repair work but not the
original III.11.3C Coin Between Companions implementation.

III.11.3D therefore restores III.11.3C onto that confirmed-green baseline and
seals the complete bridge in one package.

## Duplicate-submit protection

Every Character ↔ Fellowship transfer now carries a unique `transfer_id`.

The Fellowship Hall generates the identifier when the transfer form renders.

Treasury transfer transactions persist the identifier.

Before changing either balance, the transfer action checks whether that
identifier already exists in the Fellowship Treasury history.

If the same browser POST is submitted again, the action returns without moving
coin a second time.

Existing Treasury records remain compatible because `transfer_id` is optional
when hydrating older transactions.

## Transaction integrity

The transfer continues to use one exact canonical copper amount on both sides.

### Adventurer → Fellowship

- source Character must belong to the current user;
- Character must be a member of the Fellowship;
- personal Purse must contain the amount;
- personal Purse loses exactly the amount;
- shared Treasury gains exactly the amount.

### Fellowship → Adventurer

- Fellowship must belong to the current user;
- Character must belong to the current user;
- Character must be a Fellowship member;
- shared Treasury must contain the amount;
- shared Treasury loses exactly the amount;
- personal Purse gains exactly the amount.

## Persistence compensation

Character and Party remain separate aggregates and repositories.

If one save fails after domain mutation, the action restores both pre-transfer
snapshots and attempts compensating saves.

A successful compensation reports that both balances were restored.

A failed compensation raises the stronger permanent-record warning introduced
in III.11.3C.

## Provenance

Character transfer Treasury records retain:

- Character ID;
- direction;
- amount;
- note;
- timestamp;
- unique transfer ID.

Ordinary external Treasury deposits and withdrawals remain independent and do
not require Character provenance.

## Accessibility

The Coin Between Companions form now:

- carries an explicit explanatory description;
- distinguishes personal funds from shared funds;
- groups GP/SP/CP under a fieldset and screen-reader legend;
- retains labelled Character and direction selectors;
- preserves the dedicated nonce contract;
- returns to the Treasury tab after processing.

## Character Ledger shell

The III.11.3C.2 runtime repair remains part of the seal.

`PartyMembershipRole::label()` is protected so a Character Fellowship card
cannot terminate Ledger rendering before the Companion shell closes.

The temporary false-positive CSS layout workaround remains retired.

## Phase III.11.3 status

With III.11.3D, **The Adventurer & Fellowship Bridge** is sealed.

The next major work may return to Character class/progression completion,
bringing classes beyond Wizard up to the same systematic depth where their
rules require it.
