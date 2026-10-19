# CookieFence – Cookie Consent Banner

Install the CookieFence consent banner on a WordPress site with one setting: the CookieFence site UUID copied from the CookieFence admin UI.

## Installation

1. Download [`cookiefence.zip`](https://github.com/cookiefence/cookiefence-wordpress-plugin/releases/latest/download/cookiefence.zip) from the latest published GitHub Release.
2. In WordPress, go to `Plugins > Add Plugin > Upload Plugin`.
3. Upload the ZIP and activate CookieFence.
4. Activation opens `Settings > CookieFence` automatically. After bulk or network activation, open this page manually for the site you want to configure.
5. Paste the site UUID from CookieFence and save changes.

Requires a CookieFence account and a website configured in CookieFence. Customize your banner in the CookieFence dashboard. The plugin installs the tag; optional scripts installed by themes, other plugins, or tag managers need the supported controlled-script setup.

The release download link becomes available once the first automated release is published. Historical ZIPs in `releases/` are retained for existing links; new packages are published as GitHub release assets.

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

## Upgrading to 1.0.0

Versions before 1.0.0 have no updater. Upload `cookiefence.zip` through `Plugins > Add Plugin > Upload Plugin` and choose to replace the existing installation once. Keep the plugin directory named `cookiefence`. Saved settings are preserved. Subsequent published versions appear in WordPress’s Plugins screen; users can choose Update now or enable WordPress automatic updates.

The updater uses the public GitHub API without credentials. It accepts only non-draft, non-prerelease releases with the exact `cookiefence.zip` asset and never falls back to source archives, tags, or branches. Network failures or API rate limits can delay checks; the existing installed plugin continues working.

### Upgrading from before 0.1.3

Before upgrading an existing installation, publish its tag to Cloudflare and verify that `https://tag.cookiefence.com/tags/{uuid}.js` returns the expected JavaScript. Version 0.1.3 changes both the script and preconnect default to this host; the stored UUID and script execution order are preserved. Consent events continue to use the API origin embedded in the published tag.

Upload the new ZIP in WordPress and choose to replace the installed plugin. An existing `COOKIEFENCE_TAG_BASE_URL` override still takes precedence; remove or update an old API override when deliberately switching that site to the CDN. Verify that the CDN publication is ready before installing. API-hosted JavaScript delivery is retired; do not use the API host as a tag override. Consent submissions continue to the API endpoint embedded in the published tag.

## Development and releases

Prerequisites: PHP CLI and Python 3. No Composer install is required; Plugin Update Checker 5.7 is bundled. See [THIRD-PARTY.md](THIRD-PARTY.md) for its pinned source and license.

```bash
find cookiefence.php includes plugin-update-checker tests -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/updater.php
python3 -m unittest discover -s tests -p 'test_*.py'
python3 scripts/build-release.py --tag v1.0.0
```

The uploadable package is `dist/cookiefence.zip`, containing one `cookiefence/` directory, plugin source, licenses, and the complete updater. Packaging is deterministic and excludes repository tooling and old releases. Do not upload GitHub’s automatic source-code ZIP.

To release:

1. Update `Version` in `cookiefence.php`, `Stable tag` in `readme.txt`, and the changelogs in `readme.txt` and `CHANGELOG.md` (the latter supplies the current release notes).
2. Commit and push the changes, including bundled dependencies and `.github/workflows/release.yml`.
3. Tag the commit with its exact version, for example `git tag v1.0.0`, then `git push origin v1.0.0`.
4. GitHub Actions runs syntax and updater tests on PHP 7.4 and 8.3, validates package contents and the tag/version match, builds `cookiefence.zip`, uploads it to a draft release, and publishes the completed release.
5. Download the published asset and verify installation and a subsequent update on a disposable WordPress installation before announcing availability.

GitHub Actions must be enabled for the public repository. The release job uses its built-in `GITHUB_TOKEN` with `contents: write`; no personal token or secret is embedded in the plugin. A failed publication may leave a draft release: inspect it and remove that unpublished draft before rerunning the release job. Do not replace published assets or reuse released version numbers; fix forward with a new version.

Offline tests exercise actual bundled GitHub release selection with fixture responses and ZIP structure. They do not prove a live WordPress upgrade, GitHub Actions execution, or CDN tag availability.

## License

GPL-2.0-or-later. Bundled third-party code retains its own license notices.
