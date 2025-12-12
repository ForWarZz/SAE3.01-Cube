<?php

namespace App\Console\Commands;

use App\Models\Accessory;
use App\Models\BikeReference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigratePicturesCommand extends Command
{
    protected $signature = 'migrate:pictures {--dry-run : Ne pas effectuer les modifications}';

    protected $description = 'Migre les images de articles/{id_article}/{id_couleur}/ vers articles/{id_article}/{id_reference}/';

    public function handle(): void
    {
        $this->info('Début de la migration des images...');
        $this->info('Structure: articles/{id_article}/{id_couleur}/ -> articles/{id_article}/{id_reference}/');

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Mode dry-run activé, aucune modification ne sera effectuée.');
        }

        // Migration des vélos
        $this->info("\n=== Migration des vélos ===");
        $bikeReferences = BikeReference::withoutGlobalScopes()->with(['article', 'color'])->get();
        $this->info("Trouvé {$bikeReferences->count()} références vélo à traiter.");

        $bikeMigrated = 0;
        $bikeSkipped = 0;
        $bikeErrors = 0;

        $bar = $this->output->createProgressBar($bikeReferences->count());
        $bar->start();

        foreach ($bikeReferences as $reference) {
            $result = $this->migrateBikeReference($reference, $dryRun);

            if ($result === 'migrated') {
                $bikeMigrated++;
            } elseif ($result === 'skipped') {
                $bikeSkipped++;
            } else {
                $bikeErrors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Migration des accessoires
        $this->info("\n=== Migration des accessoires ===");
        $accessories = Accessory::withoutGlobalScopes()->with('article')->whereNotNull('id_reference')->get();
        $this->info("Trouvé {$accessories->count()} accessoires à traiter.");

        $accMigrated = 0;
        $accSkipped = 0;
        $accErrors = 0;

        $bar = $this->output->createProgressBar($accessories->count());
        $bar->start();

        foreach ($accessories as $accessory) {
            $result = $this->migrateAccessory($accessory, $dryRun);

            if ($result === 'migrated') {
                $accMigrated++;
            } elseif ($result === 'skipped') {
                $accSkipped++;
            } else {
                $accErrors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Migration terminée !');
        $this->info('  Vélos:');
        $this->info("    - Migrés: $bikeMigrated");
        $this->info("    - Ignorés (pas de source): $bikeSkipped");
        $this->info("    - Erreurs: $bikeErrors");
        $this->info('  Accessoires:');
        $this->info("    - Migrés: $accMigrated");
        $this->info("    - Ignorés (pas de source): $accSkipped");
        $this->info("    - Erreurs: $accErrors");
    }

    private function migrateBikeReference(BikeReference $reference, bool $dryRun): string
    {
        $articleId = $reference->id_article;
        $colorId = $reference->id_couleur;
        $referenceId = $reference->id_reference;

        // Source: articles/{id_article}/{id_couleur}/
        // Destination: articles/{id_article}/{id_reference}/
        $sourcePath = "articles/{$articleId}/{$colorId}";
        $destinationPath = "articles/{$articleId}/{$referenceId}";

        return $this->copyImages($sourcePath, $destinationPath, $referenceId, "couleur $colorId", $dryRun);
    }

    private function migrateAccessory(Accessory $accessory, bool $dryRun): string
    {
        $articleId = $accessory->id_article;
        $referenceId = $accessory->id_reference;

        // Les accessoires ont leurs images dans le dossier "default"
        // Source: articles/{id_article}/default/
        // Destination: articles/{id_article}/{id_reference}/
        $sourcePath = "articles/{$articleId}/default";
        $destinationPath = "articles/{$articleId}/{$referenceId}";

        return $this->copyImages($sourcePath, $destinationPath, $referenceId, 'default', $dryRun);
    }

    private function copyImages(string $sourcePath, string $destinationPath, int $referenceId, string $sourceLabel, bool $dryRun): string
    {
        // Vérifier si le dossier source existe
        if (! Storage::disk('public')->exists($sourcePath)) {
            $this->line("  Skip: Source inexistante pour référence $referenceId ($sourceLabel)");

            return 'skipped';
        }

        try {
            if (! $dryRun) {
                // Créer le dossier de destination s'il n'existe pas
                if (! Storage::disk('public')->exists($destinationPath)) {
                    Storage::disk('public')->makeDirectory($destinationPath);
                }

                // Copier les fichiers images
                $files = Storage::disk('public')->files($sourcePath);
                foreach ($files as $file) {
                    $filename = basename($file);
                    Storage::disk('public')->copy($file, "{$destinationPath}/{$filename}");
                }

                // Copier le dossier 360 s'il existe
                $source360 = "{$sourcePath}/360";
                if (Storage::disk('public')->exists($source360)) {
                    $destination360 = "{$destinationPath}/360";

                    if (! Storage::disk('public')->exists($destination360)) {
                        Storage::disk('public')->makeDirectory($destination360);
                    }

                    $files360 = Storage::disk('public')->files($source360);
                    foreach ($files360 as $file) {
                        $filename = basename($file);
                        Storage::disk('public')->copy($file, "{$destination360}/{$filename}");
                    }
                }
            }

            $this->line("  OK: Référence $referenceId <- $sourceLabel");

            return 'migrated';
        } catch (\Exception $e) {
            $this->error("  Erreur référence $referenceId: ".$e->getMessage());

            return 'error';
        }
    }
}
