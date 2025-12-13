import os
from PIL import Image
import shutil


BASE_DIR = "storage/app/public/articles"
MAX_DIMENSION = 600  # largeur ou hauteur max

def generate_thumbnails():
    for article_id in os.listdir(BASE_DIR):
        article_path = os.path.join(BASE_DIR, article_id)
        if not os.path.isdir(article_path):
            continue

        for color_id in os.listdir(article_path):
            color_path = os.path.join(article_path, color_id)
            if not os.path.isdir(color_path):
                continue

            thumb_path = os.path.join(color_path, "thumbs")
            os.makedirs(thumb_path, exist_ok=True)

            for filename in os.listdir(color_path):
                if not filename.lower().endswith((".jpg", ".jpeg", ".png")):
                    continue

                image_path = os.path.join(color_path, filename)
                thumb_file = os.path.join(thumb_path, filename)

                with Image.open(image_path) as img:
                    original_width, original_height = img.size

                    # Calcul du ratio
                    ratio = min(MAX_DIMENSION / original_width, MAX_DIMENSION / original_height, 1)
                    new_width = int(original_width * ratio)
                    new_height = int(original_height * ratio)

                    # Redimensionnement proportionnel
                    img_resized = img.resize((new_width, new_height), Image.LANCZOS)

                    # Sauvegarde optimisée
                    img_resized.save(thumb_file, optimize=True, quality=75)
                    print(f"Thumbnail saved: {thumb_file} ({new_width}x{new_height})")


def purge_article_directory_where_thumbnails():
    for article_id in os.listdir(BASE_DIR):
        article_path = os.path.join(BASE_DIR, article_id)
        if not os.path.isdir(article_path):
            continue

        for color_id in os.listdir(article_path):
            color_path = os.path.join(article_path, color_id)
            if not os.path.isdir(color_path):
                continue

            thumb_path = os.path.join(color_path, "thumbs")
            if os.path.exists(thumb_path):
                shutil.rmtree(color_path, ignore_errors=True)
                print(f"Thumbnail directory deleted: {thumb_path}")

if __name__ == "__main__":
#     generate_thumbnails()
    purge_article_directory_where_thumbnails()
