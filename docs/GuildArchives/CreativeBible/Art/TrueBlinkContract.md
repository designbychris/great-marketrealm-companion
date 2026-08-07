# True Blink Contract

Golden Apple III.3 uses two coordinated face layers during a blink.

## Open state

- open-eye layer visible;
- painted eyelid layer hidden.

## Closed state

- open-eye layer receives `is-blinking` and becomes transparent;
- painted eyelid layer receives `is-blinking` and becomes visible.

Both selectors must target the same ready portrait SVG:

```css
.gmrc-portrait-layers
[data-portrait-generation="2"]
[data-illumination-ready="true"]
```

In implementation this is a compound selector with no descendant space between
the two data attributes.

The JavaScript intentionally references both `eyes` and `eyelids` and uses the
shared `setBlinkState()` helper for normal and double blinks.
