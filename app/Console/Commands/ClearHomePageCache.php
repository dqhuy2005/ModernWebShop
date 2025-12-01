<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HomePageService;

class ClearHomePageCache extends Command
{
    protected $signature = 'cache:clear-homepage
                            {--warm : Warm up the cache after clearing}';

    protected $description = 'Clear all homepage related caches';

    public function handle(HomePageService $homePageService): int
    {
        $this->info('Clearing homepage caches...');

        $homePageService->clearHomePageCache();

        $this->info('✓ Homepage caches cleared successfully!');

        if ($this->option('warm')) {
            $this->info('Warming up caches...');
            $homePageService->warmUpCache();
            $this->info('✓ Cache warm-up completed!');
        }

        return Command::SUCCESS;
    }
}
