=== CookieFence – Cookie Consent Banner ===
Contributors: cookiefence
Tags: cookies, consent, privacy, gdpr, cookie banner
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your site to CookieFence for a customizable cookie consent banner and visitor consent management. Requires a CookieFence account.

== Description ==

CookieFence adds the CookieFence consent banner script to your WordPress site's public pages.

After activating the plugin, go to Settings > CookieFence and paste the site UUID from the CookieFence admin UI. The plugin adds the recommended preconnect hint and CookieFence script tag as early in `wp_head` as WordPress allows.

A CookieFence account and configured website are required. Manage banner appearance and consent behavior in CookieFence. This plugin installs the hosted tag; optional scripts from themes, other plugins, or tag managers still need the supported CookieFence controlled-script setup.

The plugin contacts GitHub to check for updates and download published plugin releases. Public pages load the hosted CookieFence tag, which handles visitor consent according to your website configuration.

== Installation ==

1. Upload the `cookiefence` folder to `/wp-content/plugins/`, or install the plugin ZIP from the WordPress admin.
2. Activate CookieFence from the Plugins screen.
3. Activation opens Settings > CookieFence automatically. After bulk or network activation, open this page manually for the site you want to configure.
4. Paste the site UUID from the CookieFence admin UI and save changes.

== Frequently Asked Questions ==

= Where do I find the site UUID? =

Copy it from the CookieFence admin UI installation instructions for your website.

= Does the plugin output anything before a UUID is saved? =

No. CookieFence tags are only added after a valid UUID is saved.

= Can I change the CookieFence tag host? =

Yes. Define `COOKIEFENCE_TAG_BASE_URL` in `wp-config.php` before WordPress loads plugins:

`define( 'COOKIEFENCE_TAG_BASE_URL', 'https://tag.dev.cookiefence.com' );`

The plugin uses `https://tag.cookiefence.com` when no override is defined.

== Upgrade Notice ==

= 1.0.0 =
Manually install this version once to enable future updates through WordPress. Your saved site UUID and host override are preserved. If upgrading from before 0.1.3, first verify your tag is published at https://tag.cookiefence.com.

= 0.1.3 =
Verify that your site tag is published at https://tag.cookiefence.com before upgrading. Existing host overrides remain in effect. Consent submissions continue to use the API origin configured in the published tag.

== Changelog ==

= 1.0.1 =

* Open the CookieFence UUID settings page automatically after individual activation.
* Preserve the normal bulk and network activation flow.

= 1.0.0 =

* Add native WordPress update notifications using published GitHub release ZIPs.
* Automate installable ZIP packaging and GitHub Releases for version tags.
* Clarify the plugin name, description, and CookieFence account requirement.

= 0.1.3 =

* Deliver the tag and preconnect from https://tag.cookiefence.com by default.
* Preserve the saved site UUID, early head placement and explicit host overrides.

= 0.1.2 =

* Load CookieFence earlier in the public document head by using an early `wp_head` priority.

= 0.1.1 =

* Allow overriding the CookieFence tag base URL from `wp-config.php`.

= 0.1.0 =

* Initial release.
