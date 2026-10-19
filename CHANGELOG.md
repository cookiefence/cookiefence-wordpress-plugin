# CookieFence 1.0.1

- Open Settings > CookieFence automatically after individual activation so the administrator can enter the site UUID.
- Keep bulk and network activation flows unchanged. The redirect runs once for the activating administrator.
- Preserve saved site UUIDs and tag-host overrides.

## CookieFence 1.0.0

- Receive plugin update notifications in WordPress from published GitHub Releases.
- Install complete release ZIPs through WordPress, with support for WordPress's optional automatic updates.
- Clarify the plugin title, description, and CookieFence account requirement.

Existing installations need one manual ZIP upgrade to 1.0.0 to enable the updater.
The saved site UUID and explicit tag-host override are preserved. When upgrading
from before 0.1.3, verify that the website tag is published at
`https://tag.cookiefence.com/tags/{uuid}.js` before switching to the default CDN host.
