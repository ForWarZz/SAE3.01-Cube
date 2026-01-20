<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * @property int $id_article
 * @property int $id_reference
 */
trait HasImages
{
    public function getCoverUrl(?int $referenceId = null): string
    {
        if ($referenceId) {
            $thumbnailPath = "articles/{$this->id_article}/{$referenceId}/thumbnail.webp";
        } else {
            $thumbnailPath = $this->getStorageDirectory().'thumbnail.webp';
        }

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::url($thumbnailPath);
        }

        $files = $this->getImageFiles();

        if (empty($files)) {
            return '';
        }

        return Storage::url($files[0]);
    }

    public function getImageFiles(bool $is360 = false): array
    {
        $directory = $this->getStorageDirectory();

        if ($is360) {
            $directory = rtrim($directory, '/').'/360';
        }

        $files = Storage::disk('public')->files($directory);

        return $files ?: [];
    }

    public function getStorageDirectory(): string
    {
        return "articles/{$this->id_article}/{$this->id_reference}/";
    }

    public function getImagesUrls(bool $is360 = false): array
    {
        $files = $this->getImageFiles($is360);
        $files = array_filter($files, fn ($f) => ! str_ends_with($f, 'thumbnail.webp'));

        return array_values(array_map(fn ($f) => Storage::url($f), $files));
    }

    public function getThumbnailsUrls(bool $is360 = false): array
    {
        $files = $this->getImageFiles($is360);
        $files = array_filter($files, fn ($f) => ! str_ends_with($f, 'thumbnail.webp'));
        $thumbnails = [];

        foreach ($files as $file) {
            $pathInfo = pathinfo($file);
            $thumbnailPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'-thumbnail.webp';

            if (Storage::disk('public')->exists($thumbnailPath)) {
                $thumbnails[] = Storage::url($thumbnailPath);
            } else {
                $thumbnails[] = Storage::url($file);
            }
        }

        return array_values($thumbnails);
    }

    public function getImagePathFromName(string $imageName): string
    {
        return $this->getStorageDirectory().$imageName;
    }
}
