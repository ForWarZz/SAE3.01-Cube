<?php

namespace App\Console\Commands;

use App\Models\Bike;
use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\BikeSize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateAllSizes extends Command
{
    protected $signature = 'generate:bike-sizes';
    protected $description = 'Assign sizes per bike model and generate stock for all associated references.';

    public function handle()
    {
        $models = BikeModel::all();
        $allSizes = BikeSize::all();
        $stores = DB::table('magasin')->get();

        if ($models->isEmpty() || $allSizes->isEmpty()) {
            $this->error("Missing data: models or sizes.");
            return;
        }

        $this->info("Starting: {$models->count()} bike models to process.");

        DB::table('dispo_magasin')->truncate();
        DB::table('taille_dispo')->truncate();

        $bar = $this->output->createProgressBar($models->count());
        $bar->start();

        DB::transaction(function () use ($models, $allSizes, $stores, $bar) {
            foreach ($models as $model) {
                $modelSizes = $allSizes->random(rand(2, 5));

                $bikeIds = Bike::where('id_modele_velo', $model->id_modele_velo)->pluck('id_article');

                if ($bikeIds->isEmpty()) {
                    $bar->advance();
                    continue;
                }

                $allRefIds = BikeReference::whereIn('id_article', $bikeIds)
                    ->pluck('id_reference')
                    ->toArray();

                $sizeAvailabilityData = [];
                $storeStockData = [];

                foreach ($allRefIds as $refId) {
                    foreach ($modelSizes as $size) {
                        $sizeAvailabilityData[] = [
                            'id_reference' => $refId,
                            'id_taille' => $size->id_taille,
                            'dispo_en_ligne' => rand(1, 100) <= 40
                        ];

                        foreach ($stores as $store) {
                            $rand = rand(1, 100);
                            if ($rand <= 25) $status = 'En Stock';
                            elseif ($rand <= 40) $status = 'Commandable';
                            else $status = 'Indisponible';

                            $storeStockData[] = [
                                'id_reference' => $refId,
                                'id_taille' => $size->id_taille,
                                'id_magasin' => $store->id_magasin,
                                'statut' => $status
                            ];
                        }
                    }
                }

                foreach (array_chunk($sizeAvailabilityData, 500) as $chunk) {
                    DB::table('taille_dispo')->insert($chunk);
                }

                foreach (array_chunk($storeStockData, 500) as $chunk) {
                    DB::table('dispo_magasin')->insert($chunk);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Done! Model -> size consistency has been applied.");
    }
}
