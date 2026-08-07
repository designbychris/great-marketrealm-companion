# Living Portrait Blink Selector Fix

The living portrait's readiness state is stored directly on the main SVG:

```html
<svg
    data-portrait-generation="2"
    data-illumination-ready="true"
>
```

CSS must therefore use a compound selector:

```css
[data-portrait-generation="2"]
[WRONG: descendant]
```

versus:

```css
[data-portrait-generation="2"][data-illumination-ready="true"]
```

The latter targets the same SVG element and allows the painted eyelid
overlay to become visible during the blink.
