#!/usr/bin/env python3
"""Build the same installable package locally and in GitHub Actions."""
import argparse
import re
from pathlib import Path
from zipfile import ZipFile, ZipInfo, ZIP_DEFLATED

ROOT = Path(__file__).resolve().parents[1]


def build(tag=None):
    version = re.search(r"^ \* Version: (\S+)$", (ROOT / "cookiefence.php").read_text(), re.M)[1]
    stable = re.search(r"^Stable tag: (\S+)$", (ROOT / "readme.txt").read_text(), re.M)[1]
    if not re.fullmatch(r"\d+\.\d+\.\d+", version) or stable != version:
        raise ValueError("Plugin version and readme stable tag must match a stable x.y.z version")
    if tag is not None and tag != "v" + version:
        raise ValueError(f"Release tag must be v{version}, got {tag}")
    files = [ROOT / name for name in ("cookiefence.php", "readme.txt", "LICENSE")]
    for directory in ("includes", "plugin-update-checker"):
        files.extend(p for p in (ROOT / directory).rglob("*") if p.is_file())
    required = ROOT / "plugin-update-checker/plugin-update-checker.php"
    if required not in files:
        raise ValueError("Bundled update checker is missing")
    output = ROOT / "dist/cookiefence.zip"
    output.parent.mkdir(exist_ok=True)
    with ZipFile(output, "w", ZIP_DEFLATED) as archive:
        for path in sorted(files):
            relative = path.relative_to(ROOT)
            if any(part.startswith(".") for part in relative.parts):
                continue
            info = ZipInfo("cookiefence/" + relative.as_posix(), (2026, 1, 1, 0, 0, 0))
            info.compress_type = ZIP_DEFLATED
            info.create_system = 3
            info.external_attr = 0o100644 << 16
            archive.writestr(info, path.read_bytes())
    print(f"Built {output} ({version})")
    return output


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--tag", help="Require this Git tag to match the package version")
    build(parser.parse_args().tag)
