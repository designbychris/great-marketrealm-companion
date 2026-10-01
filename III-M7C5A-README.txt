Phase III.M.7C.5A — The Guild Knows When to Say Farewell

Adds the public, web-authoritative Great MarketRealm account deletion request flow.

Public Play-facing URL:
https://greatmarketrealm.co.uk/companion/delete-account/

What changed
- Stable WordPress rewrite for /companion/delete-account/; no manually-created WP page required.
- Public deletion charter remains readable while signed out.
- Guild Profile now links to Account & Data -> Delete Account and Data.
- Signed-in request requires the current password plus the exact phrase DELETE MY ACCOUNT.
- A verified request is recorded on the WordPress user and emailed to the site Registrar/admin and account email.
- The request does NOT blindly call wp_delete_user(). Campaign/Fellowship records may be shared with other Guild members, so final erasure must detach/anonymise shared relationships safely.
- Pocket More -> Privacy & Support now links to the same web-authoritative Delete Account & Data route. Pocket itself still does not own account deletion.
- Public wording explains owned personal data deletion, Pocket credential revocation, shared-record anonymisation/detachment, and exceptional legal/security retention.

Deployment
1. Upload/replace the plugin files from this patch/repository.
2. Visit https://greatmarketrealm.co.uk/companion/delete-account/ once. The plugin registers and one-time flushes the new rewrite rule.
3. Test signed-out public charter.
4. Sign in and test Guild Profile -> Delete Account and Data.
5. Do NOT submit a real deletion request on an account you want to keep. Use a disposable/reviewer test account if testing the POST flow.
6. Rebuild native Pocket so the More-panel link is included.

Regression command
php vendor/bin/phpunit --display-warnings

Expected baseline if only the four new regression tests are added to the supplied 4035-test baseline:
4039 tests (assertion count will be determined by PHPUnit on the deployment host).
