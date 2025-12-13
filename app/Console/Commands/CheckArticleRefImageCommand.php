<?php

namespace App\Console\Commands;

use App\Models\BikeReference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckArticleRefImageCommand extends Command
{
    protected $signature = 'check:article-ref-image';

    protected $description = 'Command description';

    public function handle(): void
    {
        $refs = BikeReference::all();

        $refs->each(function ($ref) {
            // check if directory in storage app article contains a directory id id_article > id_reference
            $path = 'articles/'.$ref->id_article.'/'.$ref->id_reference;
            if (! Storage::disk('public')->exists($path)) {
                $this->info("Missing directory for reference ID: {$ref->id_reference} in article ID: {$ref->id_article}");
            }
        });
    }
}
