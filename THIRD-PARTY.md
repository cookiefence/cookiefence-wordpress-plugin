# Bundled dependencies

`plugin-update-checker/` is the unmodified Plugin Update Checker 5.7 distribution:

- Source: https://github.com/YahnisElsts/plugin-update-checker
- Tag: `v5.7`
- Commit: `275a96a2a18d03c34c87f35cb68673c8c49ac3b1`
- License: MIT; included in `plugin-update-checker/license.txt`.
- Upstream bundled dependencies and their license notices remain in the distribution.

The full distribution is committed so installed plugins and release builds do not
need Composer or a network dependency install. To update it, replace the directory
from a reviewed upstream release, update this record and the pinned `v5p7`
namespace in `includes/updater.php` and `tests/updater.php`, then run all plugin checks.
