#!/usr/bin/env python3
"""Refresh partner logos as color PNG files in assets/img/partners/."""

from __future__ import annotations

import time
import urllib.request
from pathlib import Path

THEME_DIR = Path(__file__).resolve().parents[1]
OUT_DIR = THEME_DIR / "assets" / "img" / "partners"
USER_AGENT = "Mozilla/5.0 (compatible; MesscutTheme/1.0)"

DOWNLOADS: dict[str, str] = {
    "comfy.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/COMFY_logo.svg/500px-COMFY_logo.svg.png",
    "lexus.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Lexus.svg/500px-Lexus.svg.png",
    "silpo.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Silpo_outline_logo.svg/500px-Silpo_outline_logo.svg.png",
    "inzhur.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Inzhur_logo.svg/500px-Inzhur_logo.svg.png",
    "uklon.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Logo_Uklon1.png/500px-Logo_Uklon1.png",
    "sweet-tv.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Sweet.tv_logo.svg/500px-Sweet.tv_logo.svg.png",
    "pepsi.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Pepsi_2023.svg/500px-Pepsi_2023.svg.png",
    "lifecell.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Lifecell_2016_logo_-_Wordmark.svg/500px-Lifecell_2016_logo_-_Wordmark.svg.png",
    "prostor.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/PROSTOR_logo.svg/500px-PROSTOR_logo.svg.png",
    "socar.png": "https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Logo_of_SOCAR.svg/960px-Logo_of_SOCAR.svg.png",
    "lamic.png": "https://lamic.com.ua/design/lamic/images/logo.png",
    "koblevo.png": "https://koblevo.com.ua/wp-content/uploads/2021/07/logo-black.png",
    "md-fashion.png": "https://is1-ssl.mzstatic.com/image/thumb/Purple221/v4/25/86/bb/2586bb08-b30c-f07f-85e6-3d417dd8e7c6/AppIcon-0-0-1x_U007emarketing-0-11-0-85-220.png/512x512bb.jpg",
}


def fetch(url: str, dest: Path) -> bool:
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            data = response.read()
    except Exception as error:  # noqa: BLE001
        print(f"FAIL {dest.name}: {error}")
        return False

    if not data.startswith(b"\x89PNG") and not data.startswith(b"\xff\xd8"):
        print(f"FAIL {dest.name}: not a raster image")
        return False

    dest.write_bytes(data)
    print(f"OK {dest.name} ({len(data)} bytes)")
    return True


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    ok = fail = 0

    for filename, url in DOWNLOADS.items():
        time.sleep(2)
        if fetch(url, OUT_DIR / filename):
            ok += 1
        else:
            fail += 1

    print(f"Done: {ok} ok, {fail} failed")

    import subprocess

    subprocess.run(["python3", str(THEME_DIR / "scripts" / "process-partner-logos.py")], check=False)


if __name__ == "__main__":
    main()
