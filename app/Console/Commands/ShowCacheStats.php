<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HomePageService;

class ShowCacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show homepage cache statistics';

    /**
     * Execute the console command.
     */
    public function handle(HomePageService $homePageService): int
    {
        $this->info('Homepage Cache Statistics');
        $this->newLine();
        
        $stats = $homePageService->getCacheStats();
        
        $hits = 0;
        $misses = 0;
        
        $headers = ['Cache Key', 'Status'];
        $rows = [];
        
        foreach ($stats as $key => $status) {
            $rows[] = [$key, $status];
            
            if ($status === 'HIT') {
                $hits++;
            } else {
                $misses++;
            }
        }
        
        $this->table($headers, $rows);
        
        $this->newLine();
        $total = $hits + $misses;
        $hitRate = $total > 0 ? round(($hits / $total) * 100, 2) : 0;
        
        $this->info("Total Caches: {$total}");
        $this->info("Hits: {$hits}");
        $this->info("Misses: {$misses}");
        $this->info("Hit Rate: {$hitRate}%");
        
        if ($hitRate < 80) {
            $this->warn('⚠ Cache hit rate is low. Consider running: php artisan cache:warmup-homepage');
        } else {
            $this->info('✓ Cache performance is good!');
        }
        
        return Command::SUCCESS;
    }
}
