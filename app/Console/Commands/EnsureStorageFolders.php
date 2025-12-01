<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EnsureStorageFolders extends Command
{
    protected $signature = 'storage:ensure-folders';
    protected $description = 'Ensure all required storage folders exist';

    public function handle()
    {
        $folders = config('image.paths');

        $this->info('Creating storage folders...');

        foreach ($folders as $key => $path) {
            $fullPath = "public/{$path}";

            if (!Storage::exists($fullPath)) {
                Storage::makeDirectory($fullPath);
                $this->info("✓ Created: {$fullPath}");
            } else {
                $this->line("✓ Already exists: {$fullPath}");
            }
        }

        $this->newLine();
        $this->info('All storage folders are ready!');

        return Command::SUCCESS;
    }
}
