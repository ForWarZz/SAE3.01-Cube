import os
from pathlib import Path
from PIL import Image, ImageFilter

BASE_DIR = Path("storage/app/public/articles")
THUMB_SIZE = 200  # Taille pour les miniatures de la galerie
MAIN_THUMB_SIZE = 600  # Taille pour le thumbnail principal
THUMB_SUFFIX = "-thumbnail.webp"
MAIN_THUMB_NAME = "thumbnail.webp"
IMAGE_EXTS = {".webp", ".jpg", ".jpeg", ".png"}


def delete_all_thumbnails():
    """Supprime tous les thumbnails (principal et de galerie)"""
    for root, _, files in os.walk(BASE_DIR):
        for name in files:
            if name == MAIN_THUMB_NAME or name.endswith(THUMB_SUFFIX):
                path = Path(root) / name
                try:
                    path.unlink()
                    print(f"🗑 Deleted: {path}")
                except Exception as e:
                    print(f"✖ Failed to delete {path}: {e}")


def create_thumbnail(source_image: Path, thumb_path: Path, size: int):
    """Crée un thumbnail à partir d'une image source"""
    try:
        with Image.open(source_image) as img:
            img.thumbnail((size, size), Image.LANCZOS)

            # Sharpen léger pour compenser le resize
            img = img.filter(ImageFilter.UnsharpMask(
                radius=1.0,
                percent=120,
                threshold=3
            ))

            lossless = img.mode in ("RGBA", "LA")

            img.save(
                thumb_path,
                format="WEBP",
                quality=85,
                lossless=lossless,
                method=6,
            )

        print(f"✔ Created: {thumb_path}")
        return True

    except Exception as e:
        print(f"✖ Error creating thumbnail {thumb_path}: {e}")
        return False


def create_thumbnails_for_directory(directory: Path):
    """Crée des thumbnails pour toutes les images d'un répertoire"""
    images = sorted(
        img for img in directory.iterdir()
        if img.suffix.lower() in IMAGE_EXTS
        and img.name != MAIN_THUMB_NAME
        and not img.name.endswith(THUMB_SUFFIX)
    )

    if not images:
        return 0

    count = 0

    # Créer le thumbnail principal (première image)
    main_thumb_path = directory / MAIN_THUMB_NAME
    if create_thumbnail(images[0], main_thumb_path, MAIN_THUMB_SIZE):
        count += 1

    # Créer des thumbnails pour chaque image (pour la galerie)
    for source_image in images:
        # Nom du thumbnail: image.webp -> image-thumbnail.webp
        thumb_name = source_image.stem + THUMB_SUFFIX
        thumb_path = directory / thumb_name

        if create_thumbnail(source_image, thumb_path, THUMB_SIZE):
            count += 1

    return count


def create_reference_thumbnails():
    """Crée tous les thumbnails pour toutes les références"""
    total = 0

    for article_dir in BASE_DIR.iterdir():
        if not article_dir.is_dir():
            continue

        for ref_dir in article_dir.iterdir():
            if not ref_dir.is_dir() or ref_dir.name == "360":
                continue

            print(f"\n📁 Processing: {ref_dir}")
            count = create_thumbnails_for_directory(ref_dir)
            total += count

            # Traiter le dossier 360 s'il existe
            dir_360 = ref_dir / "360"
            if dir_360.exists() and dir_360.is_dir():
                print(f"\n📁 Processing 360: {dir_360}")
                count_360 = create_thumbnails_for_directory(dir_360)
                total += count_360

    return total


if __name__ == "__main__":
    print("🚀 Starting thumbnail generation...\n")
    delete_all_thumbnails()
    print("\n" + "="*50)
    total = create_reference_thumbnails()
    print("\n" + "="*50)
    print(f"✅ Done! Created {total} thumbnails in total.")

#
#
# import os
# from pathlib import Path
# from PIL import Image
#
# BASE_DIR = Path("storage/app/public/articles")
# WEBP_QUALITY = 75
# IMAGE_EXTS = {".jpg", ".jpeg", ".png"}
#
#
# def convert_to_webp():
#     for root, _, files in os.walk(BASE_DIR):
#         for filename in files:
#             src = Path(root) / filename
#
#             if src.suffix.lower() not in IMAGE_EXTS:
#                 continue
#
#             dst = src.with_suffix(".webp")
#
#             try:
#                 with Image.open(src) as img:
#                     # Normalisation des modes
#                     if img.mode in ("RGBA", "P"):
#                         img = img.convert("RGBA")
#                         lossless = True
#                     else:
#                         img = img.convert("RGB")
#                         lossless = False
#
#                     img.save(
#                         dst,
#                         format="WEBP",
#                         quality=WEBP_QUALITY,
#                         lossless=lossless,
#                         method=6,
#                         optimize=True,
#                     )
#
#                 src.unlink()  # supprime l’original
#                 print(f"✔ Converted: {src} → {dst}")
#
#             except Exception as e:
#                 print(f"✖ Error converting {src}: {e}")
#
#
# if __name__ == "__main__":
#     convert_to_webp()
#     print("Done.")
