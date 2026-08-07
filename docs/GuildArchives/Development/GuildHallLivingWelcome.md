# Guild Hall Initiative — Phase III: The Living Welcome

Auby's Desk now changes its complete welcome experience with the visitor's
local time of day.

Each scene owns:

- hero artwork;
- ambient effects;
- mood/activity;
- main heading;
- supporting status;
- sticky-note title;
- sticky-note message;
- tea-card message.

## Dayparts

- Dawn: **A new adventure awaits.**
- Morning: **Good morning, adventurer!**
- Afternoon: **Someone has been busy.**
- Evening: **Welcome back to the Guild Hall.**
- Night: **Burning the midnight oil?**
- Late Night: **Shhh... Auby is asleep.**

A custom `gmrc:guild-hall:daypart-changed` event is emitted whenever a scene
is applied so future Guild Hall components can react to the same daypart.
