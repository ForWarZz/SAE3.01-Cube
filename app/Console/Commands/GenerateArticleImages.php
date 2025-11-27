<?php

namespace App\Console\Commands;

use App\Models\Accessory;
use App\Models\Bike;
use App\Models\BikeReference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateArticleImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:article-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder images for all articles (bikes and accessories)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');

        $allBikes = BikeReference::all();
        $accessoires = Accessory::all();

        $totalItems = $allBikes->count() + $accessoires->count();

        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        foreach ($allBikes as $item) {
            $folder = "articles/$item->id_article/$item->id_couleur";

            $this->generateFolderAndImages($disk, $folder, "Velo $item->id_article - Couleur {{$item->color->label_couleur}}");
            $bar->advance();
        }

        foreach ($accessoires as $acc) {
            $folder = "articles/$acc->id_article/default";

            $this->generateFolderAndImages($disk, $folder, "Accessoire $acc->id_article");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Finished. Generated images for {$totalItems} articles.");
    }

    private function generateFolderAndImages($disk, $folderPath, $textLabel): void
    {
        if (!$disk->exists($folderPath)) {
            $disk->makeDirectory($folderPath);
        }

        for ($i = 1; $i <= 3; $i++) {
            $filename = "$i.jpg";
            $fullPath = "$folderPath/$filename";

            $img = imagecreatetruecolor(600, 400);

            if ($i === 1) {
                $bg = imagecolorallocate($img, 220, 220, 220);
                $textInfo = "COVER (1.jpg)";
            } else {
                $bg = imagecolorallocate($img, 245, 245, 245);
                $textInfo = "VUE $i ($i.jpg)";
            }

            $textColor = imagecolorallocate($img, 50, 50, 50);
            imagefilledrectangle($img, 0, 0, 600, 400, $bg);

            imagestring($img, 5, 150, 180, $textLabel, $textColor);
            imagestring($img, 5, 150, 210, $textInfo, $textColor);

            ob_start();
            imagejpeg($img);
            $imageData = ob_get_clean();

            $disk->put($fullPath, $imageData);
            imagedestroy($img);
        }
    }
}
