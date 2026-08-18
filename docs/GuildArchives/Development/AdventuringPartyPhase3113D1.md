# Phase III.11.3D.1 — Bridge Seal Field Repair

Browser testing after the 2,100-test Bridge Seal exposed two field issues.

## Admin-post command dispatch

Companion forms submit through WordPress `admin-post.php`.

The endpoint previously delegated the command back through the full
`AppController`, which is designed primarily to render Companion pages.

For write requests this created an unnecessary second route-resolution layer
and could leave the browser parked on `wp-admin/admin-post.php` instead of
completing the expected POST/Redirect/GET flow.

The admin-post handler now dispatches the already validated posted route
directly through the application `Router`, using the resolved method override
and `gmrc_route`.

Write handlers must return an explicit `Response`, normally a
`RedirectResponse`.

If a command unexpectedly returns anything else, the endpoint safely redirects
to `/companion/` rather than rendering a full Companion page from the WordPress
admin-post URL.

The existing nonce contracts remain unchanged.

## Fellowship Register portrait

The Fellowship Register uses the `compact` company portrait variant.

Compact portraits now explicitly strip generated Character background and frame
layers using both CSS class and `data-portrait-layer` contracts.

The local per-adventurer canvas is also forced to remain borderless,
transparent and shadow-free.

The outer Fellowship portrait canvas remains intact; only the unwanted
individual Character frame/background is removed.

## Regression coverage

The field-repair regressions protect direct Router command dispatch, explicit
Response handling, Character Purse routes, Fellowship Treasury routes, compact
portrait frame/background suppression, and the compact borderless canvas.
