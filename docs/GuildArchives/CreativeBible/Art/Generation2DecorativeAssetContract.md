# Generation 2 Decorative Asset Contract

## Regression discovered during Purple Thumbprint

The first Purple Thumbprint placement removed the legacy
`g2-auby-illuminator-mark-01` asset from the Fructan Grocer manifest because
the new HTML Seal of Approval replaced it.

The live `generation2.js` asset list still required that removed ID.

Because the browser availability check requires every listed asset to exist,
the missing decorative mark caused the complete Generation 2 portrait to be
rejected and the Character Creator fell back to Generation 1.

## Rule

Decorative assets that have been superseded must be removed from both:

- the manifest / renderer asset definitions;
- the live Generation 2 JavaScript asset map.

A missing decorative ornament must never unintentionally revert the entire
portrait benchmark to an older generation.

## Current benchmark

The Apple Fructan Grocer retains:

- the round storybook Apple body;
- painted shadow/highlight layers;
- compact leaf cap;
- painted eyelid blink;
- Grocer outfit and equipment;
- Auby finishing-touch flourish;
- HTML Auby Seal of Approval.
