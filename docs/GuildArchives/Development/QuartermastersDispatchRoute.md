# Character Lifecycle Initiative — Phase III.3.2
## The Quartermaster’s Dispatch Route

State-changing forms rendered inside the Companion shortcode must not post
directly back to the Companion page.

A direct POST causes the application router to return a `RedirectResponse`
during shortcode rendering, but the WordPress shortcode callback has a strict
`string` return type.

Inventory and custom-portrait forms now use the established WordPress request
gateway:

- form action: `admin-post.php`;
- `action=gmrc_app_request`;
- `gmrc_route=<application route>`;
- existing `_method` override where required;
- existing request-specific nonce.

`FrontendServiceProvider::handleApplicationRequest()` receives these requests,
validates the nonce, dispatches the application route, sends the
`RedirectResponse`, and exits before Elementor/shortcode rendering resumes.

This keeps `renderApp(): string` reserved for page rendering only.
