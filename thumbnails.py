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
THUMB_SIZE = 400  # taille max (largeur ou hauteur)
THUMB_NAME = "thumbnail.webp"


def create_thumbnails():
    for article_dir in BASE_DIR.iterdir():
        if not article_dir.is_dir():
            continue

        first_image = None

        # On cherche la première image dispo dans les sous-dossiers (couleurs)
        for color_dir in article_dir.iterdir():
            if not color_dir.is_dir():
                continue

            for img in sorted(color_dir.iterdir()):
                if img.suffix.lower() == ".webp":
                    first_image = img
                    break

            if first_image:
                break

        if not first_image:
            continue

        thumb_path = article_dir / THUMB_NAME

        try:
            with Image.open(first_image) as img:
                img.thumbnail((THUMB_SIZE, THUMB_SIZE), Image.LANCZOS)

                img.save(
                    thumb_path,
                    format="WEBP",
                    quality=75,
                    method=6,
                )

            print(f"✔ Thumbnail created: {thumb_path}")

        except Exception as e:
            print(f"✖ Error creating thumbnail for {article_dir.name}: {e}")


if __name__ == "__main__":
    create_thumbnails()
    print("Done.")
