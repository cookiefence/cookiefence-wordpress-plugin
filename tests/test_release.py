import importlib.util
import unittest
from zipfile import ZipFile
from pathlib import Path

spec = importlib.util.spec_from_file_location("release", Path(__file__).resolve().parents[1] / "scripts/build-release.py")
release = importlib.util.module_from_spec(spec)
spec.loader.exec_module(release)


class ReleaseTests(unittest.TestCase):
    def test_installable_reproducible_package(self):
        output = release.build()
        first = output.read_bytes()
        with ZipFile(output) as archive:
            names = set(archive.namelist())
            for required in ("cookiefence.php", "readme.txt", "LICENSE", "includes/updater.php",
                             "plugin-update-checker/plugin-update-checker.php",
                             "plugin-update-checker/load-v5p7.php",
                             "plugin-update-checker/Puc/v5p7/Vcs/GitHubApi.php",
                             "plugin-update-checker/vendor/Parsedown.php",
                             "plugin-update-checker/license.txt"):
                self.assertIn("cookiefence/" + required, names)
            self.assertTrue(all(name.startswith("cookiefence/") for name in names))
            self.assertFalse(any("/." in name or name.endswith(".zip") or name.startswith(("cookiefence/tests/", "cookiefence/scripts/")) for name in names))
            self.assertIsNone(archive.testzip())
        self.assertEqual(first, release.build().read_bytes())

    def test_reject_mismatched_or_prerelease_tags(self):
        for tag in ("v9.9.9", "v1.0.0-rc1", "1.0.0", "v1.0"):
            with self.assertRaises(ValueError):
                release.build(tag)


if __name__ == "__main__":
    unittest.main()
