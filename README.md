# CookieFence WordPress Plugin

Install the CookieFence consent banner on a WordPress site with one setting: the CookieFence site UUID copied from the CookieFence admin UI.

## Installation

1. Download [`cookiefence-0.1.3.zip`](https://github.com/cookiefence/cookiefence-wordpress-plugin/raw/main/releases/cookiefence-0.1.3.zip).
2. In WordPress, go to `Plugins > Add Plugin > Upload Plugin`.
3. Upload the ZIP and activate CookieFence.
4. Go to `Settings > CookieFence`.
5. Paste the site UUID from CookieFence and save changes.

GitHub Releases are the preferred long-term distribution channel, but the installable ZIP is tracked in this repository for the initial public download.

When configured, the plugin adds these tags to public pages as early in `wp_head` as WordPress allows:

```html
<link rel="preconnect" href="https://tag.cookiefence.com" crossorigin>
<script id="CookieFence" src="https://tag.cookiefence.com/tags/{uuid}.js"></script>
```

To use a different CookieFence host for local, staging, or other environments, define `COOKIEFENCE_TAG_BASE_URL` in `wp-config.php` before WordPress loads plugins:

```php
define( 'COOKIEFENCE_TAG_BASE_URL', 'https://tag.dev.cookiefence.com' );
```

With that override, the plugin outputs:

```html
<link rel="preconnect" href="https://tag.dev.cookiefence.com" crossorigin>
<script id="CookieFence" src="https://tag.dev.cookiefence.com/tags/{uuid}.js"></script>
```

## Upgrading to 0.1.3

Before upgrading an existing installation, publish its tag to Cloudflare and verify that `https://tag.cookiefence.com/tags/{uuid}.js` returns the expected JavaScript. Version 0.1.3 changes both the script and preconnect default to this host; the stored UUID and script execution order are preserved. Consent events continue to use the API origin embedded in the published tag.

Upload the new ZIP in WordPress and choose to replace the installed plugin. An existing `COOKIEFENCE_TAG_BASE_URL` override still takes precedence; remove or update an old API override when deliberately switching that site to the CDN. If a site is not yet published to the CDN, keep the previous plugin or explicitly use `https://api.cookiefence.com` until publication is verified.

## Development

Run a syntax check before releasing:

```bash
php -l cookiefence.php
```

Build an uploadable plugin ZIP:

```bash
rm -rf dist
mkdir -p dist/cookiefence releases
cp cookiefence.php readme.txt LICENSE dist/cookiefence/
(cd dist && zip -r ../releases/cookiefence-0.1.3.zip cookiefence -x "*.DS_Store")
unzip -l releases/cookiefence-0.1.3.zip
```

## License

GPL-2.0-or-later.
