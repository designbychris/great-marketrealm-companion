# Guild Gate Registration Certification & Hotfix

The registration path is certified as a distinct public Gate command. The tabbed Gate uses explicit Cloudflare Turnstile rendering for the active form, server verification binds the token to its login or registration action, and rejected registrations return to Join the Guild with preserved safe input.

Debug-mode Gate auditing records only lifecycle stages and sanitised rejection reasons. Passwords, Turnstile response tokens, secret keys, usernames, and email addresses are never written by the audit service.

Successful registration reopens the created WordPress account, certifies the intended Companion role, records the canonical account type, establishes the WordPress authentication cookie, and returns the new Guild member to the requested Companion route.
