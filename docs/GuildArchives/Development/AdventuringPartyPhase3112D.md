# Phase III.11.2D — Fellowship Roles & Company Offices

The Fellowship Hall now distinguishes **membership authority** from
**company duty**.

## Membership role remains unchanged

The existing Party role contract remains:

- Leader
- Member

Leadership answers who leads the Fellowship.

It is not replaced by Company Offices.

## Company Offices

Each Party membership now also carries a separate `PartyOffice`.

The initial office catalogue is:

- Quartermaster
- Chronicler
- Pathfinder
- Standard Bearer
- No Company Office

This means a Character may simultaneously be:

- Fellowship Leader + Quartermaster;
- Fellowship Leader + no office;
- Member + Chronicler;
- Member + Pathfinder;
- and so on.

Character class is completely independent from the Company Office.

## Single-holder appointments

Every assigned Company Office may have at most one holder in a Fellowship.

`No Company Office` is not an appointment and may apply to any number of
members.

An office can be vacated by assigning `none`, after which it may be appointed
to another adventurer.

## Persistence and legacy compatibility

Membership rows now persist:

- Character ID;
- membership role;
- Company Office.

Older membership rows without an `office` field hydrate as
`PartyOffice::NONE`.

No migration is required for existing Fellowships.

## Application and HTTP

`ChangePartyMemberOfficeAction` updates an office through the owner-scoped
Party application layer.

The HTTP contract is:

`PUT /parties/{id}/members/{character}/office`

It reuses the existing Fellowship-membership nonce contract.

The membership nonce matcher now recognises both `/role` and `/office`.

## Fellowship Hall

Each roster member can independently change:

- membership role;
- Company Office.

Assigned offices appear as a visual badge on the member record.

The Hall also gains a **Company Offices** summary showing assigned duties and
their current holders.

When no offices are appointed, the Hall provides a dedicated empty state.

## Future use

Company Offices are deliberately domain-level Fellowship data.

Campaign, exploration and DM systems can later consume concepts such as:

- the Quartermaster responsible for company inventory;
- the Chronicler responsible for the Fellowship record;
- the Pathfinder associated with expedition/navigation workflows;
- the Standard Bearer associated with Fellowship identity.

III.11.2D does not yet grant mechanical bonuses or permissions based on an
office.
