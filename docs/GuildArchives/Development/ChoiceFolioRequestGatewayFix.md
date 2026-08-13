# Choice Folios — Application Request Gateway Fix

The first interactive Vitality Folio originally submitted directly to the
Companion shortcode URL. The route correctly returned a `RedirectResponse`,
but `FrontendServiceProvider::renderApp()` is a shortcode callback and must
return a string. PHP therefore raised a return-type fatal before the redirect
could be sent.

State-changing Companion forms already have a dedicated boundary:

`admin-post.php` → `gmrc_app_request` → `handleApplicationRequest()`

That handler validates the route nonce, dispatches the application request,
and explicitly sends `Response` objects before exiting.

The Advancement Choice form now uses the same gateway as Character Creation,
Edit, Inventory, Portrait and Experience forms. The gateway nonce resolver
also recognises `characters/{id}/progression/advance/choice` using the
`gmrc_character_advancement_{id}` nonce action.
