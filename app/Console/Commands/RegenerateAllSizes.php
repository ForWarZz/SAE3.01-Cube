<?php

namespace App\Console\Commands;

use App\Models\BikeReference;
use App\Models\BikeSize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateAllSizes extends Command
{
    protected $signature = 'generate:bike-sizes';
    protected $description = 'Regenerates sizes and stock for all bikes';

    public function handle(): void
    {
        $allSizes = BikeSize::all();
        $allStores = DB::table('magasin')->get();

        if ($allSizes->isEmpty() || $allStores->isEmpty()) {
            $this->error("Error: sizes and stores must exist in database.");
            return;
        }

        $references = BikeReference::all();
        $groupedReferences = $references->groupBy('id_article');

        $this->info("Starting: " . $groupedReferences->count() . " bike models found.");
        $bar = $this->output->createProgressBar($groupedReferences->count());
        $bar->start();

        DB::transaction(function () use ($groupedReferences, $allSizes, $allStores, $bar) {

            foreach ($groupedReferences as $refs) {

                $refIds = $refs->pluck('id_reference');

                DB::table('taille_dispo')->whereIn('id_reference', $refIds)->delete();
                DB::table('dispo_magasin')->whereIn('id_reference', $refIds)->delete();

                $modelSizes = $allSizes->random(rand(2, 4));

                foreach ($refs as $ref) {

                    foreach ($modelSizes as $size) {

                        DB::table('taille_dispo')->insert([
                            'id_reference' => $ref->id_reference,
                            'id_taille' => $size->id_taille,
                            'dispo_en_ligne' => (rand(1, 100) <= 80)
                        ]);

                        foreach ($allStores as $store) {

                            $rand = rand(1, 100);

                            $status =
                                $rand <= 30 ? 'En Stock' :
                                    ($rand <= 60 ? 'Commandable' : 'Indisponible');

                            DB::table('dispo_magasin')->insert([
                                'id_reference' => $ref->id_reference,
                                'id_taille' => $size->id_taille,
                                'id_magasin' => $store->id_magasin,
                                'statut' => $status
                            ]);
                        }
                    }
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! All catalog data regenerated.");
    }
}
