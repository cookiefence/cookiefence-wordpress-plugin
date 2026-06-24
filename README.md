# CookieFence WordPress Plugin

Install the CookieFence consent banner on a WordPress site with one setting: the CookieFence site UUID copied from the CookieFence admin UI.

## Installation

1. Download [`cookiefence-0.1.0.zip`](https://github.com/cookiefence/cookiefence-wordpress-plugin/raw/main/releases/cookiefence-0.1.0.zip).
2. In WordPress, go to `Plugins > Add Plugin > Upload Plugin`.
3. Upload the ZIP and activate CookieFence.
4. Go to `Settings > CookieFence`.
5. Paste the site UUID from CookieFence and save changes.

GitHub Releases are the preferred long-term distribution channel, but the installable ZIP is tracked in this repository for the initial public download.

When configured, the plugin adds these tags to public pages as high in the document head as WordPress allows:

```html
<link rel="preconnect" href="https://api.cookiefence.com" crossorigin>
<script id="CookieFence" src="https://api.cookiefence.com/tags/{uuid}.js"></script>
```

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
(cd dist && zip -r ../releases/cookiefence-0.1.0.zip cookiefence -x "*.DS_Store")
unzip -l releases/cookiefence-0.1.0.zip
```

## License

GPL-2.0-or-later.
