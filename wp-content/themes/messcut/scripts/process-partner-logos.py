#!/usr/bin/env python3
"""Extract transparent wordmarks for logos that break hero monochrome filters."""

from __future__ import annotations

from pathlib import Path

from PIL import Image, ImageOps

THEME_DIR = Path(__file__).resolve().parents[1]
OUT_DIR = THEME_DIR / "assets" / "img" / "partners"
SOURCE_DIR = OUT_DIR / "_source"


def save_logo(path: Path, image: Image.Image) -> None:
    padded = ImageOps.expand(image, border=10, fill=(0, 0, 0, 0))
    bbox = padded.getbbox()
    if bbox:
        padded = padded.crop(bbox)
    padded.save(path)
    print(f"processed {path.name} ({padded.width}x{padded.height})")


def source_path(name: str) -> Path:
    source = SOURCE_DIR / name
    if source.is_file():
        return source
    return OUT_DIR / name


def extract_light_wordmark(name: str, keep_accent: bool = False) -> None:
    image = Image.open(source_path(name)).convert("RGBA")
    output = Image.new("RGBA", image.size, (0, 0, 0, 0))
    src_px = image.load()
    out_px = output.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            red, green, blue, alpha = src_px[x, y]
            if alpha < 10:
                continue
            is_light = red > 210 and green > 210 and blue > 210
            is_accent = keep_accent and red > 200 and green > 150 and blue < 120
            if is_light or is_accent:
                out_px[x, y] = (red, green, blue, alpha)

    save_logo(OUT_DIR / name, output)


def extract_dark_wordmark(name: str) -> None:
    image = Image.open(source_path(name)).convert("RGBA")
    output = Image.new("RGBA", image.size, (0, 0, 0, 0))
    src_px = image.load()
    out_px = output.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            red, green, blue, alpha = src_px[x, y]
            if alpha < 10:
                continue
            if red < 90 and green < 90 and blue < 90:
                out_px[x, y] = (red, green, blue, alpha)

    save_logo(OUT_DIR / name, output)


def extract_colored_icon(name: str) -> None:
    """Keep saturated icon pixels (e.g. Uklon yellow pick)."""
    image = Image.open(source_path(name)).convert("RGBA")
    output = Image.new("RGBA", image.size, (0, 0, 0, 0))
    src_px = image.load()
    out_px = output.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            red, green, blue, alpha = src_px[x, y]
            if alpha < 10:
                continue
            if red > 170 and green > 130 and blue < 120:
                out_px[x, y] = (red, green, blue, alpha)

    save_logo(OUT_DIR / name, output)


def extract_red_wordmark(name: str) -> None:
    """Keep red text, drop white pill backgrounds."""
    image = Image.open(source_path(name)).convert("RGBA")
    output = Image.new("RGBA", image.size, (0, 0, 0, 0))
    src_px = image.load()
    out_px = output.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            red, green, blue, alpha = src_px[x, y]
            if alpha < 10:
                continue
            if red > 140 and green < 90 and blue < 90:
                out_px[x, y] = (red, green, blue, alpha)

    save_logo(OUT_DIR / name, output)


def extract_pepsi_wordmark() -> None:
    name = "pepsi.png"
    image = Image.open(source_path(name)).convert("RGBA")
    output = Image.new("RGBA", image.size, (0, 0, 0, 0))
    src_px = image.load()
    out_px = output.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            red, green, blue, alpha = src_px[x, y]
            if alpha < 10:
                continue
            if 0.35 * height < y < 0.62 * height and red < 60 and green < 60 and blue < 60:
                out_px[x, y] = (0, 0, 0, 255)

    save_logo(OUT_DIR / name, output)


def main() -> None:
    extract_light_wordmark("chipsters.png", keep_accent=True)
    extract_light_wordmark("comfy.png", keep_accent=True)
    extract_dark_wordmark("md-fashion.png")
    extract_pepsi_wordmark()
    extract_colored_icon("uklon.png")
    extract_red_wordmark("sweet-tv.png")


if __name__ == "__main__":
    main()
