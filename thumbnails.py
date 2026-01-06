# import os
# from pathlib import Path
# from PIL import Image
#
# BASE_DIR = Path("storage/app/public/articles")
# THUMB_SIZE = 400
# THUMB_NAME = "thumbnail.webp"
# IMAGE_EXTS = {".webp", ".jpg", ".jpeg", ".png"}
#
#
# def create_reference_thumbnails():
#     for article_dir in BASE_DIR.iterdir():
#         if not article_dir.is_dir():
#             continue
#
#         for ref_dir in article_dir.iterdir():
#             if not ref_dir.is_dir():
#                 continue
#
#             thumb_path = ref_dir / THUMB_NAME
#             if thumb_path.exists():
#                 continue  # déjà générée
#
#             images = sorted(
#                 img for img in ref_dir.iterdir()
#                 if img.suffix.lower() in IMAGE_EXTS and img.name != THUMB_NAME
#             )
#
#             if not images:
#                 continue
#
#             source_image = images[0]
#
#             try:
#                 with Image.open(source_image) as img:
#                     img.thumbnail((THUMB_SIZE, THUMB_SIZE), Image.LANCZOS)
#
#                     img.save(
#                         thumb_path,
#                         format="WEBP",
#                         quality=75,
#                         method=6,
#                     )
#
#                 print(f"✔ Thumbnail created: {thumb_path}")
#
#             except Exception as e:
#                 print(f"✖ Error for {ref_dir}: {e}")
#
#
# if __name__ == "__main__":
#     create_reference_thumbnails()
#     print("Done.")


import os
from pathlib import Path
from PIL import Image

BASE_DIR = Path("storage/app/public/articles")
WEBP_QUALITY = 75
IMAGE_EXTS = {".jpg", ".jpeg", ".png"}


def convert_to_webp():
    for root, _, files in os.walk(BASE_DIR):
        for filename in files:
            src = Path(root) / filename

            if src.suffix.lower() not in IMAGE_EXTS:
                continue

            dst = src.with_suffix(".webp")

            try:
                with Image.open(src) as img:
                    # Normalisation des modes
                    if img.mode in ("RGBA", "P"):
                        img = img.convert("RGBA")
                        lossless = True
                    else:
                        img = img.convert("RGB")
                        lossless = False

                    img.save(
                        dst,
                        format="WEBP",
                        quality=WEBP_QUALITY,
                        lossless=lossless,
                        method=6,
                        optimize=True,
                    )

                src.unlink()  # supprime l’original
                print(f"✔ Converted: {src} → {dst}")

            except Exception as e:
                print(f"✖ Error converting {src}: {e}")


if __name__ == "__main__":
    convert_to_webp()
    print("Done.")
