# Guild Hall Foundation

## Phase I

The first Guild Hall implementation introduces:

- `components.guild-hall.auby-desk`;
- `components.auby.sticky-note`;
- browser-local daypart selection;
- dedicated dashboard styling;
- four Auby Desk illustration states.

## Daypart rules

The browser local hour selects:

```text
05:00–10:59  morning
11:00–16:59  day
17:00–20:59  evening
21:00–04:59  night
```

No server timezone is required because the state is decorative and intended to
match the player sitting at the device.

## Accessibility

Time-of-day changes do not alter required controls or information.

Reduced-motion mode disables ambient desk animation and reveals sticky notes
immediately.
