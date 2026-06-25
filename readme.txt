=== CookieFence ===
Contributors: cookiefence
Tags: cookies, consent, privacy, gdpr, cookie banner
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Install the CookieFence consent banner on a WordPress site with a single site UUID.

== Description ==

CookieFence adds the CookieFence consent banner script to your WordPress site's public pages.

After activating the plugin, go to Settings > CookieFence and paste the site UUID from the CookieFence admin UI. The plugin adds the recommended preconnect hint and CookieFence script tag in the document head.

== Installation ==

1. Upload the `cookiefence` folder to `/wp-content/plugins/`, or install the plugin ZIP from the WordPress admin.
2. Activate CookieFence from the Plugins screen.
3. Go to Settings > CookieFence.
4. Paste the site UUID from the CookieFence admin UI and save changes.

== Frequently Asked Questions ==

= Where do I find the site UUID? =

Copy it from the CookieFence admin UI installation instructions for your website.

= Does the plugin output anything before a UUID is saved? =

No. CookieFence tags are only added after a valid UUID is saved.

= Can I change the CookieFence tag host? =

Yes. Define `COOKIEFENCE_TAG_BASE_URL` in `wp-config.php` before WordPress loads plugins:

`define( 'COOKIEFENCE_TAG_BASE_URL', 'http://localhost:3001' );`

The plugin uses `https://api.cookiefence.com` when no override is defined.

== Changelog ==

= 0.1.1 =

* Allow overriding the CookieFence tag base URL from `wp-config.php`.

= 0.1.0 =

* Initial release.
